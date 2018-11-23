require(['jquery', 'jquery/ui'], function($){
	$(document).ready(function(){
		if ($(window).width() > 767) {
			$('.block-search .label').click(function(){
				//alert($(window).width());
				//alert("Hello");
				$(this).siblings(".control").toggleClass("open");
			});
		}		
	});
});