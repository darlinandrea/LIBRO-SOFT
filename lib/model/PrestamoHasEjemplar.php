<?PHP 
class PrestamoHasEjemplar{
	private $idPrestamo_Ejemplar;
	private $Prestamo_idPrestamo;
	private $Ejemplar_idEjemplar;
	private $con;
	public function __construct(&$db){
		$this->con = $db;
	}
	//Getters

	public function getId(){
		return $this->idPrestamo_Ejemplar;
	}
	public function getNombreId(){
		return "idPrestamo_Ejemplar";
	}
	public function getIdPrestamo_Ejemplar(){
		return $this->idPrestamo_Ejemplar;
	}
	public function getPrestamo_idPrestamo(){
		return $this->Prestamo_idPrestamo;
	}
	public function getEjemplar_idEjemplar(){
		return $this->Ejemplar_idEjemplar;
	}
	public function getByPrestamo($Prestamo_idPrestamo){
		return $this->listarObj(array("Prestamo_idPrestamo"=>$Prestamo_idPrestamo));
	}
	public function getPrestamo(){
		$prestamo = new Prestamo($this->con);
		$prestamo->cargarPorId($this->Prestamo_idPrestamo);
		return $prestamo;
	}
	public function getByEjemplar($Ejemplar_idEjemplar){
		return $this->listarObj(array("Ejemplar_idEjemplar"=>$Ejemplar_idEjemplar));
	}
	public function getEjemplar(){
		$ejemplar = new Ejemplar($this->con);
		$ejemplar->cargarPorId($this->Ejemplar_idEjemplar);
		return $ejemplar;
	}

	//Setters

	public function setIdPrestamo_Ejemplar($idPrestamo_Ejemplar){
		$this->idPrestamo_Ejemplar = $idPrestamo_Ejemplar;
	}
	public function setPrestamo_idPrestamo($Prestamo_idPrestamo){
		$this->Prestamo_idPrestamo = $Prestamo_idPrestamo;
	}
	public function setEjemplar_idEjemplar($Ejemplar_idEjemplar){
		$this->Ejemplar_idEjemplar = $Ejemplar_idEjemplar;
	}
	//LLena todos los atributos de la clase sacando los valores de un array
	function setValues($array){
		foreach($array as $key => $val)
			if(property_exists($this,$key))
				$this->$key = $val;
	}
	
	//Guarda o actualiza el objeto en la base de datos, la accion se determina por la clave primaria
	public function save(){
		if(empty($this->idPrestamo_Ejemplar)){			
			$this->idPrestamo_Ejemplar = $this->con->autoInsert(array(
			"Prestamo_idPrestamo" => $this->getPrestamo_idPrestamo(),
			"Ejemplar_idEjemplar" => $this->getEjemplar_idEjemplar(),
			),"prestamo_has_ejemplar");
			return;
		}
		return $this->con->autoUpdate(array(
			"Prestamo_idPrestamo" => $this->getPrestamo_idPrestamo(),
			"Ejemplar_idEjemplar" => $this->getEjemplar_idEjemplar(),
			),"prestamo_has_ejemplar","idPrestamo_Ejemplar=".$this->getId());
	}
    
	public function cargarPorId($idPrestamo_Ejemplar){
		if($idPrestamo_Ejemplar>0){
			$result = $this->con->query("SELECT * FROM `prestamo_has_ejemplar`  WHERE idPrestamo_Ejemplar=".$idPrestamo_Ejemplar);
			$this->idPrestamo_Ejemplar = $result[0]['idPrestamo_Ejemplar'];
			$this->Prestamo_idPrestamo = $result[0]['Prestamo_idPrestamo'];
			$this->Ejemplar_idEjemplar = $result[0]['Ejemplar_idEjemplar'];
		}
 	}
	public function listar($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false){
		$whereA = array();
		if(!$exactMatch){
			$campos = $this->con->query("DESCRIBE prestamo_has_ejemplar");
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
		$rows =$this->con->query("SELECT * FROM `prestamo_has_ejemplar`  WHERE $where $orderBy LIMIT $limit");
		$rowsI = array();
		foreach($rows as $row){
			$rowsI[$row["idPrestamo_Ejemplar"]] = $row;
		}
		return $rowsI;
	}
	//como listar, pero retorna un array de objetos
	function listarObj($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false){
		$rowsr = array();
		$rows = $this->listar($filtros, $orderBy, $limit, $exactMatch);
		foreach($rows as $row){
			$this->cargarPorId($row["idPrestamo_Ejemplar"]);
			$obj = clone $this;
			$rowsr[$row["idPrestamo_Ejemplar"]] = $obj;
		}
		return $rowsr;
	}
	public function eliminar(){
		return $this->con->query("DELETE FROM `prestamo_has_ejemplar`  WHERE idPrestamo_Ejemplar=".$this->getId());
	}
}
?>
