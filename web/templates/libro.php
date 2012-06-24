<?PHP 
$this->extend('layout');
$this->javascripts->add('LIBRO-SOFT/web/javascript/libro.js');
echo $msg;
?>
<h1>Area</h1>
<input type="button" id="mostrarFormArea" value="Mostrar Formulario"  />
<form action="" method="post" enctype="application/x-www-form-urlencoded" id="formularioArea">
  <input type="hidden" name="idArea" value="<?PHP echo $area["idArea"];?>" />
  <table width="200" border="0">
    <tr>
      <td>Titulo</td>
      <td><input type="text" name="area" id="area" value="<?PHP echo $area["area"];?>" /></td>
    </tr>
    <tr>
      <td>ISBN</td>
      <td><input type="text" name="codigo" id="codigo" value="<?PHP echo $area["codigo"];?>" /></td>
    </tr>
    <tr>
      <td>Año Publicación</td>
      <td><input type="text" name="descripcion" id="descripcion" value="<?PHP echo $area["descripcion"];?>" /></td>
    </tr>
    <tr>
      <td>Área de Conocimiento</td>
      <td><select name="id_area_conocimiento">
          <option value="">[Seleccione una]</option>
          <?PHP
	  foreach($areas as $area){
	  ?>
          <option<?PHP if($area["idArea"] == $libro["id_area_conocimiento"]){ ?> selected="selected"<?PHP } ?> value="<?PHP echo $area["idArea"];?>"><?PHP echo $area["area"];?></option>
          <?PHP
	  }
	  ?>
        </select></td>
    </tr>
    <tr>
      <td>Autor</td>
      <td><select name="id_area_conocimiento">
          <option value="">[Seleccione una]</option>
          <?PHP
	  foreach($areas as $area){
	  ?>
          <option<?PHP if($area["idArea"] == $libro["id_area_conocimiento"]){ ?> selected="selected"<?PHP } ?> value="<?PHP echo $area["idArea"];?>"><?PHP echo $area["area"];?></option>
          <?PHP
	  }
	  ?>
        </select></td>
    </tr>
    <tr>
      <td>Idioma</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Palabras Claves</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Editorial</td>
      <td><select name="id_area_conocimiento">
          <option value="">[Seleccione una]</option>
          <?PHP
	  foreach($areas as $area){
	  ?>
          <option<?PHP if($area["idArea"] == $libro["id_area_conocimiento"]){ ?> selected="selected"<?PHP } ?> value="<?PHP echo $area["idArea"];?>"><?PHP echo $area["area"];?></option>
          <?PHP
	  }
	  ?>
        </select></td>
    </tr>
    <tr>
      <td>Caratula</td>
      <td><input type="file" name="caratula" /></td>
    </tr>
    <tr>
      <td>Archivo</td>
      <td><input type="file" name="archivo" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="submit" value="Enviar" />
        <input type="button" class="clear" value="Limpiar"/></td>
    </tr>
  </table>
</form>
<div id="result"> </div>
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
