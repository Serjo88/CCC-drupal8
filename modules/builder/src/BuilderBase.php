<?php
/**
 * @file
 * Provides Drupal\builder\BuilderBase.
 */

namespace Drupal\builder;

use Drupal\Core\Archiver;
use Drupal\core\Database;
use Drupal\file\Entity\File;

class BuilderBase {

  protected $elements = array();

  protected $cache_id;
  protected $bid;


  public function __construct($bid, $data = NULL) {

    $this->cache_id = $this->getCacheId($bid);
    $this->bid = $bid;
    if (isset($data)) {
      $this->elements = $data;
      $this->update();
    }
    else {
      $this->load();
    }


  }



  /**
   * Generate an unique ID for Element.
   */
  public function generateElementID() {
    return uniqid('builder-element-');
  }

  public function setElement($element, $id = NULL) {

    if (!isset($id) && empty($id)) {
      $id = $this->generateElementId();
      $parent_id = isset($element['#parent']) ? $element['#parent'] : NULL;
      $maxWeight = $this->getMaxWeight($parent_id);
      $element['#weight'] = $maxWeight + 1;
    }
    $element['#id'] = $id;
    $this->elements[$id] = $element;
    $this->update();


    return $id;
  }

  public function findElements($level = 0) {
    $items = array();
    $elements = $this->elements;
    foreach ($elements as $id => $element) {
      if (empty($element['#parent'])) {
        $items[$id] = $element;
      }
    }

    return $items;
  }


  public function getMaxWeight($parent_id = NULL) {

    $weights = array();
    $items = array();
    if (!empty($parent_id)) {
      $items = $this->getChildrenToRemove($parent_id);
    }
    else {
      $items = $this->findElements(0);
    }


    if (!empty($items)) {
      foreach ($items as $k => $v) {
        if (isset($v['#weight'])) {
          $weights[] = $v['#weight'];
        }
      }
    }

    if (!empty($weights)) {
      return max($weights);
    }


    return 0;

  }


  /**
   *Get Element by ID.
   */
  public function getElement($id) {
    return isset($this->elements[$id]) ? $this->elements[$id] : NULL;
  }

  /**
   * Add mutiple elements items to the builder
   */

  public function setElements($elements = array()) {
    foreach ($elements as $element) {
      $this->setElement($element);
    }

  }


  /**
   * Get all elements of the builder.
   */

  public function getElements() {
    return $this->elements;
  }


  /**
   * Get  elements by parent ID.
   */
  public function getChildren($parent_id) {
    $elements = array();
    foreach ($this->elements as $k => $element) {
      if (isset($v['#parent']) && $v['#parent'] == $parent_id) {
        $elements[$k] = $element;
      }
    }

    return $elements;
  }


  /**
   * Remove an element by ID, Check it has children items. We also remove it.
   */

  public function removeElement($id) {

    if (isset($this->elements[$id])) {
      unset($this->elements[$id]);

      $children = $this->getChildrenToRemove($id);

      if (!empty($children)) {
        foreach ($children as $k => $element) {

          $this->removeElement($k); // Recursive!!!

        }
      }
    }

    $this->update(); // save builder to cache after updated
  }

  /**
   * Get children element to remove.
   */
  public function getChildrenToRemove($parent_id = NULL) {
    $elements = array();
    foreach ($this->elements as $k => $element) {
      if (isset($parent_id) && isset($element['#parent']) && $element['#parent'] == $parent_id) {
        $elements[$k] = $element;
      }
    }

    return $elements;
  }

  /**
   * Sortable elements
   */
  public function sortable($elements) {
    if (!empty($elements)) {
      foreach ($elements as $key => $element) {

        if (isset($element[0]) && isset($element[1])) {
          $id = $element[0];

          $parent = $element[1];
          if (isset($this->elements[$id])) {
            $this->elements[$id]['#weight'] = $key;
            if ($parent) {
              $this->elements[$id]['#parent'] = $parent;
            }
            else {
              if (isset($this->elements[$id]['#parent'])) {
                unset($this->elements[$id]['#parent']);
              }
            }
          }
        }

      }
      $this->update();
    }


  }

