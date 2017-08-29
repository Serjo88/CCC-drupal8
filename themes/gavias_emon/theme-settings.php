<?php
use Drupal\Core\Extension\Extension;
use Drupal\Core\Extension\ExtensionDiscovery;

use Drupal\system\Controller\ThemeController;
use Drupal\Core\Theme\ThemeManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Implementation of hook_form_system_theme_settings_alter()
 *
 * @param $form
 *   Nested array of form elements that comprise the form.
 *
 * @param $form_state
 *   A keyed array containing the current state of the form.
 */
function gavias_emon_form_system_theme_settings_alter(&$form, &$form_state) {
  $form['#attached']['library'][] = 'gavias_emon/gavias-monte-admin';  
  // Get the build info for the form
  $build_info = $form_state->getBuildInfo();
  // Get the theme name we are editing
  $theme = \Drupal::theme()->getActiveTheme()->getName();
  // Create Omega Settings Object

  $form['core'] = array(
    '#type' => 'vertical_tabs',
    '#attributes' => array('class' => array('entity-meta')),
    '#weight' => -899
  );

  $form['theme_settings']['#group'] = 'core';
  $form['logo']['#group'] = 'core';
  $form['favicon']['#group'] = 'core';

  $form['theme_settings']['#open'] = FALSE;
  $form['logo']['#open'] = FALSE;
  $form['favicon']['#open'] = FALSE;
  
  // Custom settings in Vertical Tabs container
  $form['options'] = array(
    '#type' => 'vertical_tabs',
    '#attributes' => array('class' => array('entity-meta')),
    '#weight' => -999,
    '#default_tab' => 'edit-variables',
    '#states' => array(
      'invisible' => array(
       ':input[name="force_subtheme_creation"]' => array('checked' => TRUE),
      ),
    ),
  );

  /* --------- Setting general ----------------*/
  $form['general'] = array(
    '#type' => 'details',
    '#attributes' => array(),
    '#title' => t('Gerenal options'),
    '#weight' => -999,
    '#group' => 'options',
    '#open' => FALSE,
  );

  $form['general']['header_skin'] =array(
    '#type' => 'select',
    '#title' => t('Header skin'),
    '#default_value' => theme_get_setting('header_skin'),
    '#group' => 'general',
    '#options' => array(
      'header'        => t('Default'),
      'header-2'        => t('Header version 2'),
      'header-3'        => t('Header version 3'),
     ) 
  ); 

  $form['general']['sticky_menu'] =array(
    '#type' => 'select',
    '#title' => t('Enable Sticky Menu'),
    '#default_value' => theme_get_setting('sticky_menu'),
    '#group' => 'general',
    '#options' => array(
      '0'        => t('Disable'),
      '1'        => t('Enable')
     ) 
  ); 

  $form['general']['site_layout'] = array(
    '#type' => 'select',
    '#title' => t('Body Layout'),
    '#default_value' => theme_get_setting('site_layout'),
    '#options' => array(
      'wide' => t('Wide (default)'),
      'boxed' => t('Boxed'),
    ),
  );

   $form['general']['preloader'] = array(
    '#type' => 'select',
    '#title' => t('Preloader Bar'),
    '#default_value' => theme_get_setting('preloader'),
    '#options' => array(
      '0' => t('Disable'),
      '1' => t('Enable')
    ),
  );

  /* -------- Setting customize --------------*/
  $form['customize'] = array(
    '#type' => 'details',
    '#attributes' => array('class' => array('entity-meta')),
    '#title' => t('Customize options'),
    '#weight' => -999,
    '#group' => 'options',
    '#open' => FALSE,
  );

   $form['customize']['theme_skin'] = array(
    '#type' => 'select',
    '#title' => t('Theme Skin'),
    '#default_value' => theme_get_setting('theme_skin'),
     '#group' => 'customize',
    '#options' => array(
      ''        => t('Default'),
      'green'   => t('Green'),
      'lilac'   => t('Lilac'),
      'orange'  => t('Orange'),
      'red'     => t('Red'),
      'yellow'  => t('Yellow'),
    ),
  );

    $form['customize']['enable_panel'] = array(
    '#type' => 'select',
    '#title' => t('Enable Panel'),
    '#default_value' => theme_get_setting('enable_panel'),
     '#group' => 'customize',
    '#options' => array(
      '0'        => t('Disable'),
      '1'        => t('Enable'),
    ),
  );

  // User CSS
  $form['customize']['customize_css'] = array(
    '#type' => 'textarea',
    '#title' => t('Add your own CSS'),
    '#group' => 'customize',
    '#default_value' => theme_get_setting('customize_css'),
  );
    
  $form['actions']['submit']['#value'] = t('Save');
} 




