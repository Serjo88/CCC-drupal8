<?php

/**
 * @file
 * Contains \Drupal\builder\Element\CssBox.
 */

namespace Drupal\builder\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Render\Element\RenderElement;

/**
 *
 * @RenderElement("css_box")
 */
class CssBox extends RenderElement
{


    /**
     * {@inheritdoc}
     */
    public function getInfo()
    {
        $class = get_class($this);
        return array(
            '#process' => array(
                array($class, 'processFormat'),
            ),
            '#theme_wrappers' => array('css_box_wrapper'),
        );
    }


    public static function processFormat(&$element, FormStateInterface $form_state, &$complete_form)
    {

        // Ensure that children appear as subkeys of this element.
        $element['#tree'] = TRUE;

        // Turn original element into a text format wrapper.
        $element['#attached']['library'][] = 'builder/builder.css.box';

        $element['margin'] = array(
            '#type' => 'container',
            '#attributes' => array('class' => array('margin-wrapper')),
        );
        $element['margin']['top'] = array(
            '#type' => 'textfield',
            '#title' => t('Top'),
            '#title_display' => 'invisible',
            '#default_value' => isset($element['#options']['margin']['top']) ? $element['#options']['margin']['top'] : '',
            '#attributes' => array('class' => array('builder-top'), 'title' => t('Margin top')),

        );
        $element['margin']['right'] = array(
            '#type' => 'textfield',
            '#title' => t('Right'),
            '#title_display' => 'invisible',
            '#default_value' => isset($element['#options']['margin']['right']) ? $element['#options']['margin']['right'] : '',
            '#attributes' => array('class' => array('builder-right'), 'title' => t('Margin right')),
        );
        $element['margin']['bottom'] = array(
            '#type' => 'textfield',
            '#title' => t('Bottom'),
            '#title_display' => 'invisible',
            '#default_value' => isset($element['#options']['margin']['bottom']) ? $element['#options']['margin']['bottom'] : '',
            '#attributes' => array('class' => array('builder-bottom'), 'title' => t('Margin bottom')),
        );
        $element['margin']['left'] = array(
            '#type' => 'textfield',
            '#title' => t('Top'),
            '#title_display' => 'invisible',
            '#default_value' => isset($element['#options']['margin']['left']) ? $element['#options']['margin']['left'] : '',
            '#attributes' => array('class' => array('builder-left'), 'title' => t('Margin left')),
        );

        // Border

        $element['border'] = array(
            '#type' => 'container',
            '#attributes' => array('class' => array('border-wrapper')),
        );

        $element['border']['top'] = array(
            '#type' => 'textfield',
            '#title' => t('Top'),
            '#title_display' => 'invisible',
            '#default_value' => isset($element['#options']['border']['top']) ? $element['#options']['border']['top'] : '',
            '#attributes' => array('class' => array('builder-top'), 'title' => t('border top')),
        );

        $element['border']['right'] = array(
            '#type' => 'textfield',
            '#title' => t('Right'),
            '#title_display' => 'invisible',
            '#default_value' => isset($element['#options']['border']['right']) ? $element['#options']['border']['right'] : '',
            '#attributes' => array('class' => array('builder-right'), 'title' => t('Border right')),
        );
        $element['border']['bottom'] = array(
            '#type' => 'textfield',
            '#title' => t('Bottom'),
            '#title_display' => 'invisible',
            '#default_value' => isset($element['#options']['border']['bottom']) ? $element['#options']['border']['bottom'] : '',
            '#attributes' => array('class' => array('builder-bottom'), 'title' => t('Border bottom')),
        );
        $element['border']['left'] = array(
            '#type' => 'textfield',
            '#title' => t('Top'),
            '#title_display' => 'invisible',
            '#default_value' => isset($element['#options']['border']['left']) ? $element['#options']['border']['left'] : '',
            '#attributes' => array('class' => array('builder-left'), 'title' => t('Border left')),
        );

        // Padding
        $element['padding'] = array(
            '#type' => 'container',
            '#attributes' => array('class' => array('padding-wrapper')),
        );
        $element['padding']['top'] = array(
            '#type' => 'textfield',
            '#title' => t('Top'),
            '#title_display' => 'invisible',
            '#default_value' => isset($element['#options']['padding']['top']) ? $element['#options']['padding']['top'] : '',
            '#attributes' => array('class' => array('builder-top'), 'title' => t('Padding top')),
        );
        $element['padding']['right'] = array(
            '#type' => 'textfield',
            '#title' => t('Right'),
            '#title_display' => 'invisible',
            '#default_value' => isset($element['#options']['padding']['right']) ? $element['#options']['padding']['right'] : '',
            '#attributes' => array('class' => array('builder-right'), 'title' => t('Padding right')),
        );
        $element['padding']['bottom'] = array(
            '#type' => 'textfield',
            '#title' => t('Bottom'),
            '#title_display' => 'invisible',
            '#default_value' => isset($element['#options']['padding']['bottom']) ? $element['#options']['padding']['bottom'] : '',
            '#attributes' => array('class' => array('builder-bottom'), 'title' => t('padding bottom')),
        );
        $element['padding']['left'] = array(
            '#type' => 'textfield',
            '#title' => t('Top'),
            '#title_display' => 'invisible',
            '#default_value' => isset($element['#options']['padding']['left']) ? $element['#options']['padding']['left'] : '',
            '#attributes' => array('class' => array('builder-left'), 'title' => t('Padding left')),
        );

        // background
        $background = isset($element['#options']['extra']['background']) ? $element['#options']['extra']['background'] : array();
        $element['extra']['background'] = array(
            '#type' => 'details',
            '#title' => t('Background'),
            '#open' => FALSE,
        );
        $element['extra']['background']['color_enabled'] = array(
            '#title' => t('Use background color'),
            '#type' => 'checkbox',
            '#default_value' => isset($background['color_enabled']) ? $background['color_enabled'] : 0,
        );
        $element['extra']['background']['color'] = array(
            '#title' => t('Background color'),
            '#type' => 'color',
            '#default_value' => isset($background['color']) ? $background['color'] : '#ffffff',
            '#states' => array(

                'invisible' => array(

                    ':input[name="settings[design][css_box][extra][background][color_enabled]"]' => array('checked' => FALSE),
                ),
            ),
        );
        $element['extra']['background']['image'] = array(
            '#type' => 'managed_file',
            '#title' => t('Upload background image'),
            '#description' => t('Allowed extensions: gif png jpg jpeg'),
            '#default_value' => isset($background['image']) ? $background['image'] : '',
            '#upload_location' => 'public://',
            '#upload_validators' => array(
                'file_validate_extensions' => array('gif png jpg jpeg'),
                // Pass the maximum file size in bytes
                'file_validate_size' => array(10 * 3024 * 3024),
            ),
        );
        $element['extra']['background']['repeat'] = array(
            '#type' => 'select',
            '#title' => t('Repeat'),
            '#options' => array(
                'repeat' => 'Repeat',
                'no-repeat' => 'No repeat',
            ),
            '#default_value' => isset($background['repeat']) ? $background['repeat'] : 'no-repeat',
        );

        // border
        $border = isset($element['#options']['extra']['border']) ? $element['#options']['extra']['border'] : array();

        $element['extra']['border'] = array(
            '#type' => 'details',
            '#title' => t('Border'),
            '#open' => FALSE,
        );
        $element['extra']['border']['style'] = array(
            '#type' => 'select',
            '#title' => t('Border stype'),
            '#default_value' => isset($border['style']) ? $border['style'] : 'none',
            '#options' => array(
                'none' => t('None'),
                'solid' => t('Solid'),
                'hidden' => t('hidden'),
                'dotted' => t('Dotted'),
                'dashed' => t('Dashed'),
                'double' => t('Double'),
                'groove' => t('Groove'),
                'ridge' => t('Ridge'),
                'inset' => t('Inset'),
                'outset' => t('Outset'),
                'initial' => t('Initial'),
                'inherit' => t('Inherit'),
            ),
        );

        $element['extra']['border']['color_enabled'] = array(
            '#title' => t('Use color'),
            '#type' => 'checkbox',
            '#default_value' => isset($border['color_enabled']) ? $border['color_enabled'] : 0,
        );
        $element['extra']['border']['color'] = array(
            '#title' => t('Border color'),
            '#type' => 'color',
            '#default_value' => isset($border['color']) ? $border['color'] : '#ffffff',
            '#states' => array(

                'invisible' => array(

                    ':input[name="settings[design][css_box][extra][border][color_enabled]"]' => array('checked' => FALSE),
                ),
            ),
        );
        $css = isset($element['#options']['extra']['css']) ? $element['#options']['extra']['css'] : '';
        $element['extra']['css'] = array(
            '#type' => 'textarea',
            '#title' => t('Enter your own custom css'),
            '#description' => t("This css will override for only this element. Example: <br /> <pre>@css</pre> ", array('@css' => "background-color: #000; <br />padding-top: 10px;")),
            '#default_value' => $css,
        );


        return $element;
    }

    /**
     * Wraps the current user.
     *
     * \Drupal\Core\Session\AccountInterface
     */
    protected static function currentUser()
    {
        return \Drupal::currentUser();
    }

    /**
     * Wraps the config factory.
     *
     * @return \Drupal\Core\Config\ConfigFactoryInterface
     */
    protected static function configFactory()
    {
        return \Drupal::configFactory();
    }

    /**
     * Wraps the element info service.
     *
     * @return \Drupal\Core\Render\ElementInfoManagerInterface
     */
    protected static function elementInfo()
    {
        return \Drupal::service('element_info');
    }


}

