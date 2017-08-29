<?php
    $customize = (array)json_decode($json, true);
    if($customize):
    ?>
<style>
    body{}
    <?php //================= Theme color primary ================== ?>
    <?php if(isset($customize['theme_color']) && $customize['theme_color']){ ?>
        .breadcrumbs nav.breadcrumb ol > li a:hover,
        .footer a:hover,
        .footer.footer-v2 a:hover,
        .post-block .post-categories a,
        .video-block .post-image a:before,
        .video-block .post-title a:hover ,
        .style-dark .post-block .post-title a:hover,
        .post-slider.post-block.v2 .post-meta-wrap .post-title a:hover,
        .post-style-stick.v3 .item-list > ul > li:first-child .post-block .post-title a:hover,
        .list-highlight-post .item-list ul li .post-block .post-meta-wrap * a:hover,
        .post-style-list.bg-black .post-block .post-title a:hover,
        .portfolio-v1 .content .title a:hover,
        .portfolio-v1 .content a:hover,
        .testimonial-carousel .testimonial-v1 .content-inner .title,
        .testimonial-carousel .testimonial-v2 .content-inner .title,
        .nav-tabs > li > a:hover, .nav-tabs > li > a:focus, .nav-tabs > li > a:active,
        .nav-tabs > li.active > a, .nav-tabs > li > a.active,
        .bean-tab .nav-tabs > li.active > a ,
        .gavias-metro-box .gavias-icon-boxed i,
        .wrap-block-f-col .wrap-icon .inner-icon,
        .wrap-block-f-col:hover h3,
        .block .block-title,
        .block .block-title > span,
        .block.style-higlight .more-link a:hover,
        .block.block-blocktabs .ui-tabs-nav > li.ui-tabs-active > a,
        nav.breadcrumb ol > li a:hover,
        .navigation .gva_menu > li > a:hover, .navigation .gva_menu > li > a.is-active,
        .navigation .gva_menu .sub-menu li a:hover,
        .gva-navigation .gva_menu > li > a:hover,
        .gva-navigation .gva_menu li a:hover,
        .gva-mega-menu .block-blocktabs .ui-tabs-nav > li.ui-tabs-active > a,
        .view-featured-videos .video-block .video-content .video-title a:hover,
        .view-featured-videos .video-block .video-content .icon a:hover,
        .widget.gsc-heading .title-icon,
        .widget.gsc-team .team-position,
        .widget.gsc-icon-box.top-center .highlight-icon i,
        .widget.gsc-icon-box.top-left .highlight-icon i,
        .widget.gsc-icon-box.top-right .highlight-icon i,
        .widget.gsc-icon-box.left .highlight-icon i,
        .widget.milestone-block .milestone-icon i,
        .gsc-hover-background .front .icon
        {
          color: <?php echo $customize['theme_color'] ?>!important;
        }

        .pager .paginations a.active,
        .wrap-block-f-col:hover .wrap-icon .inner-icon,
        .view-featured-videos .video-block .video-content .icon a:hover,
        .gavias-slider .btn-slide.btn-slide-flat,
        .widget.gsc-icon-box:hover .highlight-icon
        {
          border-color: <?php echo $customize['theme_color'] ?>!important;
        }

        .nav-tabs > li > a:hover, .nav-tabs > li > a:focus, .nav-tabs > li > a:active,
        .nav-tabs > li.active > a, .nav-tabs > li > a.active
        {
            border-left-color: <?php echo $customize['theme_color'] ?>!important;
        }

        .nav-tabs > li > a:hover, .nav-tabs > li > a:focus, .nav-tabs > li > a:active,
        .nav-tabs > li.active > a, .nav-tabs > li > a.active
        {
            border-right-color: <?php echo $customize['theme_color'] ?>!important;
        }

        .circle{
            border-top-color: <?php echo $customize['theme_color'] ?>!important;
        }

        .widget.gsc-icon-box:hover .highlight-icon{
            -webkit-box-shadow: 0px 0px 8px 0px <?php echo $customize['theme_color'] ?>!important;
            box-shadow: 0px 0px 8px 0px <?php echo $customize['theme_color'] ?>!important;
        }

        .pager .paginations a.active,
        #edit-preview, #edit-submit,
        header.header-v2 .gva_menu,
        header.header-v3 .topbar,
        .breaking-news .title,
        .contact-message-form .form-actions #edit-preview, .contact-message-form .form-actions #edit-submit,
        .portfolio-v1 .content .title a:after,
        .btn,
        .progress .progress-bar,
        .pricing-table:hover .plan-name,
        .pricing-table.highlight-plan .plan-name,
        .gavias-metro-box .flipper .back,
        .gavias-metro-box .flipper .back h3,
        .wrap-block-f-col:hover .wrap-icon .inner-icon,
        #node-single-comment h2:before,
        .block.block-simplenews input#edit-subscribe,
        .list-tags .view-list ul > li:hover,
        .poll .poll-item .bar .foreground,
        .gavias-slider .btn-slide.btn-slide-flat,
        .gavias-slider .slick-dots > li.slick-active,
        .gavias-slider .slick-prev:hover, .gavias-slider .slick-next:hover,
        .widget.gsc-call-to-action .button-action a,
        .widget.gsc-team .social-icons a:hover,
        .widget.gsc-team.team-horizontal .team-name:after,
        .widget.gsc-box-image .body .icon
        {
          background-color: <?php echo $customize['theme_color'] ?>!important;
        }
    <?php } ?>     


    <?php //================= Body page ===================== ?>
    <?php if(isset($customize['text_color']) && $customize['text_color']){ ?>
        body .body-page{
            color: <?php echo $customize['text_color'] ?>;
        }
    <?php } ?>

    <?php if(isset($customize['link_color']) && $customize['link_color']){ ?>
        body .body-page a{
            color: <?php echo $customize['link_color'] ?>!important;
        }
    <?php } ?>

    <?php if(isset($customize['link_hover_color']) && $customize['link_hover_color']){ ?>
        body .body-page a:hover{
            color: <?php echo $customize['link_hover_color']?>!important;
        }
    <?php } ?>

    <?php //===================Header=================== ?>
    <?php if(isset($customize['header_bg']) && $customize['header_bg']){ ?>
        header .header-main{
            background: <?php echo $customize['header_bg'] ?>!important;
        }
    <?php } ?>

    <?php if(isset($customize['header_color_link']) && $customize['header_color_link']){ ?>
        header .header-main a{
            color: <?php echo $customize['header_color_link'] ?>!important;
        }
    <?php } ?>

    <?php if(isset($customize['header_color_link_hover']) && $customize['header_color_link_hover']){ ?>
        header .header-main a:hover{
            color: <?php echo $customize['header_color_link_hover'] ?>!important;
        }
    <?php } ?>

    <?php //===================Menu=================== ?>
    <?php if(isset($customize['menu_bg']) && $customize['menu_bg']){ ?>
        .main-menu, ul.gva_menu{
            background: <?php echo $customize['menu_bg'] ?>!important;
        }
    <?php } ?> 

    <?php if(isset($customize['menu_color_link']) && $customize['menu_color_link']){ ?>
        .main-menu ul.gva_menu > li > a{
            color: <?php echo $customize['menu_color_link'] ?>!important;
        }
    <?php } ?> 

    <?php if(isset($customize['menu_color_link_hover']) && $customize['menu_color_link_hover']){ ?>
        .main-menu ul.gva_menu > li > a:hover{
            color: <?php echo $customize['menu_color_link_hover'] ?>!important;
        }
    <?php } ?> 

    <?php if(isset($customize['submenu_background']) && $customize['submenu_background']){ ?>
        .main-menu .sub-menu{
            background: <?php echo $customize['submenu_background'] ?>!important;
            color: <?php echo $customize['submenu_color'] ?>!important;
        }
    <?php } ?> 

    <?php if(isset($customize['submenu_color']) && $customize['submenu_color']){ ?>
        .main-menu .sub-menu{
            color: <?php echo $customize['submenu_color'] ?>!important;
        }
    <?php } ?> 

    <?php if(isset($customize['submenu_color_link']) && $customize['submenu_color_link']){ ?>
        .main-menu .sub-menu a{
            color: <?php echo $customize['submenu_color_link'] ?>!important;
        }
    <?php } ?> 

    <?php if(isset($customize['submenu_color_link_hover']) && $customize['submenu_color_link_hover']){ ?>
        .main-menu .sub-menu a:hover{
            color: <?php echo $customize['submenu_color_link_hover'] ?>!important;
        }
    <?php } ?> 

     <?php //===================Footer=================== ?>
    <?php if(isset($customize['footer_bg']) && $customize['footer_bg']){ ?>
        .footer{
            background: <?php echo $customize['footer_bg'] ?>!important;
        }
    <?php } ?>

     <?php if(isset($customize['footer_color']) && $customize['footer_color']){ ?>
        .footer{
            color: <?php echo $customize['footer_color'] ?> !important;
        }
    <?php } ?>

    <?php if(isset($customize['footer_color_link']) && $customize['footer_color_link']){ ?>
        .footer ul.menu > li a::after, .footer a{
            color: <?php echo $customize['footer_color_link'] ?>!important;
        }
    <?php } ?>    

    <?php if(isset($customize['footer_color_link_hover']) && $customize['footer_color_link_hover']){ ?>
        .footer a:hover{
            color: <?php echo $customize['footer_color_link_hover'] ?> !important;
        }
    <?php } ?>    

    <?php //===================Copyright======================= ?>
    <?php if(isset($customize['copyright_bg']) && $customize['copyright_bg']){ ?>
        .copyright{
            background: <?php echo $customize['copyright_bg'] ?> !important;
        }
    <?php } ?>

     <?php if(isset($customize['copyright_color']) && $customize['copyright_color']){ ?>
        .copyright{
            color: <?php echo $customize['copyright_color'] ?> !important;
        }
    <?php } ?>

    <?php if(isset($customize['copyright_color_link']) && $customize['copyright_color_link']){ ?>
        .copyright a{
            color: <?php echo $customize['copyright_color_link'] ?>!important;
        }
    <?php } ?>    

    <?php if(isset($customize['copyright_color_link_hover']) && $customize['copyright_color_link_hover']){ ?>
        .copyright a:hover{
            color: <?php echo $customize['copyright_color_link_hover'] ?> !important;
        }
    <?php } ?>  
</style>
<?php 
  
    // \Drupal::configFactory()->getEditable('gavias_emon.setting')
    // ->set('gavias_emon_customize_json', $json)
    // ->save();
endif ?>
