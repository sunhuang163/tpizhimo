
$(function(){
	$("#side_switch").click(function(){
		$(".left").hide();
		$("#main").contents().find(".right_body").css('margin-left',0);
		$(this).hide();
		$("#side_switchl").show();
	})
})
$(function(){
	$("#side_switchl").click(function(){
		$(".left").show();
		$("#main").contents().find(".right_body").css('margin-left',200);
		$(this).hide();
		$("#side_switch").show();
	})
})
$(document).ready(function(){
 $("#top_nav li a ").click(function(){
	 console.log( $(this).hasClass("selected") );
   if( !$(this).hasClass("selected") ) {
	   $(this).parent().siblings().children("a").removeClass("selected");
	   $(this).addClass("selected");
	   $("#main").attr("src",$(this).attr("href"));
   }
   return false;
});
});

