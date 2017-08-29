<?php

/**
 * @file
 * contain \Drupal\builder\Form\ElementAddForm
 */

namespace Drupal\builder\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseDialogCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;


class ElementAddForm extends FormBase {


  /**
   * {@inheritdoc}.
   */
  public function getFormId() {

    return 'element_add_form';

  }


  /**
   * {@inheritdoc}.
   */

  public function buildForm(array $form, FormStateInterface $form_state) {


    $args = $this->getFormArgs($form_state);
    $delta = isset($args['delta']) ? $args['delta'] : NULL;
    $parent = isset($args['parent']) ? $args['parent'] : NULL;
    $element = isset($args['element']) ? $args['element'] : array();
    $form['settings'] = array(
      '#tree' => TRUE,
    );
    if (isset($delta)) {

      $module = $element['#module'];
      $type = $element['#type'];
      //Allow other modules implements by HOOK_builder_element_configure($delta, $element)
      $element_form = \Drupal::moduleHandler()
        ->invoke($module, 'builder_element_configure', array($delta, $element));
      if (!empty($element_form)) {
        foreach ($element_form as $k => $v) {

          if ($k == '#validate' || $k == '#submit') {
            if (!empty($v[0])) {
              // we need add form submit or validates.
              $form[$k][] = $v[0];
            }
          }
          else {

            $form['settings'][$k] = $v;


          }

        }
      }

      $form['type'] = array(
        '#type' => 'value',
        '#default_value' => $element['#type'],
      );
      $form['id'] = array(
        '#type' => 'value',
        '#default_value' => isset($args['id']) ? $args['id'] : '',
      );

    }
    if (isset($parent)) {
      $form['parent'] = array(
        '#type' => 'value',
        '#default_value' => $parent,
      );
    }
    $form['element'] = array(
      '#type' => 'value',
      '#default_value' => $element,
    );
    $form['bid'] = array(
      '#type' => 'value',
      '#default_value' => $args['bid'],
    );
    $form['actions'] = array(
      '#type' => 'action',
    );
    $form['actions']['submit'] = array(
      '#value' => t('Submit'),
      '#type' => 'submit',
      '#attributes' => array('class' => array('builder-element-submit-button')),
      '#ajax' => array(
        'callback' => '::modal',
      ),
    );

    // Allow other modules alter form data by HOOK_builder_element_configure_alter($form, $type, $element)
    \Drupal::service('module_handler')
      ->alter('builder_element_configure', $form, $delta, $element);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    $values = $form_state->getValues();
    $element = isset($values['element']) ? $values['element'] : array();

  }

  /**
   * {@inheritdoc}
   * Submit handle for adding Element
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $this->save_element($form_state);


  }

  public function save_element(FormStateInterface $form_state) {
    $values = $form_state->getValues();

    $element = $values['element'];

    $element['#type'] = $values['type'];
    if (!empty($values['parent'])) {
      $element['#parent'] = $values['parent'];
    }

    if (!empty($values['id'])) {
      // update element
      $element['#id'] = $values['id'];


    }

    $element['#settings'] = isset($values['settings']) ? $values['settings'] : NULL;

    $bid = $values['bid'];

    builder_save_element($element, $bid);
  }

  public function getFormArgs($form_state) {
    $args = array();

    $build_info = $form_state->getBuildInfo();
    if (!empty($build_info['args'])) {
      $args = array_shift($build_info['args']);

    }

    return $args;
  }


  /**
   * AJAX callback handler for Add Element Form.
   */
  public function modal(&$form, FormStateInterface $form_state) {

    $values = $form_state->getValues();

    $errors = $form_state->getErrors();
    if (!empty($errors)) {
      $form_state->clearErrors();
      drupal_get_messages('error'); // clear next message session;
      $content = '<div class="messages messages--error" aria-label="Error message" role="contentinfo"><div role="alert"><ul>';
      foreach ($errors as $name => $error) {
        // $form_state->setErrorByName($name, $error);
        $response = new AjaxResponse();
        $content .= "<li>$error</li>";
      }
      $content .= '</ul></div></div>';
      $data = array(
        '#markup' => $content,
      );
      $data['#attached']['library'][] = 'core/drupal.dialog.ajax';
      $data['#attached']['library'][] = 'core/drupal.dialog';
      $response->addCommand(new HtmlCommand('#builder-dialog-messages', $content));
      return $response;

    }

    return $this->dialog($values, $form_state);
  }

  protected function dialog($values = array(), $form_state) {

    $content = array(
      '#theme' => 'builder_ui',
      '#bid' => $values['bid'],
    );


    $element = isset($values['element']) ? $values['element'] : array();
    $response = new AjaxResponse();

    $content['#attached']['library'][] = 'core/drupal.dialog.ajax';
    $content['#attached']['library'][] = 'core/drupal.dialog';

    $ajax_prefix = 'builder-ui-ajax-wrapper-' . $values['bid'];

    // check ckeditor if source is enable, we need update it then close modal
    //$response->addCommand(new InvokeCommand('.cke_button__source.cke_button_on', 'click'));

    // $response->addCommand(new EditorDialogSave($form_state->getValues()));

    $response->addCommand(new CloseDialogCommand('.ui-dialog-content'));

    $response->addCommand(new HtmlCommand('#' . $ajax_prefix, $content));


    // quick edit compatible.
    $response->addCommand(new InvokeCommand('.quickedit-toolbar .action-save', 'attr', array(
      'aria-hidden',
      FALSE
    )));

    $response->setAttachments($content['#attached']);


    return $response;
  }

}
