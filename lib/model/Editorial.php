<?PHP 
class Editorial{
	private $idEditorial;
	private $editorial;
	private $codigo;
	private $pais_origen;
	protected $con;
	public function __construct(){
		$this->con = DBNative::get();
	}
	//Getters

	public function getId(){
		return $this->idEditorial;
	}	public function getNombreId(){
		return "idEditorial";
	}
	public function getIdEditorial(){
		return $this->idEditorial;
	}
	public function getEditorial(){
		return $this->editorial;
	}
	public function getCodigo(){
		return $this->codigo;
	}
	public function getPais_origen(){
		return $this->pais_origen;
	}

	//Setters

	public function setIdEditorial($idEditorial){
		$this->idEditorial = $idEditorial;
	}
	public function setEditorial($editorial){
		$this->editorial = $editorial;
	}
	public function setCodigo($codigo){
		$this->codigo = $codigo;
	}
	public function setPais_origen($pais_origen){
		$this->pais_origen = $pais_origen;
	}
	//LLena todos los atributos de la clase sacando los valores de un array
	function setValues($array){
		foreach($array as $key => $val)
			if(property_exists($this,$key))
				$this->$key = $val;
	}
	
	//Guarda o actualiza el objeto en la base de datos, la accion se determina por la clave primaria
	public function save(){
		if(empty($this->idEditorial)){			
			$this->idEditorial = $this->con->autoInsert(array(
			"editorial" => $this->getEditorial(),
			"codigo" => $this->getCodigo(),
			"pais_origen" => $this->getPais_origen(),
			),"editorial");
			return;
		}
		return $this->con->autoUpdate(array(
			"editorial" => $this->getEditorial(),
			"codigo" => $this->getCodigo(),
			"pais_origen" => $this->getPais_origen(),
			),"editorial","idEditorial=".$this->getId());
	}
    
	public function cargarPorId($idEditorial){
		if($idEditorial>0){
			$result = $this->con->query("SELECT * FROM `editorial`  WHERE idEditorial=".$idEditorial);
			$this->idEditorial = $result[0]['idEditorial'];
			$this->editorial = $result[0]['editorial'];
			$this->codigo = $result[0]['codigo'];
			$this->pais_origen = $result[0]['pais_origen'];
		}
 	}
	public function listar($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$whereA = array();
		if(!$exactMatch){
			$campos = $this->con->query("DESCRIBE editorial");
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
		$rows =$this->con->query("SELECT $fields,idEditorial FROM `editorial`  WHERE $where $orderBy LIMIT $limit");
		$rowsI = array();
		foreach($rows as $row){
			$rowsI[$row["idEditorial"]] = $row;
		}
		return $rowsI;
	}
	//como listar, pero retorna un array de objetos
	function listarObj($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$rowsr = array();
		$rows = $this->listar($filtros, $orderBy, $limit, $exactMatch, $fields);
		foreach($rows as $row){
			$obj = clone $this;
			$obj->cargarPorId($row["idEditorial"]);
			$rowsr[$row["idEditorial"]] = $obj;
		}
		return $rowsr;
	}
	public function eliminar(){
		return $this->con->query("DELETE FROM `editorial`  WHERE idEditorial=".$this->getId());
	}
}
?>