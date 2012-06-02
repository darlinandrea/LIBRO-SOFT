<?PHP 
class Profesor{
	private $idProfesor;
	private $Usuario_idUsuario;
	private $dependencia;
	private $titulo;
	private $áreas_interés;
	private $con;
	public function __construct(&$db){
		$this->con = $db;
	}
	//Getters

	public function getId(){
		return $this->idProfesor;
	}
	public function getNombreId(){
		return "idProfesor";
	}
	public function getIdProfesor(){
		return $this->idProfesor;
	}
	public function getUsuario_idUsuario(){
		return $this->Usuario_idUsuario;
	}
	public function getDependencia(){
		return $this->dependencia;
	}
	public function getTitulo(){
		return $this->titulo;
	}
	public function getáreas_interés(){
		return $this->áreas_interés;
	}
	public function getByUsuario($Usuario_idUsuario){
		return $this->listarObj(array("Usuario_idUsuario"=>$Usuario_idUsuario));
	}
	public function getUsuario(){
		$usuario = new Usuario($this->con);
		$usuario->cargarPorId($this->Usuario_idUsuario);
		return $usuario;
	}

	//Setters

	public function setIdProfesor($idProfesor){
		$this->idProfesor = $idProfesor;
	}
	public function setUsuario_idUsuario($Usuario_idUsuario){
		$this->Usuario_idUsuario = $Usuario_idUsuario;
	}
	public function setDependencia($dependencia){
		$this->dependencia = $dependencia;
	}
	public function setTitulo($titulo){
		$this->titulo = $titulo;
	}
	public function setáreas_interés($áreas_interés){
		$this->áreas_interés = $áreas_interés;
	}
	//LLena todos los atributos de la clase sacando los valores de un array
	function setValues($array){
		foreach($array as $key => $val)
			if(property_exists($this,$key))
				$this->$key = $val;
	}
	
	//Guarda o actualiza el objeto en la base de datos, la accion se determina por la clave primaria
	public function save(){
		if(empty($this->idProfesor)){			
			$this->idProfesor = $this->con->autoInsert(array(
			"Usuario_idUsuario" => $this->getUsuario_idUsuario(),
			"dependencia" => $this->getDependencia(),
			"titulo" => $this->getTitulo(),
			"áreas_interés" => $this->getáreas_interés(),
			),"profesor");
			return;
		}
		return $this->con->autoUpdate(array(
			"Usuario_idUsuario" => $this->getUsuario_idUsuario(),
			"dependencia" => $this->getDependencia(),
			"titulo" => $this->getTitulo(),
			"áreas_interés" => $this->getáreas_interés(),
			),"profesor","idProfesor=".$this->getId());
	}
    
	public function cargarPorId($idProfesor){
		if($idProfesor>0){
			$result = $this->con->query("SELECT * FROM `profesor`  WHERE idProfesor=".$idProfesor);
			$this->idProfesor = $result[0]['idProfesor'];
			$this->Usuario_idUsuario = $result[0]['Usuario_idUsuario'];
			$this->dependencia = $result[0]['dependencia'];
			$this->titulo = $result[0]['titulo'];
			$this->áreas_interés = $result[0]['áreas_interés'];
		}
 	}
	public function listar($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false){
		$whereA = array();
		if(!$exactMatch){
			$campos = $this->con->query("DESCRIBE profesor");
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
		$rows =$this->con->query("SELECT * FROM `profesor`  WHERE $where $orderBy LIMIT $limit");
		$rowsI = array();
		foreach($rows as $row){
			$rowsI[$row["idProfesor"]] = $row;
		}
		return $rowsI;
	}
	//como listar, pero retorna un array de objetos
	function listarObj($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false){
		$rowsr = array();
		$rows = $this->listar($filtros, $orderBy, $limit, $exactMatch);
		foreach($rows as $row){
			$this->cargarPorId($row["idProfesor"]);
			$obj = clone $this;
			$rowsr[$row["idProfesor"]] = $obj;
		}
		return $rowsr;
	}
	public function eliminar(){
		return $this->con->query("DELETE FROM `profesor`  WHERE idProfesor=".$this->getId());
	}
}
?>