<?php 
namespace Drupal\gavias_blockbuilder\shortcodes;
if(!class_exists('gsc_video_box')):
   class gsc_video_box{

      public function render_form(){
         $fields = array(
            'type' => 'gsc_video_box',
            'title' => ('Video Box'), 
            'size' => 3,
            'fields' => array(
         
               array(
                  'id'        => 'title',
                  'type'      => 'text',
                  'title'     => t('Title'),
                  'class'     => 'display-admin'
               ),
                  
               array(
                  'id'        => 'content',
                  'type'      => 'text',
                  'title'     => t('Data Url'),
                  'desc'      => t('example: //player.vimeo.com/video/88558878?color=ffffff&title=0&byline=0&portrait=0'),
               ),
               array(
                  'id'        => 'width',
                  'type'      => 'text',
                  'title'     => t('Data Width'),
                  'std'       => '1920',
                  'desc'      => 'example: 1920'
               ),
               array(
                  'id'        => 'height',
                  'type'      => 'text',
                  'title'     => t('Data Height'),
                  'std'       => '800',
                  'desc'      => 'example: 800'
               ),

               array(
                  'id'        => 'image',
                  'type'      => 'upload',
                  'title'     => t('Image Background'),
               ),
               
               array(
                  'id'        => 'animate',
                  'type'      => 'select',
                  'title'     => t('Animation'),
                  'desc'      => t('Entrance animation for element'),
                  'options'   => gavias_blockbuilder_animate(),
               ),
               
               array(
                  'id'        => 'el_class',
                  'type'      => 'text',
                  'title'     => t('Extra class name'),
                  'desc'      => t('Style particular content element differently - add a class name and refer to it in custom CSS.'),
               ),

            ),                                       
         );
         return $fields;
      }

      public function render_content( $item ) {
         if( ! key_exists('content', $item['fields']) ) $item['fields']['content'] = '';
         print self::sc_video_box( $item['fields'], $item['fields']['content'] );
      }


      public static function sc_video_box( $attr, $content = null ){
         global $base_url, $base_path;
         extract(shortcode_atts(array(
            'title'           => '',
            'width'           => '1920',
            'height'          => '800',
            'image'           => '',
            'animate'         => '',
            'el_class'        => '',
         ), $attr));

         $style='width:'.$width .'px; height:'.$height.'px; max-width:100%;';
         
         if($image){
            $image = substr($base_path, 0, -1) . $image; 
         }
      ?>
      <?php ob_start(); ?>
      <div class="widget gsc-video-box <?php print $el_class;?> clearfix" style="background-image: url('<?php print $image ?>');<?php print $style; ?>">
         <div class="video-inner">
            <div class="video-body">
               <a class="gsc-video-link" data-height="<?php print $height ?>" data-width="<?php print $width ?>" data-url="<?php print $content ?>" href="#">
                  <i class="icon-play space-40"></i>
                  <span class="video-title"><?php if($title) print $title ; ?></span>
              </a>
            </div> 
         </div>    
      </div>   
      <?php return ob_get_clean(); ?>
       <?php
      }

      public function load_shortcode(){
         add_shortcode( 'video_box', array('gsc_video_box', 'sc_video_box') );
      }
   }
endif;   




