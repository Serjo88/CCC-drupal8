<?php

namespace Drupal\builder;

class BuilderElements {

  public static function info() {
    $elements['row'] = array(
      '#type' => 'row',
      '#info' => t('Row')->render(),
    );
    $elements['column'] = array(
      '#type' => 'column',
      '#info' => t('Column')->render(),
      '#depend' => 'row',
    );

    $elements['text'] = array(
      '#type' => 'text',
      '#info' => t('Custom Text')->render(),
      '#depend' => 'column',
      '#modal' => 'dialog',
    );
    $elements['image'] = array(
      '#type' => 'image',
      '#info' => t('Image')->render(),
      '#depend' => 'column'
    );
    $elements['node'] = array(
      '#type' => 'node',
      '#info' => t('Node')->render(),
      '#depend' => 'column'
    );

    $elements['youtube'] = array(
      '#type' => 'youtube',
      '#info' => t('Youtube video')->render(),
      '#depend' => 'column'
    );
    $elements['vimeo'] = array(
      '#type' => 'vimeo',
      '#info' => t('Vimeo')->render(),
      '#depend' => 'column'
    );
    $elements['contact_form'] = array(
      '#type' => 'contact_form',
      '#info' => t('Contact form')->render(),
      '#depend' => 'column',
    );
    $elements['google_map'] = array(
      '#type' => 'google_map',
      '#info' => t('Google Map')->render(),
      '#depend' => 'column',
    );
    if (\Drupal::moduleHandler()->moduleExists('views')) {
      $elements['embed_views'] = array(
        '#type' => 'embed_views',
        '#info' => t('Embed views')->render(),
        '#depend' => 'column',
      );
    }

    $blocks = array();
    $theme = \Drupal::config('system.theme')->get('default');
    $block_entities = \Drupal::entityManager()
      ->getStorage('block')
      ->loadByProperties(array('theme' => $theme));

    
    foreach ($block_entities as $key => $entity) {
      $plugin = $entity->getPlugin();
      $plugin_id = $plugin->getPluginId();
      $blocks[$plugin_id] = $entity;
    }

    if (!empty($blocks)) {
      foreach ($blocks as $key => $block) {

        $info = t('Unnamed')->render();
        if (!empty($block->label()) && is_string($block->label())) {
          $info = $block->label();
        }
        if (is_object($block->label())) {
          if (method_exists($block->label(), 'render')) {
            $info = $block->label()->render();
          }
        }
        if ($block->access('view')) {
          $elements[$key] = array(
            '#type' => 'block',
            '#info' => $info,
            '#depend' => 'column',
            '#entity' => @serialize($block),
          );
        }

      }
    }

    return $elements;

  }
}