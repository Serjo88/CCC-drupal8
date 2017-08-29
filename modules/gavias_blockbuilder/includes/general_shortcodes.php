<?php
function gavias_blockbluider_general_shortcode(){
   return array(
      'alert'           => '[alert style="warning"]Insert your content here[/alert]',
      'blockquote'      => '[blockquote author="" link="" target="_blank"]Insert your content here[/blockquote]',
      'button'          => '[button title="Title" icon="" icon_position="" link="" target="_blank" color="" font_color="" large="0" class="" download="" onclick=""]',
      'code'            => '[code]Insert your content here[/code]',
      'content_link'    => '[content_link title="" icon="icon-lamp" link="" target="_blank" class="" download=""]',
      'dropcap'         => '[dropcap background="" color="" circle="0"]I[/dropcap]nsert your content here',
      'fancy_link'      => '[fancy_link title="" link="" target="" style="1" class="" download=""]',
      'highlight'       => '[highlight background="" color=""]Insert your content here[/highlight]',
      'hr'              => '[hr height="30" style="default" line="default" themecolor="1"]',
      'icon_bar'        => '[icon_bar icon="icon-lamp" link="" target="_blank" size="" social=""]',
      'idea'            => '[idea]Insert your content here[/idea]',
      'image'           => '[image src="" align="" caption="" link="" link_image="" target="" alt="" border="0" greyscale="0" animate=""]',
      'progress_icons'  => '[progress_icons icon="fa-heart" count="5" active="3" background=""]',
      'tooltip'         => '[tooltip hint="Insert your hint here"]Insert your content here[/tooltip]',
      
   );
}

/* ---------------------------------------------------------------------------
 * Alert [alert] [/alert]
 * --------------------------------------------------------------------------- */
if( ! function_exists( 'sc_alert' ) ){
   function sc_alert( $attr, $content = null ){
      extract(shortcode_atts(array(
            'style'  => 'warning',
      ), $attr));
   ?>
      <div class="alert alert-<?php print $style ?>"><?php print do_shortcode( $content ); ?></div>
   <?php    
   }
}

/* ---------------------------------------------------------------------------
 * Button [button]
 * --------------------------------------------------------------------------- */
if( ! function_exists( 'sc_button' ) ){
   function sc_button( $attr, $content = null ){
      extract(shortcode_atts(array(
            'title'           => 'Read more',
            'icon'                  => '',
            'icon_position' => 'left',
            'link'                  => '',
            'target'          => '',
            'color'           => '',
            'font_color'      => '',
            'large'           => '',
            'class'           => '',
            'download'        => '',
            'onclick'         => '',
      ), $attr));
      
      // target
      if( $target ){
            $target = 'target="_blank"';
      } else {
            $target = false;
      }
      
      // download
      if( $download ){
            $download = 'download="'. $download .'"';
      } else {
            $download = false;
      }
      
      // onclick
      if( $onclick ){
            $onclick = 'onclick="'. $onclick .'"';
      } else {
            $onclick = false;
      }
      
      // icon_position
      if( $icon_position != 'left' ){
            $icon_position = 'right';
      }
      
      // class
      if( $icon )       $class .= ' button_'. $icon_position;
      if( $large )      $class .= ' button_large';
      
      // custom color
      $style = '';
      if( $color ){
            if( strpos($color, '#') === 0 ){
                  $style .= ' background-color:'. $color .' !important;';
            } else {
                  $class .= ' button_'. $color;
            }
      }
      if( $font_color ){
            $style .= ' color:'. $font_color .';';
      }
      if( $style ){
            $style = ' style="'. $style .'"';
      }
      
      $output = '<a class="button '. $class .' button_js" href="'. $link .'" '. $onclick .' '. $target .' '. $style .' '. $download .'>';
            if( $icon ) $output .= '<span class="button_icon"><i class="'. $icon .'"></i></span>';
            if( $title ) $output .= '<span class="button_label">'. $title .'</span>';
      $output .= '</a>'."\n";

    return $output;
   }
}

/* ---------------------------------------------------------------------------
 * Code [code] [/code]
 * --------------------------------------------------------------------------- */
if( !function_exists( 'sc_code' ) ){
      function sc_code( $attr, $content = null ){
            $output  = '<pre>';
                  $output .= do_shortcode(htmlspecialchars($content));
            $output .= '</pre>'."\n";
          return $output;
      }
}


/* ---------------------------------------------------------------------------
 * Content Link [content_link]
* --------------------------------------------------------------------------- */
if( ! function_exists( 'sc_content_link' ) ){
   function sc_content_link( $attr, $content = null ){
      extract(shortcode_atts(array(
            'title'     => '',
            'icon'            => '',
            'link'            => '',
            'target'    => '',
            'class'     => '',
            'download'  => '',
      ), $attr));

      // target
      if( $target ){
            $target = 'target="_blank"';
      } else {
            $target = false;
      }
      
      // download
      if( $download ){
            $download = 'download="'. $download .'"';
      } else {
            $download = false;
      }

      $output = '<a class="content_link '. $class .'" href="'. $link .'" '. $target .' '. $download .'>';
            if( $icon ) $output .= '<span class="icon"><i class="'. $icon .'"></i></span>';
            if( $title ) $output .= '<span class="title">'. $title .'</span>';
      $output .= '</a>';
      
      return $output;
   }
}

