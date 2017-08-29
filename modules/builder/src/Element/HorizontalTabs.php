<?php
/**
 * @file
 * Contains \Drupal\builder\Element\HorizontalTabs.
 */

namespace Drupal\builder\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Render\Element\RenderElement;


/**
 *
 * @RenderElement("horizontal_tabs")
 */
class HorizontalTabs extends RenderElement
{


    /**
     * {@inheritdoc}
     */
    public function getInfo()
    {


        $class = get_class($this);
        return array(
            '#default_tab' => '',
            '#process' => array(
                array($class, 'processHorizontalTabs'),
            ),
            '#pre_render' => array(
                array($class, 'preRenderHorizontalTabs'),
            ),
            '#theme_wrappers' => array('horizontal_tabs_wrapper', 'form_element'),
        );
    }


    public static function preRenderHorizontalTabs($element)
    {

        // Do not render the horizontal tabs element if it is empty.
        $group = implode('][', $element['#parents']);
        if (!Element::getVisibleChildren($element['group']['#groups'][$group])) {
            $element['#printed'] = TRUE;
        }
        return $element;

    }


    public static function processHorizontalTabs(&$element, FormStateInterface $form_state, &$complete_form)
    {


        if (isset($element['#access']) && !$element['#access']) {
            return $element;
        }

        // Inject a new details as child, so that form_process_details() processes
        // this details element like any other details.
        $element['group'] = array(
            '#type' => 'details',
            '#theme_wrappers' => array(),
            '#parents' => $element['#parents'],
        );
        // Add an invisible label for accessibility.
        if (!isset($element['#title'])) {
            $element['#title'] = t('Horizontal Tabs');
            $element['#title_display'] = 'invisible';
        }

        $element['#attached']['library'][] = 'builder/builder.horizontal.tabs';
        $element['#attached']['library'][] = 'builder/builder.drupal.dialog';

        // The JavaScript stores the currently selected tab in this hidden
        // field so that the active tab can be restored the next time the
        // form is rendered, e.g. on preview pages or when form validation
        // fails.
        $name = implode('__', $element['#parents']);
        if ($form_state->hasValue($name . '__active_tab')) {
            $element['#default_tab'] = $form_state->getValue($name . '__active_tab');
        }
        $element[$name . '__active_tab'] = array(
            '#type' => 'hidden',
            '#default_value' => $element['#default_tab'],
            '#attributes' => array('class' => array('horizontal-tabs-active-tab')),
        );
        // Clean up the active tab value so it's not accidentally stored in
        // settings forms.
        $form_state->addCleanValueKey($name . '__active_tab');

        return $element;
    }


}