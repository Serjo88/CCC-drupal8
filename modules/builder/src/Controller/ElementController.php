<?php

/**
 * @file
 * Contains Drupal\builder\Controller\ElementController.
 */

namespace Drupal\builder\Controller;

use Drupal\builder\BuilderBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Controller\ControllerBase;


/**
 * Class ElementController.
 *
 * @package Drupal\builder\Controller
 */
class ElementController extends ControllerBase
{

    /**
     * Return title for add new element
     */
    public function getTitle($bid, $parent, $delta)
    {

        return t('Add @title', array('@title' => $delta));
    }

    /**
     * Return title for edit element
     */

    public function getEditTitle($bid, $id)
    {

        $BuilderBase = new BuilderBase($bid);
        $element = $BuilderBase->getElement($id);
        return t('Edit @title', array('@title' => $element['#delta']));
    }

    /**
     * Return title for delete element
     */
    public function getDeleteTitle($bid, $id)
    {
        $BuilderBase = new BuilderBase($bid);
        $element = $BuilderBase->getElement($id);
        return t('Are you sure you want to delete the @title', array('@title' => $element['#delta']));
    }

    /**
     * List elements for select to add to the Builder.
     *
     */
    public function index($bid, $parent)
    {

        $elements = BuilderBase::getElementsInfo();


        return [
            '#theme' => 'builder_ui_elements',
            '#elements' => $elements,
            '#bid' => $bid,
            '#parent' => $parent,
        ];
    }

    /**
     * Add.
     *
     * @return $form
     */
    public function add($bid, $parent, $delta)
    {


        $cache_id = BuilderBase::getCacheId($bid);
        $elements = BuilderBase::cache_get($cache_id);
        if ($parent && !isset($elements[$parent])) {
            return drupal_set_message($this->t('Parent Element doest not exist. Please try again.'), 'error');
        }

        $element = BuilderBase::getElementByDelta($delta);
        $form = \Drupal::formBuilder()->getForm('Drupal\builder\Form\ElementAddForm', array('bid' => $bid, 'delta' => $delta, 'parent' => $parent, 'element' => $element));

        return $form;


    }

    /**
     * Edit.
     *
     * @return string
     *   Return Hello string.
     */
    public function edit($bid, $id)
    {

        $builderBase = new BuilderBase($bid);
        $element = $builderBase->getElement($id);

        if (isset($element) && !empty($element['#id'])) {
            $parent = isset($element['#parent']) ? $element['#parent'] : 0;
            $delta = $element['#delta'];
            $form = \Drupal::formBuilder()->getForm('Drupal\builder\Form\ElementAddForm', array('bid' => $bid, 'delta' => $delta, 'parent' => $parent, 'element' => $element, 'id' => $id));
            return $form;
        }
        return [
            '#type' => 'markup',
            '#markup' => $this->t('Element ID: !id does not exist.', array('!id' => $id)),
        ];


    }

    /**
     * Delete.
     *
     * @return string
     *   Return Hello string.
     */
    public function delete($bid, $id)
    {

        $builder = new BuilderBase($bid);
        $builder->removeElement($id);


        $response = $this->ajaxResponsive($bid);

        return $response;
    }

    /**
     * Duplicate element.
     * @return string
     */
    public function duplicate($bid, $id)
    {

        $builder = new BuilderBase($bid);
        $element = $builder->getElement($id);
        if (!empty($element['#id'])) {
            $builder->duplicateElement($element);
        }

        $response = $this->ajaxResponsive($bid);

        return $response;
    }

    /**
     * Sortable element
     */

    public function sortable($bid)
    {
        if (!empty($_POST['elements'])) {
            $elements = $_POST['elements'];

            $builder = new BuilderBase($bid);

            $builder->sortable($elements);


        }
        exit;

    }

    /**
     * return ajax html insert callback to builder.
     * @param $bid
     * @return AjaxResponse
     */

    public function ajaxResponsive($bid)
    {
        $content = array(
            '#theme' => 'builder_ui',
            '#bid' => $bid,
        );

        $response = new AjaxResponse();
        // Attach the library necessary for using the Open(Modal)DialogCommand and
        // set the attachments for this Ajax response.
        $content['#attached']['library'][] = 'core/drupal.dialog.ajax';

        $ajax_prefix = 'builder-ui-ajax-wrapper-' . $bid;

        $response->addCommand(new HtmlCommand('#' . $ajax_prefix, $content));

        // quick edit compatible.
        $response->addCommand(new InvokeCommand('.quickedit-toolbar .action-save', 'attr', array('aria-hidden', false)));
        $response->setAttachments($content['#attached']);

        return $response;
    }


}

