$(function(){
	$("#mostrarFormArea").bind("click", function(){
		if($("#formularioArea").css("display") == "none"){
			$("#formularioArea").slideDown();
		}else{
			$("#formularioArea").slideUp();
		}
	});	
	$("a[href*=delete]").live("click", function(){
		return confirm("Are you sure? Delete?");	
	});
	$('input').autoGrowInput({
		comfortZone: 24,
		minWidth: 150,
		maxWidth: 800
	});
	$("table thead").addClass("ui-widget-header").parent().children("tbody").addClass("ui-widget-content");
	$("table tr").live("mouseover", function(){$(this).addClass("ui-state-highlight");}).live("mouseout",function(){$(this).removeClass("ui-state-highlight");});
	$("table tr:even").addClass("alt");
	var tArea = $('#tArea').dataTable({
		/*"fnDrawCallback": function ( oSettings ) {
            if ( oSettings.aiDisplay.length == 0 )
            {
                return;
            }
             
            var nTrs = $('#tArea tbody tr');
            var iColspan = nTrs[0].getElementsByTagName('td').length;
            var sLastGroup = "";
            for ( var i=0 ; i<nTrs.length ; i++ )
            {
                var iDisplayIndex = oSettings._iDisplayStart + i;
				iDisplayIndex = i;//fix by jose.nobile at gmail dot com
				var sGroup = oSettings.aoData[ oSettings.aiDisplay[iDisplayIndex] ]._aData[4];
                if ( sGroup != sLastGroup ){
                    var nGroup = document.createElement( 'tr' );
                    var nCell = document.createElement( 'td' );
                    nCell.colSpan = iColspan;
                    nCell.className = "group";
                    nCell.innerHTML = sGroup;
                    nGroup.appendChild( nCell );
                    nTrs[i].parentNode.insertBefore( nGroup, nTrs[i] );
                    sLastGroup = sGroup;
                }
            }
        },*/
		//"aaSortingFixed": [[ 4, 'asc' ]],
		//"aaSorting": [[ 1, 'asc' ]],
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "index.php?ac=area",
		"bSearchable": true,
		"sScrollY": $(window).height()*0.99-377,
        "sDom": "frtiSHF",
        "bDeferRender": true,
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"sServerMethod": "POST",
		"aoColumns": [
			/*null,*/{"bVisible": false},
			null,
			null,	
			null,	
			{"bSortable": false, "mDataProp": null, "fnRender": function(o){return '<div style="display:block; width:120px;"><a id="areaEdit" href="index.php?ac=area&accion=editar&id='+o.aData[0]+'">Editar</a> '+'<a id="areaDelete" href="index.php?ac=area&accion=eliminar&'+o.oSettings.aoColumns[0].sTitle+'='+o.aData[0]+'">Eliminar</a></div>'; }}
		]
	}).columnFilter({ 	
		sPlaceHolder: "foot",
		sRangeSeparator: '~',
		aoColumns: [
			null,
			{type: "text"},
			{type: "text"},
			{type: "text"},
			null
		]
	});
});