/* ---------------------------------------------------------------------------
 * Fancy Link [fancy_link]
* --------------------------------------------------------------------------- */
if( ! function_exists( 'sc_fancy_link' ) ){
   function sc_fancy_link( $attr, $content = null ){
      extract(shortcode_atts(array(
            'title'     => '',
            'link'      => '',
            'target'    => '',
            'style'     => '1',
            'class'     => '',
            'download'  => '',
      ), $attr));

      // target
      if( $target ){
            $target = 'target="_blank"';
      } else {
            $target = false;
      }
      
      // download
      if( $download ){
            $download = 'download="'. $download .'"';
      } else {
            $download = false;
      }

      $output = '<a class="mfn-link mfn-link-'. $style .' '. $class .'" href="'. $link .'" data-hover="'. $title .'" ontouchstart="this.classList.toggle(\'hover\');" '. $target .' '. $download .'>';
            $output .= '<span data-hover="'. $title .'">'. $title .'</span>';
      $output .= '</a>';
      
      return $output;
   }
}

/* ---------------------------------------------------------------------------
* Dropcap [dropcap] [/dropcap]
* --------------------------------------------------------------------------- */
if( ! function_exists( 'sc_dropcap' ) ){
   function sc_dropcap( $attr, $content = null ){
      extract(shortcode_atts(array(
            'background'      => '',
            'color'           => '',
            'circle'          => '',
      ), $attr));

      // circle
      if( $circle ){
            $class = ' dropcap_circle';
      } else {
            $class = false;
      }

      $style = '';
      if( $background ) $style .= 'background-color:'. $background .';';
      if( $color ) $style .= ' color:'. $color .';';
      if( $style ) $style = 'style="'. $style .'"';
            
      $output  = '<span class="dropcap'. $class .'" '. $style .'>';
            $output .= do_shortcode( $content );
      $output .= '</span>'."\n";

      return $output;
   }
}

/* ---------------------------------------------------------------------------
 * Highlight [highlight] [/highlight]
 * --------------------------------------------------------------------------- */
if( ! function_exists( 'sc_highlight' ) ){
   function sc_highlight( $attr, $content = null ){
      extract(shortcode_atts(array(
            'background'      => '',
            'color'           => '',
      ), $attr));

      // style
      $style = '';
      if( $background ) $style .= 'background-color:'. $background .';';
      if( $color ) $style .= ' color:'. $color .';';
      if( $style ) $style = 'style="'. $style .'"';
                                    
      $output  = '<span class="highlight" '. $style .'>';
            $output .= do_shortcode($content);
      $output .= '</span>'."\n";

      return $output;
   }
}



/* ---------------------------------------------------------------------------
 * Tooltip [tooltip] [/tooltip]
 * --------------------------------------------------------------------------- */
if( ! function_exists( 'sc_tooltip' ) ){
   function sc_tooltip( $attr, $content = null ){
      extract(shortcode_atts(array(
         'hint'   => '',
      ), $attr));

      $output = '';
      if( $hint ){
            $output .= '<span class="tooltip" data-tooltip="'. $hint .'" ontouchstart="this.classList.toggle(\'hover\');">';
                  $output .= do_shortcode( $content );
            $output .= '</span>'."\n";
      }

      return $output;
   }
}

/* ---------------------------------------------------------------------------
 * Progress Icons [progress_icons]
* --------------------------------------------------------------------------- */
if( ! function_exists( 'sc_progress_icons' ) )
{
   function sc_progress_icons( $attr, $content = null )
   {
      extract(shortcode_atts(array(
         'icon'         => 'icon-lamp',
         'count'     => 5,
         'active'       => 0,
         'background'   => '',
      ), $attr));
      
      $output = '<div class="progress_icons" data-active="'. $active .'" data-color="'. $background .'">';
         for ($i = 1; $i <= $count; $i++) {
            $output .= '<span class="progress_icon"><i class="'. $icon .'"></i></span>';
         }
      $output .= '</div>'."\n";
   
      return $output;
   }
}

add_shortcode( 'alert', 'sc_alert' );
add_shortcode( 'button', 'sc_button' );
add_shortcode( 'code', 'sc_code' );
add_shortcode( 'content_link', 'sc_content_link' );
add_shortcode( 'dropcap', 'sc_dropcap' );
add_shortcode( 'fancy_link', 'sc_fancy_link' );
add_shortcode( 'highlight', 'sc_highlight' );
add_shortcode( 'hr', 'sc_divider' );
add_shortcode( 'icon', 'sc_icon' );
add_shortcode( 'icon_bar', 'sc_icon_bar' );
add_shortcode( 'progress_icons', 'sc_progress_icons' );
add_shortcode( 'tooltip', 'sc_tooltip' );
