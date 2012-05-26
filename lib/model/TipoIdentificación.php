<?PHP 
class TipoIdentificaci�n{
	private $idTipo_Identificaci�n;
	private $nombre;
	private $con;
	public function __construct(&$db){
		$this->con = $db;
	}
	//Getters

	public function getId(){
		return $this->idTipo_Identificaci�n;
	}
	public function getNombreId(){
		return "idTipo_Identificaci�n";
	}
	public function getIdTipo_Identificaci�n(){
		return $this->idTipo_Identificaci�n;
	}
	public function getNombre(){
		return $this->nombre;
	}

	//Setters

	public function setIdTipo_Identificaci�n($idTipo_Identificaci�n){
		$this->idTipo_Identificaci�n = $idTipo_Identificaci�n;
	}
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}
	//LLena todos los atributos de la clase sacando los valores de un array
	function setValues($array){
		foreach($array as $key => $val)
			if(property_exists($this,$key))
				$this->$key = $val;
	}
	
	//Guarda o actualiza el objeto en la base de datos, la accion se determina por la clave primaria
	public function save(){
		if(empty($this->idTipo_Identificaci�n)){			
			$this->idTipo_Identificaci�n = $this->con->autoInsert(array(
			"nombre" => $this->getNombre(),
			),"tipo_identificaci�n");
			return;
		}
		return $this->con->autoUpdate(array(
			"nombre" => $this->getNombre(),
			),"tipo_identificaci�n","idTipo_Identificaci�n=".$this->getId());
	}
    
	public function cargarPorId($idTipo_Identificaci�n){
		if($idTipo_Identificaci�n>0){
			$result = $this->con->query("SELECT * FROM `tipo_identificaci�n`  WHERE idTipo_Identificaci�n=".$idTipo_Identificaci�n);
			$this->idTipo_Identificaci�n = $result[0]['idTipo_Identificaci�n'];
			$this->nombre = $result[0]['nombre'];
		}
 	}
	public function listar($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false){
		$whereA = array();
		if(!$exactMatch){
			$campos = $this->con->query("DESCRIBE tipo_identificaci�n");
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
		$rows =$this->con->query("SELECT * FROM `tipo_identificaci�n`  WHERE $where $orderBy LIMIT $limit");
		$rowsI = array();
		foreach($rows as $row){
			$rowsI[$row["idTipo_Identificaci�n"]] = $row;
		}
		return $rowsI;
	}
	//como listar, pero retorna un array de objetos
	function listarObj($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false){
		$rowsr = array();
		$rows = $this->listar($filtros, $orderBy, $limit, $exactMatch);
		foreach($rows as $row){
			$this->cargarPorId($row["idTipo_Identificaci�n"]);
			$obj = clone $this;
			$rowsr[$row["idTipo_Identificaci�n"]] = $obj;
		}
		return $rowsr;
	}
	public function eliminar(){
		return $this->con->query("DELETE FROM `tipo_identificaci�n`  WHERE idTipo_Identificaci�n=".$this->getId());
	}
}
?>