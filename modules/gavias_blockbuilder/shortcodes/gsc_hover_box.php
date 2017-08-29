<?php 
namespace Drupal\gavias_blockbuilder\shortcodes;
if(!class_exists('gsc_hover_box')):
   class gsc_hover_box{
      
      public function render_form(){
         $fields = array(
            'type'            => 'gsc_hover_box',
            'title'           => t('Hover Box'),
            'size'            => 3,
            'icon'            => 'fa fa-bars',
            'fields' => array(
               array(
                  'id'        => 'icon',
                  'type'      => 'text',
                  'title'     => t('Icon'),
               ),
               array(
                  'id'        => 'frontend_title',
                  'type'      => 'text',
                  'title'     => t('Title for FrontEnd'),
                  'class'     => 'display-admin'
               ),
               array(
                  'id'        => 'frontend_content',
                  'type'      => 'textarea',
                  'title'     => t('Content for FrontEnd'),
               ),
               array(
                  'id'        => 'backend_title',
                  'type'      => 'text',
                  'title'     => t('Title for BackEnd'),
               ),
               array(
                  'id'        => 'backend_content',
                  'type'      => 'textarea',
                  'title'     => t('Content for BackEnd'),
               ),
               array(
                  'id'        => 'link',
                  'type'      => 'text',
                  'title'     => t('Link'),
               ),
               array(
                  'id'        => 'target',
                  'type'      => 'select',
                  'title'     => t('Open in new window'),
                  'desc'      => t('Adds a target="_blank" attribute to the link'),
                  'options'   => array( 0 => 'No', 1 => 'Yes' ),
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
         print self::sc_hover_box( $item['fields'] );
      }

      public static function sc_hover_box( $attr, $content = null ){
         extract(shortcode_atts(array(
            'icon'                  => 'fa fa-bars',
            'frontend_title'        => '',
            'frontend_content'      => '',
            'backend_title'         => '',
            'backend_content'       => '',
            'link'                  => '',
            'target'                => '',
            'el_class'              => ''
         ), $attr));

         // target
         if( $target ){
            $target = 'target="_blank"';
         } else {
            $target = false;
         }

         ?>
         <?php ob_start(); ?>
         <div class="widget gsc-hover-box gavias-metro-box <?php print $el_class; ?>">
            <div class="flipper">
               <div class="front">
                  <div class="gavias-metro-box-header">
                     <a <?php if($link) print 'href="'. $link .'"' ?> <?php print $target ?> class="gavias-icon-boxed"><i class="<?php print $icon ?>"></i></a>
                     <span class="box-title">
                     <a <?php if($link) print 'href="'. $link .'"' ?> <?php print $target ?> >
                        <?php print $frontend_title ?>
                     </a>   
                     </span>
                  </div>
                  <div><p><?php print $frontend_content ?></p></div>
               </div>
               <div class="back">
                  <h3><a <?php if($link) print 'href="'. $link .'"' ?> <?php print $target ?> ><?php print $backend_title ?></a></h3>
                  <p class="p-tc"><?php print $backend_content ?></p>
               </div>
            </div>
         </div>
         <?php return ob_get_clean(); ?>
         <?php
      }

      public function load_shortcode(){
         add_shortcode( 'hover_box',array('gsc_hover_box', 'sc_hover_box') );
      }
   }
endif;   




