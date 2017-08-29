<?php
namespace Drupal\gavias_blockbuilder\Form;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation;
class AddForm implements FormInterface {
   /**
   * Implements \Drupal\Core\Form\FormInterface::getFormID().
   */
   public function getFormID() {
      return 'add_form';
   }

   /**
    * Implements \Drupal\Core\Form\FormInterface::buildForm().
   */
   public function buildForm(array $form, FormStateInterface $form_state) {
      $bid = 0;
      if(\Drupal::request()->attributes->get('bid')) $bid = \Drupal::request()->attributes->get('bid');
      if (is_numeric($bid) && $bid > 0) {
        $bblock = db_select('{gavias_blockbuilder}', 'd')
            ->fields('d', array('id', 'title'))
            ->condition('id', $bid)
            ->execute()
            ->fetchAssoc();
      } else {
        $bblock = array('id' => 0, 'title' => '', 'body_class'=>'');
      }      

       $form['id'] = array(
          '#type' => 'hidden',
          '#default_value' => $bblock['id']
      );
      $form['title'] = array(
        '#type' => 'textfield',
        '#title' => 'Title',
        '#default_value' => $bblock['title']
      );
      $form['body_class'] = array(
        '#type' => 'textfield',
        '#title' => 'Class body',
        '#default_value' => isset($bblock['body_class']) ? $bblock['body_class'] : '',
        '#description_display' => 'Layout display boxed when class = "boxed", e.g body class "boxed header-absolute"'
      );
      $form['actions'] = array('#type' => 'actions');
      $form['submit'] = array(
        '#type' => 'submit',
        '#value' => 'Save'
      );
    return $form;
   }

   /**
   * Implements \Drupal\Core\Form\FormInterface::validateForm().
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
      if (isset($form['values']['title']) && $form['values']['title'] === '' ) {
         $this->setFormError('title', $form_state, $this->t('Please enter title for buider block.'));
       } 
   }

   /**
   * Implements \Drupal\Core\Form\FormInterface::submitForm().
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
      if (is_numeric($form['id']['#value']) && $form['id']['#value'] > 0) {
        $pid = db_update("gavias_blockbuilder")
          ->fields(array(
              'title' => $form['title']['#value'],
              'body_class'  => $form['body_class']['#value']
          ))
          ->condition('id', $form['id']['#value'])
          ->execute();
          \Drupal::service('plugin.manager.block')->clearCachedDefinitions();
        drupal_set_message("blockbuilder '{$form['title']['#value']}' has been update");
        $response = new \Symfony\Component\HttpFoundation\RedirectResponse(\Drupal::url('gavias_blockbuilder.admin'));
        $response->send();
      } else {
        $pid = db_insert("gavias_blockbuilder")
          ->fields(array(
              'title' => $form['title']['#value'],
              'body_class'  => $form['body_class']['#value'],
              'params' => '',
          ))
          ->execute();
          \Drupal::service('plugin.manager.block')->clearCachedDefinitions();
        drupal_set_message("blockbuilder '{$form['title']['#value']}' has been created");
        $response = new \Symfony\Component\HttpFoundation\RedirectResponse(\Drupal::url('gavias_blockbuilder.admin'));
        $response->send();
      } 
   }
}