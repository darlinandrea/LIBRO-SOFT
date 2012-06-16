<?PHP 
$this->extend('layout');
$this->javascripts->add('LIBRO-SOFT/web/javascript/carrera.js');
echo $msg;
?><h1>Carrera</h1>

<input type="button" id="mostrarFormCarrera" value="Mostrar Formulario"  />

<form action="" method="post" enctype="application/x-www-form-urlencoded" id="formularioCarrera">
<input type="hidden" name="id" value="<?PHP echo $carrera["idCarrera"];?>" />
  <table width="200" border="0">
    <tr>
      <td>Carrera</td>
      <td><input type="text" name="carrera" id="carrera" value="<?PHP echo $carrera["carrera"];?>" /></td>
    </tr>
    <tr>
      <td>C&oacute;digo</td>
      <td><input type="text" name="codigo" id="codigo" value="<?PHP echo $carrera["codigo"];?>" /></td>
    </tr>
    <tr>
      <td>Descripci&oacute;n</td>
      <td><input type="text" name="descripcion" id="descripcion" value="<?PHP echo $carrera["descripcion"];?>" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="submit" value="Enviar" /></td>
    </tr>
  </table>
</form>

<table>
<tr>
	<th>C&oacute;digo</th>
    <th>Carrera</th>
</tr>
<?PHP
foreach($carreras as $carrera){
?>
<tr>
	<td><?PHP echo $carrera["codigo"];?></td>
    <td><?PHP echo $carrera["carrera"];?></td>
    <td><a href="?ac=carrera&accion=editar&id=<?PHP echo $carrera["idArea"];?>">Editar</a></td>
    <td><a href="?ac=carrera&accion=eliminar&id=<?PHP echo $carrera["idArea"];?>">Eliminar</a></td>
</tr>
<?PHP
}
?>
</table>