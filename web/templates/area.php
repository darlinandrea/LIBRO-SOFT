<?PHP 
$this->extend('layout');
$this->javascripts->add('LIBRO-SOFT/web/javascript/area.js');
echo $msg;
?><h1>Area</h1>

<input type="button" id="mostrarFormArea" value="Mostrar Formulario"  />

<form action="" method="post" enctype="application/x-www-form-urlencoded" id="formularioArea">
<input type="hidden" name="idArea" value="<?PHP echo $area["idArea"];?>" />
  <table width="200" border="0">
    <tr>
      <td>Area</td>
      <td><input type="text" name="area" id="area" value="<?PHP echo $area["area"];?>" /></td>
    </tr>
    <tr>
      <td>C&oacute;digo</td>
      <td><input type="text" name="codigo" id="codigo" value="<?PHP echo $area["codigo"];?>" /></td>
    </tr>
    <tr>
      <td>Descripci&oacute;n</td>
      <td><input type="text" name="descripcion" id="descripcion" value="<?PHP echo $area["descripcion"];?>" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="submit" value="Enviar" /><input type="button" class="clear" value="Limpiar"/></td>
    </tr>
  </table>
</form>

<div id="result">

</div>

<table id="tArea">
  <thead>
		<tr>
			<th>idArea</th>
			<th>Codigo</th>
			<th>Area</th>
			<th>Descripcion</th>
			<th>Action</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>idArea</th>
			<th>Codigo</th>
			<th>Area</th>
			<th>Descripcion</th>
			<th>Action</th>
		</tr>
	</tfoot>
    <tbody>
<?PHP
foreach($areas as $area){
?>
<tr>
	<td><?PHP echo $area["idArea"];?></td>
	<td><?PHP echo $area["codigo"];?></td>
    <td><?PHP echo $area["area"];?></td>
	<td><?PHP echo $area["descripcion"];?></td>
    <td><a href="?ac=area&accion=editar&id=<?PHP echo $area["idArea"];?>">Editar</a><a href="?ac=area&accion=eliminar&id=<?PHP echo $area["idArea"];?>">Eliminar</a></td>
</tr>
<?PHP
}
?>
</tbody>
</table>