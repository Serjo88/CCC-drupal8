jQuery(document).ready(function () {
	
   // Expanding lists
   if( jQuery('.expanding-list') ){
	   

      jQuery('.expanded-list-items ul li').on('click', function(e){
         e.preventDefault();
         e.stopPropagation();

         var id = jQuery(this).attr('id').split('-')[2];
         var detailBlock = jQuery('.list-item-detail#list-detail-' + id);

         var isVisible = detailBlock.is(':visible');

         if( !isVisible ) {
            if( jQuery('.expanding-list').hasClass('twocol') ) {
               jQuery('.list-item-detail').hide();
            } else {
               jQuery('.list-item-detail').slideUp(300);
            }

            if( jQuery('.expanding-list').hasClass('twocol') ) {
               detailBlock.fadeIn(300);
            } else {
               detailBlock.slideDown(300);
            }
         }
      });

   } 

});