$(function(){
	$("#mostrarFormCarrera").bind("click", function(){
		if($("#formularioCarrera").css("display") == "none"){
			$("#formularioCarrera").slideDown();
		}else{
			$("#formularioCarrera").slideUp();
		}
	});	
});