  /**
   * duplicate element
   */

  public function duplicateElement($element) {
    $e = $element;
    $generated_id = $this->generateElementId();
    $e['#id'] = $generated_id;
    $this->setElement($e, $generated_id);

    // check if has children, we dupliate child first.
    $children = $this->getChildrenToRemove($element['#id']);

    if (!empty(($children))) {
      foreach ($children as $k => $v) {
        if (!empty($v['#parent'])) {
          $v['#parent'] = $generated_id;
        }
        $this->duplicateElement($v);
      }
    }


    $this->update();

  }


  /**
   * Set Weight of element.
   */

  public function setWeight($id, $weight) {

    if (isset($this->elements[$id])) {

      $element = $this->elements[$id];
      $element['#weight'] = $weight;

      $this->setElement($element, $id);

    }
  }

  /**
   * Get Elements by Tree view.
   */

  public function getTree() {
    $elements = $this->elements;
    $tree = array();
    foreach ($elements as $key => $element) {
      // Allow other modules implement element by hook_builder_element_view($type, $element);

      $elements[$key]['#id'] = $key;
      $elements[$key]['#bid'] = $this->bid;


      $elements[$key] = \Drupal::service('module_handler')
        ->invokeAll('builder_element_view', array(
          $element['#type'],
          $elements[$key]
        ));
      if (!isset($element['#parent']) || (isset($element['#parent']) && !isset($elements[$element['#parent']]))) {
        $tree[$key] = &$elements[$key];
      }
      else {
        if (isset($element['#parent']) && $element['#parent'] && isset($elements[$element['#parent']])) {
          $elements[$element['#parent']]['children'][$key] = &$elements[$key];
        }
      }
    }

    return $tree;
  }

  /**
   * Render the Elements.
   */
  public function render() {
    $tree = $this->getTree();


    return $this->render_tree($tree);
  }

  public static function getRenderTree($bid, $elements) {
    $tree = array();
    foreach ($elements as $key => $element) {
      // Allow other modules implement element by hook_builder_element_view($delta, $element);

      // HOOK_block_builder_element_view(&$element)

      $element['#view_bid'] = $bid;
      $view = \Drupal::service('module_handler')
        ->invokeAll('builder_element_view', array(
          $element['#type'],
          $elements[$key]
        ));
      if (!empty($view)) {
        $elements[$key] = $view;
      }

      if (isset($elements[$key]['#theme_wrappers'])) {
        //  unset($elements[$key]['#theme_wrappers']);
      }
      if (!isset($element['#parent']) || (isset($element['#parent']) && !isset($elements[$element['#parent']]))) {
        $tree[$key] = &$elements[$key];
      }
      else {
        if (isset($element['#parent']) && $element['#parent'] && isset($elements[$element['#parent']])) {
          $elements[$element['#parent']][$key] = &$elements[$key];
        }
      }
    }

    return $tree;
  }

  /**
   * Render a tree.
   */
  public function render_tree($tree) {

    return render($tree);
  }

  /**
   * Update builder to cache table.
   */

  public function update() {

    $this->cache_set($this->cache_id, $this->elements);

  }

  public function load() {
    $data = $this->cache_get($this->cache_id);
    if ($data) {
      $this->elements = $data;
    }
  }


  /**
   *Create new builder bid temp prepare for save. db table: builder_data
   */

  public static function createBid() {
    return _builder_create_bid();

  }

  /**
   * Generate a cache_id from builder ID.
   * @param $bid
   * @return string
   */
  public static function getCacheId($bid) {
    $language_id = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $cid = "builder:$language_id:$bid";
    return $cid;
  }

  /**
   * Get builder data from caching.
   */
  public static function cache_get($cid) {

    // $cache = \Drupal::cache()->get($cid);
    /* if (isset($cache->data)) {
     return $cache->data;
   }*/

    $config = \Drupal::config('builder.cache');
    $data = $config->get($cid);
    return $data;

  }


