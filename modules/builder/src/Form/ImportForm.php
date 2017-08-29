<?php

/**
 * @file
 * contain \Drupal\builder\Form\ImportForm
 */

namespace Drupal\builder\Form;

use Drupal\builder\BuilderBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseDialogCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;


class ImportForm extends FormBase
{


    /**
     * {@inheritdoc}.
     */
    public function getFormId()
    {

        return 'builder_export_form';

    }


    /**
     * {@inheritdoc}.
     */

    public function buildForm(array $form, FormStateInterface $form_state)
    {


        $args = $this->getFormArgs($form_state);

        $form['builder-dialog-messages'] = array(
            '#markup' => '<div id="builder-dialog-messages"></div>',
        );
        $form['file'] = array(
            '#type' => 'managed_file',
            '#title' => t('Upload'),
            '#description' => t('Upload your builder that exported before. Allowed extensions: zip'),
            '#upload_location' => 'public://',
            '#upload_validators' => array(
                'file_validate_extensions' => array('zip'),
                // Pass the maximum file size in bytes
                'file_validate_size' => array(1024 * 1280 * 800),
            ),
            '#required' => TRUE,
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
            '#ajax' => array(
                'callback' => '::modal',
            ),
        );

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {


    }

    /**
     * {@inheritdoc}
     * Submit handle for adding Element
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $values = $form_state->getValues();
        $bid = $values['bid'];
        if (!empty($values['file'][0])) {
            $fid = $values['file'][0];
            $file = File::load($fid);
            $builder = new BuilderBase($bid);
            $builder->import($file);
        }


    }


    public function getFormArgs($form_state)
    {
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
    public function modal(&$form, FormStateInterface $form_state)
    {

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

        return $this->dialog($values);
    }

    protected function dialog($values = array())
    {

        $content = array(
            '#theme' => 'builder_ui',
            '#bid' => $values['bid'],
        );


        $element = isset($values['element']) ? $values['element'] : array();
        $response = new AjaxResponse();

        $content['#attached']['library'][] = 'core/drupal.dialog.ajax';
        $content['#attached']['library'][] = 'core/drupal.dialog';

        $ajax_prefix = 'builder-ui-ajax-wrapper-' . $values['bid'];

        $response->addCommand(new CloseDialogCommand('.ui-dialog-content'));

        $response->addCommand(new HtmlCommand('#' . $ajax_prefix, $content));
        // quick edit compatible.
        $response->addCommand(new InvokeCommand('.quickedit-toolbar .action-save', 'attr', array('aria-hidden', false)));

        $response->setAttachments($content['#attached']);


        return $response;
    }

}