<?PHP 
class EstadoSolicitudCompra{
	private $idEstado_Solicitud_Compra;
	private $estado;
	private $con;
	public function __construct(&$db){
		$this->con = $db;
	}
	//Getters

	public function getId(){
		return $this->idEstado_Solicitud_Compra;
	}
	public function getNombreId(){
		return "idEstado_Solicitud_Compra";
	}
	public function getIdEstado_Solicitud_Compra(){
		return $this->idEstado_Solicitud_Compra;
	}
	public function getEstado(){
		return $this->estado;
	}

	//Setters

	public function setIdEstado_Solicitud_Compra($idEstado_Solicitud_Compra){
		$this->idEstado_Solicitud_Compra = $idEstado_Solicitud_Compra;
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
		if(empty($this->idEstado_Solicitud_Compra)){			
			$this->idEstado_Solicitud_Compra = $this->con->autoInsert(array(
			"estado" => $this->getEstado(),
			),"estado_solicitud_compra");
			return;
		}
		return $this->con->autoUpdate(array(
			"estado" => $this->getEstado(),
			),"estado_solicitud_compra","idEstado_Solicitud_Compra=".$this->getId());
	}
    
	public function cargarPorId($idEstado_Solicitud_Compra){
		if($idEstado_Solicitud_Compra>0){
			$result = $this->con->query("SELECT * FROM `estado_solicitud_compra`  WHERE idEstado_Solicitud_Compra=".$idEstado_Solicitud_Compra);
			$this->idEstado_Solicitud_Compra = $result[0]['idEstado_Solicitud_Compra'];
			$this->estado = $result[0]['estado'];
		}
 	}
	public function listar($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false){
		$whereA = array();
		if(!$exactMatch){
			$campos = $this->con->query("DESCRIBE estado_solicitud_compra");
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
		$rows =$this->con->query("SELECT * FROM `estado_solicitud_compra`  WHERE $where $orderBy LIMIT $limit");
		$rowsI = array();
		foreach($rows as $row){
			$rowsI[$row["idEstado_Solicitud_Compra"]] = $row;
		}
		return $rowsI;
	}
	//como listar, pero retorna un array de objetos
	function listarObj($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false){
		$rowsr = array();
		$rows = $this->listar($filtros, $orderBy, $limit, $exactMatch);
		foreach($rows as $row){
			$this->cargarPorId($row["idEstado_Solicitud_Compra"]);
			$obj = clone $this;
			$rowsr[$row["idEstado_Solicitud_Compra"]] = $obj;
		}
		return $rowsr;
	}
	public function eliminar(){
		return $this->con->query("DELETE FROM `estado_solicitud_compra`  WHERE idEstado_Solicitud_Compra=".$this->getId());
	}
}
?>
