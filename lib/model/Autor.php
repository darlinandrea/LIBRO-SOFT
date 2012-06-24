<?PHP 
class Autor{
	private $idAutor;
	private $primer_nombre;
	private $segundo_nombre;
	private $primer_apellido;
	private $segundo_apellido;
	private $pais_nacionalidad;
	protected $con;
	public function __construct(){
		$this->con = DBNative::get();
	}
	//Getters

	public function getId(){
		return $this->idAutor;
	}	public function getNombreId(){
		return "idAutor";
	}
	public function getIdAutor(){
		return $this->idAutor;
	}
	public function getPrimer_nombre(){
		return $this->primer_nombre;
	}
	public function getSegundo_nombre(){
		return $this->segundo_nombre;
	}
	public function getPrimer_apellido(){
		return $this->primer_apellido;
	}
	public function getSegundo_apellido(){
		return $this->segundo_apellido;
	}
	public function getPais_nacionalidad(){
		return $this->pais_nacionalidad;
	}

	//Setters

	public function setIdAutor($idAutor){
		$this->idAutor = $idAutor;
	}
	public function setPrimer_nombre($primer_nombre){
		$this->primer_nombre = $primer_nombre;
	}
	public function setSegundo_nombre($segundo_nombre){
		$this->segundo_nombre = $segundo_nombre;
	}
	public function setPrimer_apellido($primer_apellido){
		$this->primer_apellido = $primer_apellido;
	}
	public function setSegundo_apellido($segundo_apellido){
		$this->segundo_apellido = $segundo_apellido;
	}
	public function setPais_nacionalidad($pais_nacionalidad){
		$this->pais_nacionalidad = $pais_nacionalidad;
	}
	//LLena todos los atributos de la clase sacando los valores de un array
	function setValues($array){
		foreach($array as $key => $val)
			if(property_exists($this,$key))
				$this->$key = $val;
	}
	
	//Guarda o actualiza el objeto en la base de datos, la accion se determina por la clave primaria
	public function save(){
		if(empty($this->idAutor)){			
			$this->idAutor = $this->con->autoInsert(array(
			"primer_nombre" => $this->getPrimer_nombre(),
			"segundo_nombre" => $this->getSegundo_nombre(),
			"primer_apellido" => $this->getPrimer_apellido(),
			"segundo_apellido" => $this->getSegundo_apellido(),
			"pais_nacionalidad" => $this->getPais_nacionalidad(),
			),"autor");
			return;
		}
		return $this->con->autoUpdate(array(
			"primer_nombre" => $this->getPrimer_nombre(),
			"segundo_nombre" => $this->getSegundo_nombre(),
			"primer_apellido" => $this->getPrimer_apellido(),
			"segundo_apellido" => $this->getSegundo_apellido(),
			"pais_nacionalidad" => $this->getPais_nacionalidad(),
			),"autor","idAutor=".$this->getId());
	}
    
	public function cargarPorId($idAutor){
		if($idAutor>0){
			$result = $this->con->query("SELECT * FROM `autor`  WHERE idAutor=".$idAutor);
			$this->idAutor = $result[0]['idAutor'];
			$this->primer_nombre = $result[0]['primer_nombre'];
			$this->segundo_nombre = $result[0]['segundo_nombre'];
			$this->primer_apellido = $result[0]['primer_apellido'];
			$this->segundo_apellido = $result[0]['segundo_apellido'];
			$this->pais_nacionalidad = $result[0]['pais_nacionalidad'];
		}
 	}
	public function listar($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$whereA = array();
		if(!$exactMatch){
			$campos = $this->con->query("DESCRIBE autor");
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
		$rows =$this->con->query("SELECT $fields,idAutor FROM `autor`  WHERE $where $orderBy LIMIT $limit");
		$rowsI = array();
		foreach($rows as $row){
			$rowsI[$row["idAutor"]] = $row;
		}
		return $rowsI;
	}
	//como listar, pero retorna un array de objetos
	function listarObj($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$rowsr = array();
		$rows = $this->listar($filtros, $orderBy, $limit, $exactMatch, $fields);
		foreach($rows as $row){
			$obj = clone $this;
			$obj->cargarPorId($row["idAutor"]);
			$rowsr[$row["idAutor"]] = $obj;
		}
		return $rowsr;
	}
	public function eliminar(){
		return $this->con->query("DELETE FROM `autor`  WHERE idAutor=".$this->getId());
	}
}
?>