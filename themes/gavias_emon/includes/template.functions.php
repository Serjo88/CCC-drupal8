<?php
function gavias_emon_preprocess_node(&$variables) {
  if ($variables['teaser'] || !empty($variables['content'])) {
    unset($variables['content']['links']['#links']);
  }
  if ($variables['node']->getType() == 'article') {
      $node = $variables['node'];
      $post_format = 'standard';
      try{
         $field_post_format = $node->get('field_post_format');
         if(isset($field_post_format->value) && $field_post_format->value){
            $post_format = $field_post_format->value;
         }
      }catch(Exception $e){
         $post_format = 'standard';
      }

      $iframe = '';
      if($post_format == 'video' || $post_format == 'audio'){
         try{
            $field_post_embed = $node->get('field_post_embed');
            if(isset($field_post_embed->value) && $field_post_embed->value){
               $autoembed = new AutoEmbed();
               $iframe = $autoembed->parse($field_post_embed->value);
            }else{
               $iframe = '';
               $post_format = 'standard';
            }
         }
         catch(Exception $e){
            $post_format = 'standard';
         }
      }
      $variables['gva_iframe'] = $iframe;
      $variables['post_format'] = $post_format;

  }elseif($variables['node']->getType() == 'portfolio'){
    $iframe = '';
    $node = $variables['node'];
    try{
      $field_portfolio_embed = $node->get('field_portfolio_embed');
      if(isset($field_portfolio_embed->value) && $field_portfolio_embed->value){
        $autoembed = new AutoEmbed();
        $iframe = $autoembed->parse($field_portfolio_embed->value);
      }else{
        $iframe = '';
      }
    }
    catch(Exception $e){
       $iframe = '';
    }
    $variables['gva_iframe'] = $iframe;
  }

}

function gavias_emon_preprocess_field(&$variables, $hook) {
  $element = $variables['element'];
}  

function gavias_emon_preprocess_breadcrumb(&$variables){
  $variables['#cache']['max-age'] = 0;

  $request = \Drupal::request();
  $title = '';
  if ($route = $request->attributes->get(\Symfony\Cmf\Component\Routing\RouteObjectInterface::ROUTE_OBJECT)) {
    $title = \Drupal::service('title_resolver')->getTitle($request, $route);
  }

  if($variables['breadcrumb']){
     foreach ($variables['breadcrumb'] as $key => &$value) {
      if($value['text'] == 'Node'){
        unset($variables['breadcrumb'][$key]);
      }
    }

    if(($node = \Drupal::routeMatch()->getParameter('node')) && $variables['breadcrumb']){
      try{
        $field = $node->get('field_post_category');
        $field = $field->getValue();
         if( isset($field[0]['target_id']) && $field[0]['target_id'] ){
            $term = taxonomy_term_load($field[0]['target_id']);
            if($term){
              $variables['breadcrumb'][] = array(
                'text' => $term->get('name')->value,
                'url' => \Drupal::url('entity.taxonomy_term.canonical', array('taxonomy_term'=>$field[0]['target_id']))
              );
            }  
         }
        
      }catch(Exception $e){

      }
    } 
   
    if(!empty($title)){
      $variables['breadcrumb'][] = array(
          'text' => ''
      );
      $variables['breadcrumb'][] = array(
          'text' => $title
      );
    }  
  }
}