$(function(){
	$("#mostrarFormArea").bind("click", function(){
		if($("#formularioArea").css("display") == "none"){
			$("#formularioArea").slideDown();
		}else{
			$("#formularioArea").slideUp();
		}
	});	
});