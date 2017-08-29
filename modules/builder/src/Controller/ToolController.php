<?php

/**
 * @file
 * Contains \Drupal\builder\Controller\ToolController.
 */

namespace Drupal\builder\Controller;

use Drupal\builder\BuilderBase;
use Drupal\Core\Controller\ControllerBase;

/**
 * Class ToolController.
 *
 * @package Drupal\builder\Controller
 */
class ToolController extends ControllerBase
{
    /**
     * Import.
     *
     * @return string
     *   Return Hello string.
     */
    public function import($bid)
    {

        $builder = new BuilderBase($bid);
        if ($builder) {
            $form = \Drupal::formBuilder()->getForm('Drupal\builder\Form\ImportForm', array('bid' => $bid));
            return $form;
        }
        return [
            '#type' => 'markup',
            '#markup' => $this->t('An error import builder'),
        ];
    }

    /**
     * Export.
     *
     * @return string
     *   Return Hello string.
     */
    public function export($bid)
    {
        $builderBase = new BuilderBase($bid);
        return $builderBase->export();
    }

}
