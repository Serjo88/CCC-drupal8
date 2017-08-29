<?php
function variable_get($name, $default = NULL) {
  global $conf;
  return isset($conf[$name]) ? $conf[$name] : $default;
}

function gavias_blockbuilder_makeid($length = 5){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function gavias_blockbuilder_includes( $path, $ifiles=array() ){
    if( !empty($ifiles) ){
         foreach( $ifiles as $key => $file ){
            $file  = $path.'/'.$file; 
            if(is_file($file)){
                require($file);
            }
         }   
    }else {
        $files = glob($path);
        foreach ($files as $key => $file) {
            if(is_file($file)){
                require($file);
            }
        }
    }
}

/*================================================
                Block for theme
=================================================*/                
function gavias_blockbuilder_get_blocks_options() {
  static $_blocks_array = array();
    if (empty($_blocks_array)) {
      // Get default theme for user.
      $theme_default = \Drupal::config('system.theme')->get('default');
      // Get storage handler of block.
      $block_storage = \Drupal::entityManager()->getStorage('block');
      // Get the enabled block in the default theme.
      $entity_ids = $block_storage->getQuery()->condition('theme', $theme_default)->execute();
      $entities = $block_storage->loadMultiple($entity_ids);
      $_blocks_array = [];
      foreach ($entities as $block_id => $block) {
        //if ($block->get('settings')['provider'] != 'tb_megamenu') {
          $_blocks_array[$block_id] = $block->label();
        //}
      }
      asort($_blocks_array);
    }
    return $_blocks_array;
}

function gavias_blockbuilder_load_block($block_key) {
  $blocks = gavias_blockbuilder_get_blocks_info();
  return isset($blocks[$block_key]) ? $blocks[$block_key] : NULL;
}


function render_block_content($module, $delta) {
  $output = '';
  if ($block = block_load($module, $delta)) {
    if ($build = module_invoke($module, 'block_view', $delta)) {
      $delta = str_replace('-', '_', $delta);
      drupal_alter(array('block_view', "block_view_{$module}_{$delta}"), $build, $block);

      if (!empty($build['content'])) {
        print is_array($build['content']) ? render($build['content']) : $build['content'];
      }
    }
  }
  print $output;
}


function gavias_blockbuilder_render_block($key) {
    $block = \Drupal\block\Entity\Block::load($key);
    if($block){
      $block_content = \Drupal::entityManager()
        ->getViewBuilder('block')
        ->view($block);
      return drupal_render($block_content);
    }else{
      return '<div>Missing view, block "'.$key.'"</div>';
    }
  }

function gavias_blockbuilder_get_blocks_info() {
  static $_blocks_array = array();
  if (empty($_blocks_array)) {
    $blocks = db_select('block', 'b')->fields('b')->execute()->fetchAll();
    $_blocks_array = array();
    foreach ($blocks as $block) {

        $_blocks_array[$block->module . '---' . $block->delta] = $block;
  
    }
  }
  return $_blocks_array;
}

function gavias_blockbuilder_block_title($block_key) {
  $blocks_options = gavias_blockbuilder_render_block();
  if (isset($blocks_options[$block_key])) {
    return $blocks_options[$block_key];
  }
  return NULL;
}
