(function() {
	Z(".nav .brand").addEvent('click',function() {
		if(!Z(".nav ul").attr("class"))
		{
			Z().attr("class","open");
			Z(".short").addClass("open");
		}else{
			Z().attr("class","");
			Z(".short").removeClass("open");
		}
	});
	Z(".close").addEvent('click', function() {
		Z('.shade,#create').hide();
		Z('.shade,#view').hide();
	});
	Z(".create").addEvent('click', function() {
		Z('.shade,#create').show();
	});
	Z(".editbox li .more").addEvent('click', function() {
		Z(Z(this).next()).toggle();
		Z(".editbox li").css("background-color", "transparent");
		if(Z(this).html() == "+") {
			Z(this).html("-");			
		}else {
			Z(Z(this).parents()).css("background-color", "#e82");
			Z(this).html("+");
		}
	});
	Z(".editbox li").addEvent('click', function() {
		if(Z(this).children().length < 2) {
			Z(".editbox li").css("background-color", "transparent");			
			Z(this).css("background-color", "#e82");			
		}else {
			if( Z(Z().getChild( "ul" , this)[0]).css("display") == "none" ) {
				Z(".editbox li").css("background-color", "transparent");
				Z(this).css("background-color", "#e82");
			};
		}
	});
})();