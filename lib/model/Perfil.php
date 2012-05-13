<?PHP 
class Perfil{
	private $idPerfil;
	private $nombre;
	private $con;
	public function __construct(&$db){
		$this->con = $db;
	}
	//Getters

	public function getId(){
		return $this->idPerfil;
	}
	public function getNombreId(){
		return "idPerfil";
	}
	public function getIdPerfil(){
		return $this->idPerfil;
	}
	public function getNombre(){
		return $this->nombre;
	}

	//Setters

	public function setIdPerfil($idPerfil){
		$this->idPerfil = $idPerfil;
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
		if(empty($this->idPerfil)){			
			$this->idPerfil = $this->con->autoInsert(array(
			"nombre" => $this->getNombre(),
			),"perfil");
			return;
		}
		return $this->con->autoUpdate(array(
			"nombre" => $this->getNombre(),
			),"perfil","idPerfil=".$this->getId());
	}
    
	public function cargarPorId($idPerfil){
		if($idPerfil>0){
			$result = $this->con->query("SELECT * FROM `perfil`  WHERE idPerfil=".$idPerfil);
			$this->idPerfil = $result[0]['idPerfil'];
			$this->nombre = $result[0]['nombre'];
		}
 	}
	public function listar($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false){
		$whereA = array();
		if(!$exactMatch){
			$campos = $this->con->query("DESCRIBE perfil");
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
		$rows =$this->con->query("SELECT * FROM `perfil`  WHERE $where $orderBy LIMIT $limit");
		$rowsI = array();
		foreach($rows as $row){
			$rowsI[$row["idPerfil"]] = $row;
		}
		return $rowsI;
	}
	//como listar, pero retorna un array de objetos
	function listarObj($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false){
		$rowsr = array();
		$rows = $this->listar($filtros, $orderBy, $limit, $exactMatch);
		foreach($rows as $row){
			$this->cargarPorId($row["idPerfil"]);
			$obj = clone $this;
			$rowsr[$row["idPerfil"]] = $obj;
		}
		return $rowsr;
	}
	public function eliminar(){
		return $this->con->query("DELETE FROM `perfil`  WHERE idPerfil=".$this->getId());
	}
}
?>