<?PHP 
class Area{
	private $idArea;
	private $area;
	private $codigo;
	private $descripcion;
	private $con;
	public function __construct(&$db){
		$this->con = $db;
	}
	//Getters

	public function getId(){
		return $this->idArea;
	}
	public function getNombreId(){
		return "idArea";
	}
	public function getIdArea(){
		return $this->idArea;
	}
	public function getArea(){
		return $this->area;
	}
	public function getCodigo(){
		return $this->codigo;
	}
	public function getDescripcion(){
		return $this->descripcion;
	}

	//Setters

	public function setIdArea($idArea){
		$this->idArea = $idArea;
	}
	public function setArea($area){
		$this->area = $area;
	}
	public function setCodigo($codigo){
		$this->codigo = $codigo;
	}
	public function setDescripcion($descripcion){
		$this->descripcion = $descripcion;
	}
	//LLena todos los atributos de la clase sacando los valores de un array
	function setValues($array){
		foreach($array as $key => $val)
			if(property_exists($this,$key))
				$this->$key = $val;
	}
	
	//Guarda o actualiza el objeto en la base de datos, la accion se determina por la clave primaria
	public function save(){
		if(empty($this->idArea)){			
			$this->idArea = $this->con->autoInsert(array(
			"area" => $this->getArea(),
			"codigo" => $this->getCodigo(),
			"descripcion" => $this->getDescripcion(),
			),"area");
			return;
		}
		return $this->con->autoUpdate(array(
			"area" => $this->getArea(),
			"codigo" => $this->getCodigo(),
			"descripcion" => $this->getDescripcion(),
			),"area","idArea=".$this->getId());
	}
    
	public function cargarPorId($idArea){
		if($idArea>0){
			$result = $this->con->query("SELECT * FROM `area`  WHERE idArea=".$idArea);
			$this->idArea = $result[0]['idArea'];
			$this->area = $result[0]['area'];
			$this->codigo = $result[0]['codigo'];
			$this->descripcion = $result[0]['descripcion'];
		}
 	}
	public function listar($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false){
		$whereA = array();
		if(!$exactMatch){
			$campos = $this->con->query("DESCRIBE area");
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
		$rows =$this->con->query("SELECT * FROM `area`  WHERE $where $orderBy LIMIT $limit");
		$rowsI = array();
		foreach($rows as $row){
			$rowsI[$row["idArea"]] = $row;
		}
		return $rowsI;
	}
	//como listar, pero retorna un array de objetos
	function listarObj($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false){
		$rowsr = array();
		$rows = $this->listar($filtros, $orderBy, $limit, $exactMatch);
		foreach($rows as $row){
			$this->cargarPorId($row["idArea"]);
			$obj = clone $this;
			$rowsr[$row["idArea"]] = $obj;
		}
		return $rowsr;
	}
	public function eliminar(){
		return $this->con->query("DELETE FROM `area`  WHERE idArea=".$this->getId());
	}
}
?>
