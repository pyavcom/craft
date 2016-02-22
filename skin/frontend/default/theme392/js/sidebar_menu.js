jQuery(document).ready(function(){
  jQuery('#nav li').hover(
    function(){
        jQuery(this).addClass("hover");
    },
    function(){
        jQuery(this).removeClass("hover");
    }
  );  
  jQuery('#nav ul').each(function(){
      jQuery(this).parent().addClass('items');
  });  
  var is_playing = false;  
  jQuery('#nav > li, #nav > li > ul > li').not('.active').each(function(){
      jQuery(this).on("mouseenter mouseleave", function(){
         if(jQuery(this).hasClass('items')){
             var counter = 30;
             function slide_check(this_button){
                 if(counter > 0){
                     if(this_button.hasClass('hover')){
                         if(is_playing == false){
                            is_playing = true;                          
                            this_button.children('ul').slideDown(500, function(){is_playing = false});
                            counter = counter - 10;
                         }
                     } else{
                            if(is_playing == false){
//                                is_playing = true;
//                                this_button.children('ul').slideUp(1000, function(){is_playing = false});
                             }
                         }
                    counter--;
                    setTimeout(function(){slide_check(this_button)}, 1000);
                 }               
              }
              slide_check(jQuery(this));          
          }       
      });
  });
});