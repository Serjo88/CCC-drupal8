<?php

/**
 * Implements of hook_builder_element_info()
 */

function HOOK_builder_element_info()
{

    $elements['youtube'] = array(
        '#type' => 'text',
        '#info' => t('Text')->render(),
        '#depend' => 'column',
        '#modal' => 'dialog', // dialog or modal
    );


    return $elements;
}

/***
 * Implements of hook_builder_element_configure($type, $element)
 * @param $delta
 * @param $element
 */
function HOOK_builder_element_configure($delta, $element)
{
    $form = array();
    $settings = isset($element['#settings']) ? $element['#settings'] : array();
    if ($delta == 'text') {
        $values = isset($element['#settings']) ? $element['#settings'] : array();

        $form['vertical_tabs'] = array(
            '#type' => 'vertical_tabs',
            '#parents' => ['vertical_tabs'],
            '#attached' => array(
                'library' => array('builder/builder-vertical-tabs', 'builder/builder.drupal.dialog'),
            ),

        );
        $form['general'] = array(
            '#type' => 'details',
            '#title' => t('General')->render(),
            '#group' => 'vertical_tabs',

        );

        $form['general']['attributes'] = array(
            '#type' => 'details',
            '#open' => FALSE,
            '#title' => t('Attributes settings')->render(),
        );
        $form['general']['attributes']['id'] = array(
            '#type' => 'textfield',
            '#title' => t('ID')->render(),
            '#default_value' => isset($values['general']['attributes']['id']) ? $values['general']['attributes']['id'] : '',
            '#description' => t('Enter html ID for element wrapper.')->render(),
        );
        $form['general']['attributes']['class'] = array(
            '#type' => 'textfield',
            '#title' => t('Extra class name')->render(),
            '#default_value' => isset($values['general']['attributes']['class']) ? $values['general']['attributes']['class'] : '',
            '#description' => t('Enter your custom css class name, this will use to your custom css.')->render()
        );

        $form['general']['text'] = array(
            '#type' => 'text_format',
            '#default_value' => isset($settings['general']['text']['value']) ? $settings['general']['text']['value'] : '',
            '#format' => isset($settings['general']['text']['format']) ? $settings['general']['text']['format'] : filter_default_format(),
        );
        $form['design'] = array(
            '#type' => 'details',
            '#title' => t('Design')->render(),
            '#group' => 'vertical_tabs',
            '#tree' => TRUE,

        );

        $form['design']['css_box'] = array(
            '#type' => 'css_box',
            '#options' => !empty($values['design']['css_box']) ? $values['design']['css_box'] : array(),
        );

    }

    return $form;

}

/**
 * Implements of hook_builder_element_configure_alter($form, $type, $element)
 */

function HOOK_builder_element_configure_alter(&$form, $delta, $element)
{

    $form['title']['#type'] = 'textarea';
}

/**
 * Implements of hook__builder_element_view($type, $element)
 */

function HOOK_builder_element_view($delta, $element)
{

    if ($delta == 'text') {

        $element['#attributes']['class'][] = 'builder-element-no-children';
        $element['#children'] = isset($settings['general']['text']['value']) ? check_markup($settings['general']['text']['value'], $settings['general']['text']['format']) : '';

    }

    return $element;
}

/**
 * hook_builder_render_alter($render, $bid)
 */
function HOOK_builder_render_alter(&$render, $bid)
{


    return $render;
}

/**
 * run this when builder save and all elements saved to database.
 *
 * HOOK_builder_element_save($bid, $delta, $element)
 */

function HOOK_builder_element_save($bid, $delta, $element)
{


}

/**
 * run this when builder save and all elements deleted from database.
 *
 * HOOK_builder_element_delete($bid, $delta, $element)
 */

function HOOK_builder_element_delete($bid, $delta, $element)
{


}

/**
 * hook_builder_element_export_alter($zip, $element)
 */
function HOOK_builder_element_export_alter(&$zip, &$element)
{
    $files = array();
    $j = 1;
    if ($element['#module'] == 'builder' && $element['#delta'] == 'image' && $element['#type'] == 'image') {
        $settings = isset($element['#settings']) ? $element['#settings'] : NULL;
        if (!empty($settings['general']['image'])) {
            $fid = $settings['general']['image'];
            for ($i = 0; $i < $zip->numFiles; $i++) {
                if ($name = $zip->getNameIndex($i)) {
                    $files[] = $name;
                }
            }
            if ($file = File::load($fid[0])) {
                $filename = $file->getFilename();
                if (!empty($files) && in_array($filename, $files)) {
                    $unique = uniqid();
                    $filename = $j . '_' . $unique . '_' . $filename;
                    $j++;
                    $files[] = $filename;
                }
                $zip->addFile(drupal_realpath($file->getFileUri()), $filename);
                $element['#settings']['general']['image'] = $filename; // add file name location callback for settings data in settings.txt (zip file).
            }
        }
    }

}