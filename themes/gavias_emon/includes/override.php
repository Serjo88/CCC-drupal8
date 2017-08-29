<?php
function gavias_emon_preprocess_views_view_unformatted__taxonomy_term(&$variables){
   $current_uri = \Drupal::request()->getRequestUri();
   $url = \Drupal::service('path.current')->getPath();
   $arg = explode('/', $url);
   $tid = 0;
   if ((isset($arg[1]) && $arg[1] ==  "taxonomy") && (isset($arg[2]) && $arg[2] == "term") && isset($arg[3]) && is_numeric($arg[3]) ) {
      $tid = $arg[3];
   }
   $term = taxonomy_term_load($tid);
   $layout = 'list';
   $columns = 3;
   try{
      $field = $term->get('field_category_layout');
      if(isset($field) && $field){
         $field = $field->getValue();
         if( isset($field[0]['value']) && $field[0]['value'] ){
            $layout = $field[0]['value'];
         }
      }
   }catch(Exception $e){
      $layout = 'list';
   }
   
   $item_class = '';
   if($layout == 'grid' || $layout == 'masonry'){
      try{
         $field = $term->get('field_category_columns');
         if(isset($field) && $field){
            $field = $field->getValue();
            if( isset($field[0]['value']) && $field[0]['value'] ){
               $columns = $field[0]['value'];
            }
         }
      }catch(Exception $e){
         $columns = 3;
      }

      if ($columns == '1'){
         $item_class = 'col-lg-12 col-md-12 col-sm-12 col-xs-12';
      }else if($columns == 2){
         $item_class = 'col-lg-6 col-md-6 col-sm-6 col-xs-12';
      }else if($columns == 3){
         $item_class = 'col-lg-4 col-md-4 col-sm-4 col-xs-12';
      }else if($columns == 4){
         $item_class = 'col-lg-3 col-md-3 col-sm-6 col-xs-12';
      }else if($columns == 6){
         $item_class = 'col-lg-2 col-md-2 col-sm-6 col-xs-12';
      }
   }   
   if($layout=='masonry'){
      $item_class .= ' item-masory';
   }   
   $variables['gva_columns'] = $columns;
   $variables['gva_item_class'] = $item_class;

   $variables['gva_layout'] = $layout;
}

function gavias_emon_preprocess_views_view_unformatted(&$variables) {
   $view = $variables['view'];
   $rows = $variables['rows'];
   $style = $view->style_plugin;
   $options = $style->options;
   if(strpos($options['row_class'] , 'gva-carousel-1') || $options['row_class'] == 'gva-carousel-1' ){
      $variables['gva_carousel']['class'] = 'owl-carousel init-carousel-owl';
      $variables['gva_carousel']['columns'] = '1';
   }
   if(strpos($options['row_class'].'x' , 'gva-carousel-2') || $options['row_class'] == 'gva-carousel-2' ){
      $variables['gva_carousel']['class'] = 'owl-carousel init-carousel-owl';
      $variables['gva_carousel']['columns'] = '2';
   }
   if(strpos($options['row_class'].'x', 'gva-carousel-3') || $options['row_class'] == 'gva-carousel-3' ){
      $variables['gva_carousel']['class'] = 'owl-carousel init-carousel-owl';
      $variables['gva_carousel']['columns'] = '3';
   }
   if(strpos($options['row_class'].'x', 'gva-carousel-4') || $options['row_class'] == 'gva-carousel-4' ){
      $variables['gva_carousel']['class'] = 'owl-carousel init-carousel-owl';
      $variables['gva_carousel']['columns'] = '4';
   }
   if(strpos($options['row_class'].'x', 'gva-carousel-5') || $options['row_class'] == 'gva-carousel-5' ){
      $variables['gva_carousel']['class'] = 'owl-carousel init-carousel-owl';
      $variables['gva_carousel']['columns'] = '5';
   }
   if(strpos($options['row_class'].'x', 'gva-carousel-6') || $options['row_class'] == 'gva-carousel-6' ){
      $variables['gva_carousel']['class'] = 'owl-carousel init-carousel-owl';
      $variables['gva_carousel']['columns'] = '6';
   }
   if(strpos($options['row_class'].'x', 'gva-carousel-7') || $options['row_class'] == 'gva-carousel-7' ){
      $variables['gva_carousel']['class'] = 'owl-carousel init-carousel-owl';
      $variables['gva_carousel']['columns'] = '7';
   }
   if(strpos($options['row_class'].'x', 'gva-carousel-8') || $options['row_class'] == 'gva-carousel-8' ){
      $variables['gva_carousel']['class'] = 'owl-carousel init-carousel-owl';
      $variables['gva_carousel']['columns'] = '8';
   }
}

function gavias_emon_preprocess_views_view_grid(&$variables) {
   $view = $variables['view'];
   $rows = $variables['rows'];
   $style = $view->style_plugin;
   $options = $style->options;
   $variables['gva_masonry']['class'] = '';
   $variables['gva_masonry']['class_item'] = '';
   if(strpos($options['row_class_custom'] , 'masonry') || $options['row_class_custom'] == 'masonry' ){
      $variables['gva_masonry']['class'] = 'post-masonry-style row';
      $variables['gva_masonry']['class_item'] = 'item-masory';
   }
}

function gavias_emon_preprocess_views_view_fields__testimonial_v1(&$vars) {
   $fields = $vars['fields'];
   $vars['iframe_video'] = '';
   try{
      if(isset($fields['field_testimonial_embed']) && $fields['field_testimonial_embed']->content){
         $embed = strip_tags($fields['field_testimonial_embed']->content);
         $autoembed = new AutoEmbed();
         $vars['iframe_video'] = trim($autoembed->parse($embed));
      }   
   }catch(Exception $e){
      $vars['iframe_video'] = '';
   }
}