  /**
   * Set builder data to cache.
   */
  public static function cache_set($cid, $data) {
    /* $tags = array('module:builder');
     \Drupal::cache()
       ->set($cid, $data, CacheBackendInterface::CACHE_PERMANENT, $tags);*/

    $config = \Drupal::service('config.factory')->getEditable('builder.cache');
    $config->set($cid, $data);
    $config->save();


  }

  /**
   * Clear all builder cache
   */
  public function clear() {
    //\Drupal::cache()->delete($this->cache_id);
    $config = \Drupal::service('config.factory')->getEditable('builder.cache');
    $config->clear($this->cache_id)->save();
    $this->elements = NULL;
    $this->cache_id = NULL;
    $this->bid = NULL;


  }


  /**
   * Get Builder data from Existing Entity.
   */
  public static function getData($bid) {

    return _builder_get_data($bid);

  }


  /**
   * Convert an Object to Array.
   *
   * @param $obj
   * @param $arr
   * @return mixed
   */
  public static function objToArray($obj, &$arr) {

    if (!is_object($obj) && !is_array($obj)) {
      $arr = $obj;
      return $arr;
    }

    foreach ($obj as $key => $value) {
      if (!empty($value)) {
        $arr[$key] = array();
        self::objToArray($value, $arr[$key]);
      }
      else {
        $arr[$key] = $value;
      }
    }
    return $arr;

  }


  /**
   * Form Modal dialog settings
   */

  public static function modalDialogOptions() {

    return json_encode(array(
      'resizable' => TRUE,
      'width' => '100%',
      'height' => 'auto',
      'maxWidth' => '1100',
      'modal' => TRUE,
    ));
  }

  /**
   * Get all elements from hook_builder_element_info()
   */


  public static function getElementsInfo() {

    $elements = array();
    foreach (\Drupal::moduleHandler()
               ->getImplementations('builder_element_info') as $module) {
      $module_element = \Drupal::moduleHandler()
        ->invoke($module, 'builder_element_info', $args = array());
      foreach ($module_element as $delta => $element) {
        $element['#delta'] = $delta;
        $element['#module'] = $module;
        $elements[] = $element;
      }
    }
    return $elements;


  }

  public static function getElementByDelta($delta = '') {
    $element = array();


    $elements = self::getElementsInfo();

    foreach ($elements as $element) {

      if ($element['#delta'] == $delta) {

        return $element;
      }
    }


    return $element;
  }

  /**
   * Export builder to zip file
   */

  public function export() {

    $elements = $this->elements;
    $files_added = array();
    $i = 1;
    $name = 'builder_' . REQUEST_TIME;
    $data = '';

    $zip_uri = 'temporary://' . $name . '.zip';
    $zip = new \ZipArchive();
    $items = array();
    //ArchiverInterface::ad
    if ($zip->open(\Drupal::service('file_system')
        ->realpath($zip_uri), \ZipArchive::CREATE) === TRUE
    ) {

      if (!empty($elements)) {
        foreach ($elements as $key => $element) {
          if (!empty($key)) {
            unset($elements[$key]);
            $key = $key . '-007';
          }
          // allow other modules use hook_builder_element_export_alter($zip, $element) or hook_builder_element_export__DELTA_alter($zip, $element)
          \Drupal::moduleHandler()->alter([
            'builder_element_export',
            "builder_element_export_" . $element['#delta']
          ], $zip, $element);
          $settings = isset($element['#settings']) ? $element['#settings'] : NULL;
          $background_images = !empty($settings['design']['css_box']['extra']['background']['image']) ? $settings['design']['css_box']['extra']['background']['image'] : array();
          if (!empty($background_images)) {
            foreach ($background_images as $k => $fid) {
              $file = File::load($fid);
              $file_name = $file->getFilename();
              if (!empty($files_added) && in_array($file_name, $files_added)) {
                // this file has been exits in zip. Now we need rename it
                $prefix = $i;
                $file_name = $prefix . '_' . $file_name;
                $i++; // if file exist we need increase prefix number
              }
              $files_added[] = $file_name; // added this to array and check for later.
              $zip->addFile(\Drupal::service('file_system')
                ->realpath($file->getFileUri()), $file_name);
              $element['#settings']['design']['css_box']['extra']['background']['image'][$k] = $file_name;

            }
          }


          $element['#id'] = $element['#id'] . '-007'; // keep id same but extra new part "007"
          if (!empty($element['#parent'])) {
            $element['#parent'] = $element['#parent'] . '-007';
          }
          if (!empty($key)) {
            $elements[$key] = $element;
          }
          $items[$key] = $element;

        }


        $json = @serialize($items);
        $zip->addFromString('settings.txt', $json);
        $zip->close();
        header('Content-disposition: attachment; filename=' . $name . '.zip');
        header('Content-type: application/zip');
        readfile(\Drupal::service('file_system')->realpath($zip_uri));
        exit();
      }

    }

    return [
      '#type' => 'markup',
      '#markup' => t('An error exporting the builder.'),
    ];
  }

