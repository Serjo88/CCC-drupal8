<?php

/**
 * Plugin implementation of the 'Builder' formatter.
 *
 * @FieldFormatter(
 *   id = "builder_formatter",
 *   label = @Translation("Builder"),
 *   field_types = {
 *     "builder"
 *   }
 * )
 */

namespace Drupal\builder\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

class BuilderFormatter extends FormatterBase {


  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {

    $elements = array();

    foreach ($items as $delta => $item) {
      $bid = !empty($item->bid) ? $item->bid : 0;
      $elements[$delta] = array(
        '#type' => 'markup',
        '#bid' => $bid,
        '#theme' => 'builder',
        '#cache' => array(
          'max-age' => 0,
        ),
      );
    }


    return $elements;
  }
}