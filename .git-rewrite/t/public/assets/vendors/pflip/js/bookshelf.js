(function($){
	
////////////////////////////////////////////////////////////////////////////////

  function BookShelf(instance,options){	  
	  this.element=instance
	  this.init();
	  this.addWrap(); 
   }
   
////////////////////////////////////////////////////////////////////////////////   
   
   BookShelf.prototype.addWrap = function () {	
	   
	   //reset wrap 
	   $(".bs-shelf-background",this.element).remove();
	   $(".bs-shelf",this.element).contents().unwrap();
	
	   //add Wrap
       var index=0;
       $(".bs-shelf-image",this.element).each(function() {
	      ++index;
		   
          if($(this).prev().length > 0) {			  
             if($(this).offset().top != $(this).prev().offset().top) {	
			    index--;
				return false;
			}}
        });	  
 
       var images = $(".bs-shelf-image",this.element);
       for(var i = 0; i < images.length; i+=index) {
          images.slice(i, i+index).wrapAll("<div class='bs-shelf'></div>");
		 
       }
	   
	   //add background
	    $('.bs-shelf',this.element).prepend("<div class='bs-shelf-background'><div class='bs-rectangle'></div></div>");
		
		//add mask
		$('.bs-textbox',this.element).wrap("<div class='bs-mask'></div>");
		

}
   
/////////////////////////////////////////////////////////////////////////////////   
   
  
   BookShelf.prototype.init = function () {
        var _this=this;
		$(window).bind('orientationchange resize', function(event){
     	   _this.addWrap()
		});
		
		
		//mouse over
		$("img",this.element).bind('mouseenter',function(){									
			var _bs_shelf_image=$(this).parent();
			if( _bs_shelf_image.attr('class')!="bs-shelf-image" ){
				_bs_shelf_image=_bs_shelf_image.parent();
			}
						
			var textbox=$('.bs-textbox',_bs_shelf_image);
			var textbox_height=textbox.innerHeight();
			var img=$(this);
			var img_width=img.width();
			var img_height=img.height();
			var img_top=img.position().top;
			textbox.css('width',img_width+'px')
            textbox.css('bottom',-textbox_height+'px');
				textbox.animate(
			            {
						  opacity: "1",
						  bottom:0+"px"
						  
						},
						200
				)	
		 })
			 
		///mouse out 
		$("img",this.element).bind('mouseleave',function(){
			var _bs_shelf_image=$(this).parent();
			if( _bs_shelf_image.attr('class')!="bs-shelf-image" ){
				_bs_shelf_image=_bs_shelf_image.parent();
			}			  
			var textbox=$('.bs-textbox',_bs_shelf_image);
			var img=$(this);
	    	textbox.animate(
			            {
						  opacity: "1",
						  bottom:-textbox.innerHeight()+"px"
						    
						},
						200
						)			
		    })
    };
	
	
/////////////////////////////////////////////////////////////////////////////////	


  $.fn.bookshelf = function( options ) {
	  
        var settings = $.extend({
            param         : 'param',
        }, options);				

        return this.each( function() {           
			new BookShelf(this,settings);			
        });

    }
	
///////////////////////////////////////////////////////////////////////////////////	


})(jQuery)



 
