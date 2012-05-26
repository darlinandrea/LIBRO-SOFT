<?PHP 
class Usuario{
	private $idUsuario;
	private $id_tipo_identificacion;
	private $nombres;
	private $apellidos;
	private $identificacion;
	private $direccion;
	private $telefono;
	private $email;
	private $universidad;
	private $con;
	public function __construct(&$db){
		$this->con = $db;
	}
	//Getters

	public function getId(){
		return $this->idUsuario;
	}
	public function getNombreId(){
		return "idUsuario";
	}
	public function getIdUsuario(){
		return $this->idUsuario;
	}
	public function getId_tipo_identificacion(){
		return $this->id_tipo_identificacion;
	}
	public function getNombres(){
		return $this->nombres;
	}
	public function getApellidos(){
		return $this->apellidos;
	}
	public function getIdentificacion(){
		return $this->identificacion;
	}
	public function getDireccion(){
		return $this->direccion;
	}
	public function getTelefono(){
		return $this->telefono;
	}
	public function getEmail(){
		return $this->email;
	}
	public function getUniversidad(){
		return $this->universidad;
	}
	public function getByTipoIdentificación($id_tipo_identificacion){
		return $this->listarObj(array("id_tipo_identificacion"=>$id_tipo_identificacion));
	}
	public function getTipoIdentificación(){
		$tipo_identificación = new TipoIdentificación($this->con);
		$tipo_identificación->cargarPorId($this->id_tipo_identificacion);
		return $tipo_identificación;
	}

	//Setters

	public function setIdUsuario($idUsuario){
		$this->idUsuario = $idUsuario;
	}
	public function setId_tipo_identificacion($id_tipo_identificacion){
		$this->id_tipo_identificacion = $id_tipo_identificacion;
	}
	public function setNombres($nombres){
		$this->nombres = $nombres;
	}
	public function setApellidos($apellidos){
		$this->apellidos = $apellidos;
	}
	public function setIdentificacion($identificacion){
		$this->identificacion = $identificacion;
	}
	public function setDireccion($direccion){
		$this->direccion = $direccion;
	}
	public function setTelefono($telefono){
		$this->telefono = $telefono;
	}
	public function setEmail($email){
		$this->email = $email;
	}
	public function setUniversidad($universidad){
		$this->universidad = $universidad;
	}
	//LLena todos los atributos de la clase sacando los valores de un array
	function setValues($array){
		foreach($array as $key => $val)
			if(property_exists($this,$key))
				$this->$key = $val;
	}
	
	//Guarda o actualiza el objeto en la base de datos, la accion se determina por la clave primaria
	public function save(){
		if(empty($this->idUsuario)){			
			$this->idUsuario = $this->con->autoInsert(array(
			"id_tipo_identificacion" => $this->getId_tipo_identificacion(),
			"nombres" => $this->getNombres(),
			"apellidos" => $this->getApellidos(),
			"identificacion" => $this->getIdentificacion(),
			"direccion" => $this->getDireccion(),
			"telefono" => $this->getTelefono(),
			"email" => $this->getEmail(),
			"universidad" => $this->getUniversidad(),
			),"usuario");
			return;
		}
		return $this->con->autoUpdate(array(
			"id_tipo_identificacion" => $this->getId_tipo_identificacion(),
			"nombres" => $this->getNombres(),
			"apellidos" => $this->getApellidos(),
			"identificacion" => $this->getIdentificacion(),
			"direccion" => $this->getDireccion(),
			"telefono" => $this->getTelefono(),
			"email" => $this->getEmail(),
			"universidad" => $this->getUniversidad(),
			),"usuario","idUsuario=".$this->getId());
	}
    
	public function cargarPorId($idUsuario){
		if($idUsuario>0){
			$result = $this->con->query("SELECT * FROM `usuario`  WHERE idUsuario=".$idUsuario);
			$this->idUsuario = $result[0]['idUsuario'];
			$this->id_tipo_identificacion = $result[0]['id_tipo_identificacion'];
			$this->nombres = $result[0]['nombres'];
			$this->apellidos = $result[0]['apellidos'];
			$this->identificacion = $result[0]['identificacion'];
			$this->direccion = $result[0]['direccion'];
			$this->telefono = $result[0]['telefono'];
			$this->email = $result[0]['email'];
			$this->universidad = $result[0]['universidad'];
		}
 	}
	public function listar($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false){
		$whereA = array();
		if(!$exactMatch){
			$campos = $this->con->query("DESCRIBE usuario");
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
		$rows =$this->con->query("SELECT * FROM `usuario`  WHERE $where $orderBy LIMIT $limit");
		$rowsI = array();
		foreach($rows as $row){
			$rowsI[$row["idUsuario"]] = $row;
		}
		return $rowsI;
	}
	//como listar, pero retorna un array de objetos
	function listarObj($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false){
		$rowsr = array();
		$rows = $this->listar($filtros, $orderBy, $limit, $exactMatch);
		foreach($rows as $row){
			$this->cargarPorId($row["idUsuario"]);
			$obj = clone $this;
			$rowsr[$row["idUsuario"]] = $obj;
		}
		return $rowsr;
	}
	public function eliminar(){
		return $this->con->query("DELETE FROM `usuario`  WHERE idUsuario=".$this->getId());
	}
}
?>