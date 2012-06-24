// JavaScript Document
$(function() {
	// Agregar la capaciada de crecer a los inputs cuando se le agrege mucho
	// texto
	$('input').autoGrowInput({
		comfortZone : 24,
		minWidth : 150,
		maxWidth : 800
	});

	// Clear
	$(".clear").live("click", function() {
		$('[name]', $(this).parents("form")).val('');
		$("#result").html('')
	});

	// Selector de todos los thead que esten dentro de table para agregarles
	// unas clases que le dan estilos de JqueryUI
	$("table thead").addClass("ui-widget-header").parent().children("tbody")
			.addClass("ui-widget-content");

	// cambio de color en las filas dinamico por donde pase el mouse
	$("table tr").live("mouseover", function() {
		$(this).addClass("ui-state-highlight");
	}).live("mouseout", function() {
		$(this).removeClass("ui-state-highlight");
	});

	// efecto zebra estatico
	$("table tr:even").addClass("alt");
});