  /**
   * Import builder
   */

  public function import($file) {

    $user = \Drupal::currentUser();

    $elements = array();
    $files_saved = array();

    $zip = new \ZipArchive();
    $res = $zip->open(file_create_url(\Drupal::service('file_system')
      ->realpath($file->getFileUri())));

    if ($res === TRUE) {

      $extract_directory = _builder_extract_directory();
      $local_file = \Drupal::service('file_system')
        ->realpath($file->getFileUri());
      try {
        builder_tools_extract_archive($local_file, $extract_directory);
      } catch (Exception $e) {
        drupal_set_message(t('An error: @msg', array('@msg' => $e->getMessage())), 'error');
      }

      $files = file_scan_directory($extract_directory, '/.*/');
      if (!empty($files)) {
        foreach ($files as $read_file) {
          if ($read_file->filename == 'settings.txt') {
            $settings_content = file_get_contents(\Drupal::service('file_system')
              ->realpath($read_file->uri));
            $elements = @unserialize($settings_content);
            $file_setting = entity_create('file', array(
              'uri' => $read_file->uri,
              'uid' => $user->id(),
              'status' => 0, //FILE_STATUS_PERMANENT,
              'filename' => $read_file->filename,
            ));
            if ($file_setting) {
              $file_setting->save();
              $file_setting->delete();
            }
          }
          else {
            if (!empty($read_file->uri)) {
              $file_create = entity_create('file', array(
                'uri' => $read_file->uri,
                'uid' => $user->id(),
                'status' => 0, //FILE_STATUS_PERMANENT,
                'filename' => $read_file->filename,
              ));
              $file_create->save();
              $file_copy = file_move($file_create, 'public://');
              if ($file_copy) {
                $files_saved[$file_copy->getFilename()] = $file_copy->id();
              }
            }
          }
        }
      }


      if (!empty($elements)) {
        // re update fid to each element
        foreach ($elements as $key => $element) {
          // allow other module alter use HOOK_builder_element_import(&$files, &$element) OR HOOK_builder_element_DELTA_import(&$files, &$element)
          \Drupal::moduleHandler()->alter([
            'builder_element_import',
            "builder_element_import_" . $element['#delta']
          ], $files_saved, $element);
          $settings = isset($element['#settings']) ? $element['#settings'] : NULL;
          $background_images = !empty($settings['design']['css_box']['extra']['background']['image']) ? $settings['design']['css_box']['extra']['background']['image'] : array();

          if (!empty($background_images)) {
            foreach ($background_images as $k => $filename) {
              if (!empty($filename) && !empty($files_saved[$filename])) {
                $element['#settings']['design']['css_box']['extra']['background']['image'][$k] = $files_saved[$filename];
              }
            }
          }
          $elements[$key] = $element;
        }
        $this->elements = $elements; // add elements to builder.
        $this->update(); //update builder
      }

      drupal_rmdir($extract_directory); // remove import directory temp.
    }
    $file->delete(); // delete zip import file
  }


}




