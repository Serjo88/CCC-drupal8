<?php
/**
 * @file
 * Contains \Drupal\gaviasthemer\Controller\CustomizeController.
 */
namespace Drupal\gaviasthemer\Controller;

use Drupal\Core\Url;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;

class CustomizeController extends ControllerBase {

  public function save(){
    $user = \Drupal::currentUser();
    header('Content-type: application/json');
    $json = $_REQUEST['data'];
    $theme = $_REQUEST['theme_name'];
    $path_theme = drupal_get_path('theme', $theme);
    gaviasthemer_writecache(  $path_theme . '/css/', 'customize', $json, 'json' );
    
    ob_start();
      if($json){
        require_once($path_theme . '/customize/dynamic_style.php');
      } 
    $styles = ob_get_clean();
    
    $styles = str_replace(array('<style>', '</style>'), '', $styles);
    $styles = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $styles );
    $styles = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '   ', '    ' ), '', $styles );
    gaviasthemer_writecache( $path_theme . '/css/', 'customize',  $styles, 'css' );

     $result = array(
      'data' => 'update saved'
    );
    print json_encode($result);
    exit(0);
  }

  public function preview(){
    header('Content-type: application/json');
    $json = $_REQUEST['data'];
    $theme = $_REQUEST['theme_name'];
    $path_theme = drupal_get_path('theme', $theme);
    
    ob_start();
    if($json){
      require_once($path_theme . '/customize/dynamic_style.php');
    } 
    $styles = ob_get_clean();

    $styles = str_replace(array('<style>', '</style>'), '', $styles);
    $styles = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $styles );
    $styles = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '   ', '    ' ), '', $styles );
    $return['style']= $styles;

    echo json_encode($return);
    exit(0);
  }  
  
}
