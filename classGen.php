<?PHP 
//Class Generator
require_once "lib/DBNative.php";
//require_once 'frontend_tpl_conf.php';
$fileConf = realpath(dirname(__FILE__))."/config/databases.ini";
if (file_exists($fileConf) && is_readable($fileConf)) {
    $aSettings = parse_ini_file($fileConf, true);    
    define("DB_SERVER",         $aSettings["remote_database"]["server"]);
    define("DB_NAME",           $aSettings["remote_database"]["name"]);
    define("DB_USER",           $aSettings["remote_database"]["user"]);
    define("DB_PASS",           $aSettings["remote_database"]["password"]);
    define("DB_SERVER_LOCAL",   $aSettings["local_database"]["server"]);
    define("DB_NAME_LOCAL",     $aSettings["local_database"]["name"]);
    define("DB_USER_LOCAL",     $aSettings["local_database"]["user"]);
    define("DB_PASS_LOCAL",     $aSettings["local_database"]["password"]);
} else {
    die("File configuration was not found!");
}
if (in_array($_SERVER['SERVER_ADDR'], array("127.0.0.1", "localhost", "192.168.0.117","192.168.0.128","192.168.1.103","192.168.1.104"))){
	$mode = "local";
	define("DSN", "mysql://".DB_USER_LOCAL.":".DB_PASS_LOCAL."@".DB_SERVER_LOCAL."/".DB_NAME_LOCAL);
}
else{
	$mode = "remote";
	define("DSN", "mysql://".DB_USER.":".DB_PASS."@".DB_SERVER."/".DB_NAME);
}

$con =  DBNative::get(DSN);
$inicio = microtime(true);
function printCode($source_code)
    {

        if (is_array($source_code))
            return false;
       
        $source_code = explode("\n", str_replace(array("\r\n", "\r"), "\n", $source_code));
        $line_count = 1;
				$formatted_code = '';
        foreach ($source_code as $code_line)
        {
            $formatted_code .= '<tr><td>'.$line_count.'</td>';
            $line_count++;
           
            if (preg_match('/<\?(php)?[^[:graph:]]/', $code_line))
                $formatted_code .= '<td>'. str_replace(array('<code>', '</code>'), '', highlight_string($code_line, true)).'</td></tr>';
            else
                $formatted_code .= '<td>'.@ereg_replace('(&lt;\?php&nbsp;)+', '', str_replace(array('<code>', '</code>'), '', highlight_string('<?php '.$code_line, true))).'</td></tr>';
        }

        return '<table style="font: 1em Consolas, \'andale mono\', \'monotype.com\', \'lucida console\', monospace;">'.$formatted_code.'</table>';
    }


