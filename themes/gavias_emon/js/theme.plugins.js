(function ($) {
   "use strict";
  
   //------- OWL carousle init  ---------------
    jQuery(document).ready(function(){
      $('.init-carousel-owl').each(function(){
        var items = 4;
        var items_lg = 3;
        var items_md = 2;
        items = $(this).data('items');
        switch (items){
          case 1:
            items_lg = items_md = 1;
          break;
          case 2:
            items_lg = items_md = 2;
          break;
          case 3: 
            items_lg = 3; items_md = 2;
          break;
          case 4: 
            items_lg = 3; items_md = 2;
          break;
          case 5: 
            items_lg = 4; items_md = 2;
          break;
          case 6: 
            items_lg = 4; items_md = 2; 
          break;  
           default: items_lg = items - 2; items_md = items - 3;
        }
        
        $(this).owlCarousel({
          nav: true,
          autoplay: true,
          autoplayTimeout: 5000,
          smartSpeed: 350,
          navText: [ '<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>' ],
          autoHeight: false,
          loop: true,
          responsive : {
              0 : {
                  items: 1,
                  nav: false
              },
              640 : {
                 items : items_md
              },
              992: {
                  items : items_lg
              },
              1200: {
                  items: items
              }
          }
      });
   });
  
  if ($(window).width() > 780) {
    if ( $.fn.jpreLoader ) {
      var $preloader = $( '.js-preloader' );
      $preloader.jpreLoader({
        autoClose: true,
      }, function() {
        $preloader.addClass( 'preloader-done' );
        $( 'body' ).trigger( 'preloader-done' );
        $( window ).trigger( 'resize' );
      });
    }
  }else{
    $('body').removeClass('js-preloader');
  };

  var $container = $('.post-masonry-style');
  $container.imagesLoaded( function(){
      $container.masonry({
          itemSelector : '.item-masory',
          gutterWidth: 0,
          columnWidth: 1,
      }); 
  });

  if($('.post-masonry-style').length){
    $('.block-views').bind('DOMNodeInserted', function(event) {
      if($(this).find('.post-masonry-style').length){
        var $container = $('.post-masonry-style');
        $container.imagesLoaded( function(){
            $container.masonry({
                itemSelector : '.item-masory',
                gutterWidth: 0,
                columnWidth: 1,
            }); 
        });
      }  
    });
  }

  $('.gva-search-region .icon').on('click',function(e){
    if($(this).parent().hasClass('show')){
        $(this).parent().removeClass('show');
    }else{
        $(this).parent().addClass('show');
    }
    e.stopPropagation();
  })

/*-------------Milestone Counter----------*/
  jQuery('.milestone-block').each(function() {
    jQuery(this).appear(function() {
      var $endNum = parseInt(jQuery(this).find('.milestone-number').text());
      jQuery(this).find('.milestone-number').countTo({
        from: 0,
        to: $endNum,
        speed: 4000,
        refreshInterval: 60,
        formatter: function (value, options) {
          value = value.toFixed(options.decimals);
          value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
          return value;
        }
      });
    },{accX: 0, accY: 0});
  });


  // ==================================================================================
  // Offcavas
  // ==================================================================================
  $('#menu-bar').on('click',function(e){
    if($('.gva-navigation').hasClass('show-view')){
        $(this).removeClass('show-view');
        $('.gva-navigation').removeClass('show-view');
    }else{
        $(this).addClass('show-view');
       $('.gva-navigation').addClass('show-view'); 
    }

    e.stopPropagation();
  })

    /*========== Click Show Sub Menu ==========*/
   
    $('.gva-navigation a').on('click','.nav-plus',function(){
        if($(this).hasClass('nav-minus') == false){
            $(this).parent('a').parent('li').find('> ul').slideDown();
            $(this).addClass('nav-minus');
        }else{
            $(this).parent('a').parent('li').find('> ul').slideUp();
            $(this).removeClass('nav-minus');
        }
        return false;
    });

    if ( $.fn.isotope ) {
      $( '.isotope-items' ).each(function() {

        var $el = $( this ),
            $filter = $( '.portfolio-filter a' ),
            $loop =  $( this );

        $loop.isotope();

        $loop.imagesLoaded(function() {
          $loop.isotope( 'layout' );
        });

        if ( $filter.length > 0 ) {

          $filter.on( 'click', function( e ) {
            e.preventDefault();
            var $a = $(this);
            $filter.removeClass( 'active' );
            $a.addClass( 'active' );
            $loop.isotope({ filter: $a.data( 'filter' ) });
          });
        };
      });
    };

   //==== Customize =====
    $('.gavias-skins-panel .control-panel').click(function(){
        if($(this).parents('.gavias-skins-panel').hasClass('active')){
            $(this).parents('.gavias-skins-panel').removeClass('active');
        }else $(this).parents('.gavias-skins-panel').addClass('active');
    });

    $('.gavias-skins-panel .layout').click(function(){
        $('body').removeClass('wide-layout').removeClass('boxed');
        $('body').addClass($(this).data('layout'));
        $('.gavias-skins-panel .layout').removeClass('active');
        $(this).addClass('active');
        var $container = $('.post-masonry-style');
        $container.imagesLoaded( function(){
            $container.masonry({
                itemSelector : '.item-masory',
                gutterWidth: 0,
                columnWidth: 1,
            }); 
        });
    });

/*----------- Animation Progress Bars --------------------*/

  $("[data-progress-animation]").each(function() {
    var $this = $(this);
    $this.appear(function() {
      var delay = ($this.attr("data-appear-animation-delay") ? $this.attr("data-appear-animation-delay") : 1);
      if(delay > 1) $this.css("animation-delay", delay + "ms");
      setTimeout(function() { $this.animate({width: $this.attr("data-progress-animation")}, 800);}, delay);
    }, {accX: 0, accY: -50});
  });
  
 /*-------------------------------------------------------*/
      /* Video box
  /*-------------------------------------------------------*/

  if(jQuery('.gsc-video-link').length) {
    jQuery('.gsc-video-link').click(function(e) {
        e.preventDefault();
        var link = jQuery(this);
        
        var popup = jQuery('<div id="gsc-video-overlay"><a class="video-close" href="#close">&times;</a><iframe src="'+link.attr('data-url')+'" width="'+link.attr('data-width')+'" height="'+link.attr('data-height')+'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>');
        
        link.parent().append(popup);
        var video_element = popup.find('iframe');
        setTimeout(function() {
            video_element.addClass('loaded');
        }, 1000);

        popup.addClass('show');
        
        setTimeout(function() {
            popup.addClass('open');
        }, 50);
        
        popup.find('a[href="#close"]').click(function(e) {
            e.preventDefault();
            
            popup.removeClass('open');
            
            setTimeout(function() {
                popup.removeClass('show');
                popup.remove();
            }, 350);
        });
    });
  }

  // ============================================================================
  // Fixed top Menu Bar
  // ============================================================================
   if($('.gv-sticky-menu').length > 0){
      var sticky = new Waypoint.Sticky({
        element: $('.gv-sticky-menu')[0]
    });
   }  

});

})(jQuery);
