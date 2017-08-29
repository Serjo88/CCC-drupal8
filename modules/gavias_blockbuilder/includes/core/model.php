<?php
function gavias_blockbuilder_load($pid) {
  $result = db_select('{gavias_blockbuilder}', 'd')
          ->fields('d')
          ->condition('id', $pid, '=')
          ->execute()
          ->fetchObject();
  $page = new stdClass();
  if($result){
    $page->title =  $result->title;
    $page->body_class = $result->body_class;  
    $page->params = $result->params;  
  }else{
    return false;
  }
  return $page;
}

function gavias_blockbuilder_get_list(){
    $result = db_select('{gavias_blockbuilder}', 'd')
          ->fields('d')
          ->execute();
    return $result;
}