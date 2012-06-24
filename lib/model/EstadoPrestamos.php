<?PHP 
class EstadoPrestamos{
	private $idEstados_Prestamos;
	private $estado;
	protected $con;
	public function __construct(){
		$this->con = DBNative::get();
	}
	//Getters

	public function getId(){
		return $this->idEstados_Prestamos;
	}	public function getNombreId(){
		return "idEstados_Prestamos";
	}
	public function getIdEstados_Prestamos(){
		return $this->idEstados_Prestamos;
	}
	public function getEstado(){
		return $this->estado;
	}

	//Setters

	public function setIdEstados_Prestamos($idEstados_Prestamos){
		$this->idEstados_Prestamos = $idEstados_Prestamos;
	}
	public function setEstado($estado){
		$this->estado = $estado;
	}
	//LLena todos los atributos de la clase sacando los valores de un array
	function setValues($array){
		foreach($array as $key => $val)
			if(property_exists($this,$key))
				$this->$key = $val;
	}
	
	//Guarda o actualiza el objeto en la base de datos, la accion se determina por la clave primaria
	public function save(){
		if(empty($this->idEstados_Prestamos)){			
			$this->idEstados_Prestamos = $this->con->autoInsert(array(
			"estado" => $this->getEstado(),
			),"estado_prestamos");
			return;
		}
		return $this->con->autoUpdate(array(
			"estado" => $this->getEstado(),
			),"estado_prestamos","idEstados_Prestamos=".$this->getId());
	}
    
	public function cargarPorId($idEstados_Prestamos){
		if($idEstados_Prestamos>0){
			$result = $this->con->query("SELECT * FROM `estado_prestamos`  WHERE idEstados_Prestamos=".$idEstados_Prestamos);
			$this->idEstados_Prestamos = $result[0]['idEstados_Prestamos'];
			$this->estado = $result[0]['estado'];
		}
 	}
	public function listar($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$whereA = array();
		if(!$exactMatch){
			$campos = $this->con->query("DESCRIBE estado_prestamos");
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
		$rows =$this->con->query("SELECT $fields,idEstados_Prestamos FROM `estado_prestamos`  WHERE $where $orderBy LIMIT $limit");
		$rowsI = array();
		foreach($rows as $row){
			$rowsI[$row["idEstados_Prestamos"]] = $row;
		}
		return $rowsI;
	}
	//como listar, pero retorna un array de objetos
	function listarObj($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$rowsr = array();
		$rows = $this->listar($filtros, $orderBy, $limit, $exactMatch, $fields);
		foreach($rows as $row){
			$obj = clone $this;
			$obj->cargarPorId($row["idEstados_Prestamos"]);
			$rowsr[$row["idEstados_Prestamos"]] = $obj;
		}
		return $rowsr;
	}
	public function eliminar(){
		return $this->con->query("DELETE FROM `estado_prestamos`  WHERE idEstados_Prestamos=".$this->getId());
	}
}
?>