$tablas = $con->query("SHOW TABLES");
$contenidos = array();
foreach($tablas as $tabla)
{
	$tabla = $tabla["Tables_in_librosoft"];
	$campos = $con->query("DESCRIBE $tabla");
	//Poner en un array los campos pulpitos
	$camposP = array();
	$primarias = array();
	foreach($campos as $campo)
	{
		if($campo["Key"]!="PRI")
			$camposP[] = $campo["Field"];
		else{
			$primarias[] = $campo["Field"];
			$primary = $campo["Field"];
		}		
	}
	if(empty($primary)){
		echo "Saltando tabla {$tabla}: No tiene una clave primaria<br />";
		continue;
	}
	if(count($primarias)>1){
		echo "Saltando tabla {$tabla}: Contiene ".count($primarias). " campos (".implode(", ",$primarias).") como clave primaria 
		<a href='http://trac.propelorm.org/ticket/359'>Propel</a> - 
		<a href='http://docs.doctrine-project.org/projects/doctrine-orm/en/2.0.x/tutorials/composite-primary-keys.html'>Doctrine</a><br />";
		continue;
	}
	//Inteligencia artificial
	$create = $con->query("SHOW CREATE TABLE `$tabla`");
	if(!isset($create[0]["Create Table"])){
		echo "Saltando $tabla por que no es un base tabla<br />";	
		continue;
	}
	$lineas = explode("\n",$create[0]["Create Table"]);
	$foraneas = array();
	//echo "<pre>".print_r($lineas,true)."</pre>";
	foreach($lineas as $linea)
	{
		if(strpos($linea,"CONSTRAINT")!==false)//posicion cero
		{
			//Parsear
			$pos = strpos($linea,"FOREIGN KEY (`")+14;
			$tmp = substr($linea,$pos);
			$pos = strpos($tmp,"`) REFERENCES `");
			$campo = substr($tmp,0,$pos);//Listo el campo
			//echo $campo." --> ";
			$tmp = substr($tmp,$pos+15);
			$pos = strpos($tmp,"` (`");
			$tablatmp = substr($tmp,0,$pos);//Lista la tabla a la que referencia
			//echo $tabla.".";
			$campor = substr($tmp,$pos+4,strpos($tmp,"`) ")-($pos+4));
			//echo $campor."<br />";
			$foraneas[$campo] = array("tabla"=>$tablatmp,"campo" => $campor);
		}
	}

	ob_start();
	$clase = str_replace(" ","",ucwords(str_replace("_"," ",$tabla)));
?>

class <?PHP echo $clase;?>{
<?PHP
foreach($campos as $campo)
{
?>
	private $<?PHP echo $campo["Field"];?>;
<?PHP
}//foreach de campos
?>
	protected $con;
	public function __construct(){
		$this->con = DBNative::get();
	}
	//Getters

<?PHP
foreach($campos as $campo)
{
?><?PHP
if($campo["Field"]==$primary)//Un lindo "alias"
{
	if($campo["Field"] != "id"){
?>
	public function getId(){
		return $this-><?PHP echo $campo["Field"];?>;
	}<?PHP } ?>
	public function getNombreId(){
		return "<?PHP echo $campo["Field"];?>";
	}
<?PHP
}
?>
	public function get<?PHP echo str_replace(" ","",ucwords(str_replace("_"," ",$campo["Field"])));?>(){
		return $this-><?PHP echo $campo["Field"];?>;
	}
<?PHP
}//foreach de campos
$tablasForaneas = array();
foreach($foraneas as $campo => $foranea){
	$nombreCampo = str_replace(" ","",ucwords(str_replace("_"," ",$foranea["tabla"])));
	$c = @$tablasForaneas[$nombreCampo];
	@$tablasForaneas[$nombreCampo] += 1;
	if($c>0){
		$nombreCampo .= $c;	
	}
?>
	public function getBy<?PHP echo $nombreCampo;?>($<?PHP echo $campo;?>){
		return $this->listarObj(array("<?PHP echo $campo;?>"=>$<?PHP echo $campo;?>));
	}
	public function get<?PHP echo $nombreCampo;?>(){
		$<?PHP echo lcfirst($foranea["tabla"])?> = new <?PHP echo $nombreCampo;?>($this->con);
		$<?PHP echo lcfirst($foranea["tabla"])?>->cargarPorId($this-><?PHP echo $campo;?>);
		return $<?PHP echo lcfirst($foranea["tabla"])?>;
	}
<?PHP } ?>

	//Setters

<?PHP
foreach($campos as $campo){
?>
	public function set<?PHP echo str_replace(" ","",ucwords($campo["Field"]));?>($<?PHP echo $campo["Field"];?>){
		$this-><?PHP echo $campo["Field"];?> = $<?PHP echo $campo["Field"];?>;
	}
<?PHP
}//foreach de campos
?>
	//LLena todos los atributos de la clase sacando los valores de un array
	function setValues($array){
		foreach($array as $key => $val)
			if(property_exists($this,$key))
				$this->$key = $val;
	}
	
	//Guarda o actualiza el objeto en la base de datos, la accion se determina por la clave primaria
	public function save(){
		if(empty($this-><?PHP echo $primary;?>)){			
			$this-><?PHP echo $primary;?> = $this->con->autoInsert(array(
			<?PHP foreach($camposP as $campo){?>"<?PHP echo $campo;?>" => $this->get<?PHP echo str_replace(" ","",ucwords($campo));?>(),
			<?PHP }?>),"<?PHP echo $tabla;?>");
			return;
		}
		return $this->con->autoUpdate(array(
			<?PHP foreach($camposP as $campo){?>"<?PHP echo $campo;?>" => $this->get<?PHP echo str_replace(" ","",ucwords($campo));?>(),
			<?PHP }?>),"<?PHP echo $tabla;?>","<?PHP echo $primary?>=".$this->getId());
	}
    
	public function cargarPorId($<?PHP echo $primary;?>){
		if($<?PHP echo $primary;?>>0){
			$result = $this->con->query("SELECT * FROM `<?PHP echo $tabla;?>`  WHERE <?PHP echo $primary;?>=".$<?PHP echo $primary;?>);
<?PHP
foreach($campos as $campo)
{
?>
			$this-><?PHP echo $campo["Field"];?> = $result[0]['<?PHP echo $campo["Field"];?>'];
<?PHP
}//foreach de campos
?>
		}
 	}
	public function listar($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$whereA = array();
		if(!$exactMatch){
			$campos = $this->con->query("DESCRIBE <?PHP echo $tabla;?>");
			$listicos = array();
			foreach($campos as $campo){
				$tmp = explode("(",$campo["Type"]);
				$listicos[$campo["Field"]] = $tmp[0];
			}
			foreach($filtros as $filtro => $valor){
				if($listicos[$filtro] == "int")
					$whereA[] = $filtro." = ".floatval($valor);
				else
					$whereA[] = $filtro." LIKE '%".$this->con->escape($valor)."%'";			
			}

		}else{
			foreach($filtros as $filtro => $valor)
				$whereA[] = $filtro." = ".$this->con->quote($valor);
		}
		$where = implode(" AND ",$whereA);
		if($where == '')
			$where = 1;
		if ($orderBy != "")
			$orderBy = "ORDER BY $orderBy";
		$rows =$this->con->query("SELECT $fields,<?PHP echo $primary;?> FROM `<?PHP echo $tabla;?>`  WHERE $where $orderBy LIMIT $limit");
		$rowsI = array();
		foreach($rows as $row){
			$rowsI[$row["<?PHP echo $primary;?>"]] = $row;
		}
		return $rowsI;
	}
	//como listar, pero retorna un array de objetos
	function listarObj($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$rowsr = array();
		$rows = $this->listar($filtros, $orderBy, $limit, $exactMatch, $fields);
		foreach($rows as $row){
			$obj = clone $this;
			$obj->cargarPorId($row["<?PHP echo $primary;?>"]);
			$rowsr[$row["<?PHP echo $primary;?>"]] = $obj;
		}
		return $rowsr;
	}
	public function eliminar(){
		return $this->con->query("DELETE FROM `<?PHP echo $tabla;?>`  WHERE <?PHP echo $primary;?>=".$this->getId());
	}
}<?PHP
	$contenido = ob_get_contents();
	ob_end_clean();
	$contenidos[$clase] = $contenido;
}
$lineas = 0;
foreach($contenidos as $clase => $codigo){
	echo "<h2>$clase</h2>";
	if(class_exists($clase)){
		echo "Clase $clase ya existe, saltando<br />";
		continue;
	}
	$resp = eval($codigo);
	if($resp === false)
		die("Error al compilar el codigo, el codigo fue <br /><pre>".printCode("<?PHP ".$codigo)."</pre>");
	$codigo = "<"."?"."PHP"." ".$codigo."\r\n?".">";
	$ruta = "lib/model/{$clase}.php";
	file_put_contents($ruta,utf8_encode($codigo)) or die("Error al grabar $ruta");
	echo "Guardado $ruta <br />";
	$lineas += count(explode("\n",$codigo));
	//echo "<pre>".htmlentities($codigo,ENT_COMPAT,"UTF-8")."</pre>";
	echo "<br />";
}
$fin = microtime(true);
$total = $fin-$inicio;
echo "$lineas lineas generadas<br />";
echo "hecho en $total segundos";
?>