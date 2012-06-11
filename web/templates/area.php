<?PHP 
$this->extend('layout');
$this->javascripts->add('LIBRO-SOFT/web/javascript/area.js');
echo $msg;
?><h1>Area</h1>

<input type="button" id="mostrarFormArea" value="Mostrar Formulario"  />

<form action="" method="post" enctype="application/x-www-form-urlencoded" id="formularioArea">
  <table width="200" border="0">
    <tr>
      <td>Area</td>
      <td><input type="text" name="area" id="area" /></td>
    </tr>
    <tr>
      <td>C&oacute;digo</td>
      <td><input type="text" name="codigo" id="codigo" /></td>
    </tr>
    <tr>
      <td>Descripci&oacute;n</td>
      <td><input type="text" name="descripcion" id="descripcion" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="submit" value="Enviar" /></td>
    </tr>
  </table>
</form>