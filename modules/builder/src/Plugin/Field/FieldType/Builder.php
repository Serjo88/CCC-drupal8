<?php

/**
 * Plugin implementation of the 'builder' field type.
 *
 * @FieldType(
 *   id = "builder",
 *   label = @Translation("Builder"),
 *   module = "builder",
 *   description = @Translation("Drag drop builder field."),
 *   default_widget = "builder_widget",
 *   default_formatter = "builder_formatter"
 * )
 */

namespace Drupal\builder\Plugin\Field\FieldType;

use Drupal\builder\BuilderBase;
use Drupal\Core\Database;
use Drupal\Core\Entity\Entity;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;


class Builder extends FieldItemBase {

  /**
   * {Inheritdoc}
   */

  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {


    $properties['bid'] = DataDefinition::create('integer')
      ->setLabel(t('Builder ID'))
      ->setDescription(t('A Builder ID referenced the Builder'));

    return $properties;

  }
  

  /**
   * {Inheritdoc}
   */

  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $columns = array(
      'bid' => array(
        'description' => 'The {builder_data}.bid being referenced in this field.',
        'type' => 'int',
        'unsigned' => TRUE,
      ),

    );

    $schema = array(
      'columns' => $columns,
      'indexes' => array(
        'bid' => array('bid'),
      ),
      'foreign keys' => array(
        'bid' => array(
          'table' => 'builder_data',
          'columns' => array('bid' => 'bid'),
        ),
      ),
    );

    return $schema;
  }

  public function isEmpty() {
    $value = $this->get('bid')->getValue();
    return $value === NULL || $value === '';
  }


  public function preSave() {

    parent::preSave();
    $user = \Drupal::currentUser();
    $bid = isset($this->values['bid']) ? $this->values['bid'] : 0;
    $entity = $this->getEntity();
    $isNewRevision = $entity->isNewRevision();

    if (!empty($entity->original) && $entity->getRevisionId() != $entity->original->getRevisionId() && $isNewRevision) {
      //dsm('revision');

      $cid = BuilderBase::getCacheId($bid);
      $config = \Drupal::config('builder.cache');
      $data = $config->get($cid);
      $new_bid = _builder_create_bid();
      if (isset($data)) {
        $builder = new BuilderBase($bid);
        $data = $builder->getElements();
        $builder->clear();
        // init new builder
        $new_builder = new BuilderBase($new_bid, $data);
        $this->values['bid'] = $new_bid;
        $this->bid = $new_bid;
      }
      else {

        $data = _builder_get_data($bid);
        $builder = new BuilderBase($bid, $data);

      }

    }

  }


  /**
   * {Inheritdoc}
   */

  public function postSave($update) {
    parent::postSave($update);

    $this->builder_save();


  }

  public function builder_save() {
    $user = \Drupal::currentUser();
    $entity = $this->getEntity();
    $field_name = $this->getFieldDefinition()->getName();
    $langcode = $this->getLangcode();
    $revision_id = $entity->getRevisionId();
    $entity_id = $entity->id();
    $bid = isset($this->values['bid']) ? $this->values['bid'] : 0;

    $cid = BuilderBase::getCacheId($bid);
    $config = \Drupal::config('builder.cache');
    $data = $config->get($cid);
    $elements = array();
    if (isset($data)) {
      $builder = new BuilderBase($bid);
      $elements = $builder->getElements();


      if (!empty($elements)) {
        foreach ($elements as $element) {
          //Allow other modules implements by HOOK_builder_element_save($bid, $delta, $element)
          \Drupal::moduleHandler()
            ->invoke($element['#module'], 'builder_element_save', array(
              $bid,
              $element['#delta'],
              $element
            ));
        }
      }
      $fields = array(
        'uid' => $user->id(),
        'entity_id' => $entity_id,
        'revision_id' => $revision_id,
        'type' => $entity->getEntityTypeId(),
        'data' => @serialize($elements),
        'status' => 1,
        'created' => REQUEST_TIME,
        'langcode' => $langcode,
      );

      $check = _builder_check_revision_id($bid, $entity);
      if (!$check) {
        // bid exist but revision id does not exist.
        db_update('builder_data')
          ->fields(
            array(
              'revision_id' => $revision_id,
            )
          )
          ->condition('bid', $bid)
          ->execute();
      }
      _builder_save_data($bid, $fields, $revision_id); // save builder data

      $builder->clear();

    }


  }

  public function delete() {
    parent::delete();
    $bid = isset($this->values['bid']) ? $this->values['bid'] : 0;
    _builder_delete($bid);

  }


  /**
   * {@inheritdoc}
   */
  public function deleteRevision() {
    parent::deleteRevision();
    $entity = $this->getEntity();

    $vid = $entity->getRevisionId();

    $langcode = $this->getLangcode();

    db_delete('builder_data')
      ->condition('type', $entity->getEntityTypeId())
      ->condition('entity_id', $entity->id())
      ->condition('revision_id', $vid)
      ->condition('langcode', $langcode)
      ->execute();
  }


}