<?PHP 
class Multa{
	private $idMulta;
	private $id_ejemplar;
	private $id_prestamo;
	private $id_estado;
	private $multa;
	private $fecha_pago;
	protected $con;
	public function __construct(){
		$this->con = DBNative::get();
	}
	//Getters

	public function getId(){
		return $this->idMulta;
	}	public function getNombreId(){
		return "idMulta";
	}
	public function getIdMulta(){
		return $this->idMulta;
	}
	public function getId_ejemplar(){
		return $this->id_ejemplar;
	}
	public function getId_prestamo(){
		return $this->id_prestamo;
	}
	public function getId_estado(){
		return $this->id_estado;
	}
	public function getMulta(){
		return $this->multa;
	}
	public function getFecha_pago(){
		return $this->fecha_pago;
	}
	public function getByEjemplar($id_ejemplar){
		return $this->listarObj(array("id_ejemplar"=>$id_ejemplar));
	}
	public function getEjemplar(){
		$ejemplar = new Ejemplar($this->con);
		$ejemplar->cargarPorId($this->id_ejemplar);
		return $ejemplar;
	}
	public function getByPrestamo($id_prestamo){
		return $this->listarObj(array("id_prestamo"=>$id_prestamo));
	}
	public function getPrestamo(){
		$prestamo = new Prestamo($this->con);
		$prestamo->cargarPorId($this->id_prestamo);
		return $prestamo;
	}
	public function getByEstadoMulta($id_estado){
		return $this->listarObj(array("id_estado"=>$id_estado));
	}
	public function getEstadoMulta(){
		$estado_multa = new EstadoMulta($this->con);
		$estado_multa->cargarPorId($this->id_estado);
		return $estado_multa;
	}

	//Setters

	public function setIdMulta($idMulta){
		$this->idMulta = $idMulta;
	}
	public function setId_ejemplar($id_ejemplar){
		$this->id_ejemplar = $id_ejemplar;
	}
	public function setId_prestamo($id_prestamo){
		$this->id_prestamo = $id_prestamo;
	}
	public function setId_estado($id_estado){
		$this->id_estado = $id_estado;
	}
	public function setMulta($multa){
		$this->multa = $multa;
	}
	public function setFecha_pago($fecha_pago){
		$this->fecha_pago = $fecha_pago;
	}
	//LLena todos los atributos de la clase sacando los valores de un array
	function setValues($array){
		foreach($array as $key => $val)
			if(property_exists($this,$key))
				$this->$key = $val;
	}
	
	//Guarda o actualiza el objeto en la base de datos, la accion se determina por la clave primaria
	public function save(){
		if(empty($this->idMulta)){			
			$this->idMulta = $this->con->autoInsert(array(
			"id_ejemplar" => $this->getId_ejemplar(),
			"id_prestamo" => $this->getId_prestamo(),
			"id_estado" => $this->getId_estado(),
			"multa" => $this->getMulta(),
			"fecha_pago" => $this->getFecha_pago(),
			),"multa");
			return;
		}
		return $this->con->autoUpdate(array(
			"id_ejemplar" => $this->getId_ejemplar(),
			"id_prestamo" => $this->getId_prestamo(),
			"id_estado" => $this->getId_estado(),
			"multa" => $this->getMulta(),
			"fecha_pago" => $this->getFecha_pago(),
			),"multa","idMulta=".$this->getId());
	}
    
	public function cargarPorId($idMulta){
		if($idMulta>0){
			$result = $this->con->query("SELECT * FROM `multa`  WHERE idMulta=".$idMulta);
			$this->idMulta = $result[0]['idMulta'];
			$this->id_ejemplar = $result[0]['id_ejemplar'];
			$this->id_prestamo = $result[0]['id_prestamo'];
			$this->id_estado = $result[0]['id_estado'];
			$this->multa = $result[0]['multa'];
			$this->fecha_pago = $result[0]['fecha_pago'];
		}
 	}
	public function listar($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$whereA = array();
		if(!$exactMatch){
			$campos = $this->con->query("DESCRIBE multa");
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
		$rows =$this->con->query("SELECT $fields,idMulta FROM `multa`  WHERE $where $orderBy LIMIT $limit");
		$rowsI = array();
		foreach($rows as $row){
			$rowsI[$row["idMulta"]] = $row;
		}
		return $rowsI;
	}
	//como listar, pero retorna un array de objetos
	function listarObj($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$rowsr = array();
		$rows = $this->listar($filtros, $orderBy, $limit, $exactMatch, $fields);
		foreach($rows as $row){
			$obj = clone $this;
			$obj->cargarPorId($row["idMulta"]);
			$rowsr[$row["idMulta"]] = $obj;
		}
		return $rowsr;
	}
	public function eliminar(){
		return $this->con->query("DELETE FROM `multa`  WHERE idMulta=".$this->getId());
	}
}
?>