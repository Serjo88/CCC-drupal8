<?php

/**
 * Plugin implementation of the 'builder_builder' widget.
 *
 * @FieldWidget(
 *   id = "builder_widget",
 *   label = @Translation("Builder"),
 *   field_types = {
 *     "builder"
 *   }
 * )
 */

namespace Drupal\builder\Plugin\Field\FieldWidget;

use Drupal\builder\BuilderBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;


class BuilderWidget extends WidgetBase {


  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);

    return $element;
  }


  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {


    if (isset($form['#parents'][0]) && $form['#parents'][0] == 'default_value_input' && !empty($element['#field_parents'][0] && $element['#field_parents'][0] == 'default_value_input')) {
      $element['bid'] = array(
        '#type' => 'hidden',
        '#default_value' => 0,
      );

      $element['markup'] = array(
        '#type' => 'markup',
        '#markup' => t('<em>Default value settings for Builder field is not available.</em>')
      );

      return $element;
    }
    $parent_entity = $items->getEntity();
    $entity = $parent_entity;

    $user = \Drupal::currentUser();

    if (!$user->hasPermission('use builder')) {
      return;
    }
    $langcode = $items->getLangcode();

    $builder_data = NULL;
    $field_name = $items->getName();
    $input = $form_state->getUserInput();


    if ($parent_entity->isNew()) {
      // this is Add new Entity. We will create new Bid.

      $bid = isset($input[$field_name][$delta]['bid']) ? $input[$field_name][$delta]['bid'] : FALSE;

      if (empty($bid)) {
        $bid = _builder_create_bid();
      }
      if (_builder_check($bid, $entity)) {
        $bid = _builder_create_bid();
      }


    }
    else {
      // update entity
      $bid = !empty($items[$delta]->bid) ? $items[$delta]->bid : _builder_create_bid();
      if (!_builder_check_bid($bid)) {
        $bid = _builder_create_bid();
      }
      if (_builder_check($bid) && !_builder_check_bid($bid, $langcode)) {
        // bid exist on other language. However we need copy it to new language.
        $data = _builder_get_data($bid);
        $new_bid = _builder_create_bid();
        $update = db_update('builder_data')
          ->fields(
            array(
              'entity_id' => $entity->id(),
              'created' => REQUEST_TIME,
              'status' => 0,
              'langcode' => $langcode,
              'data' => serialize($data),
              'type' => $entity->getEntityTypeId(),
              'revision_id' => $entity->getRevisionId(),
              'uid' => $user->id(),
            )
          )
          ->condition('bid', $new_bid)
          ->execute();

        $bid = $new_bid;

      }
      if (empty($input[$field_name][$delta]['bid'])) {
        // form re-submiting.
        $builder_data = BuilderBase::getData($bid);
      }


    }

    $builder = new BuilderBase($bid, $builder_data);
    $ajax_prefix = 'builder-ui-ajax-wrapper-' . $bid;

    $element['bid'] = array(
      '#type' => 'hidden',
      '#default_value' => $bid,
    );
    $element['builder'] = array(
      '#type' => 'markup',
      '#theme' => 'builder_ui',
      '#bid' => $bid,
      '#prefix' => '<div id="' . $ajax_prefix . '">',
      '#suffix' => '</div>',
    );

    return $element;
  }

}