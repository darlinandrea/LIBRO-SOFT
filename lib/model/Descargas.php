<?PHP 
class Descargas{
	private $idDescargas;
	private $contador;
	private $Usuario_idUsuario;
	private $Libro_idLibro;
	private $fecha;
	protected $con;
	public function __construct(){
		$this->con = DBNative::get();
	}
	//Getters

	public function getId(){
		return $this->idDescargas;
	}	public function getNombreId(){
		return "idDescargas";
	}
	public function getIdDescargas(){
		return $this->idDescargas;
	}
	public function getContador(){
		return $this->contador;
	}
	public function getUsuario_idUsuario(){
		return $this->Usuario_idUsuario;
	}
	public function getLibro_idLibro(){
		return $this->Libro_idLibro;
	}
	public function getFecha(){
		return $this->fecha;
	}
	public function getByUsuario($Usuario_idUsuario){
		return $this->listarObj(array("Usuario_idUsuario"=>$Usuario_idUsuario));
	}
	public function getUsuario(){
		$usuario = new Usuario($this->con);
		$usuario->cargarPorId($this->Usuario_idUsuario);
		return $usuario;
	}
	public function getByLibro($Libro_idLibro){
		return $this->listarObj(array("Libro_idLibro"=>$Libro_idLibro));
	}
	public function getLibro(){
		$libro = new Libro($this->con);
		$libro->cargarPorId($this->Libro_idLibro);
		return $libro;
	}

	//Setters

	public function setIdDescargas($idDescargas){
		$this->idDescargas = $idDescargas;
	}
	public function setContador($contador){
		$this->contador = $contador;
	}
	public function setUsuario_idUsuario($Usuario_idUsuario){
		$this->Usuario_idUsuario = $Usuario_idUsuario;
	}
	public function setLibro_idLibro($Libro_idLibro){
		$this->Libro_idLibro = $Libro_idLibro;
	}
	public function setFecha($fecha){
		$this->fecha = $fecha;
	}
	//LLena todos los atributos de la clase sacando los valores de un array
	function setValues($array){
		foreach($array as $key => $val)
			if(property_exists($this,$key))
				$this->$key = $val;
	}
	
	//Guarda o actualiza el objeto en la base de datos, la accion se determina por la clave primaria
	public function save(){
		if(empty($this->idDescargas)){			
			$this->idDescargas = $this->con->autoInsert(array(
			"contador" => $this->getContador(),
			"Usuario_idUsuario" => $this->getUsuario_idUsuario(),
			"Libro_idLibro" => $this->getLibro_idLibro(),
			"fecha" => $this->getFecha(),
			),"descargas");
			return;
		}
		return $this->con->autoUpdate(array(
			"contador" => $this->getContador(),
			"Usuario_idUsuario" => $this->getUsuario_idUsuario(),
			"Libro_idLibro" => $this->getLibro_idLibro(),
			"fecha" => $this->getFecha(),
			),"descargas","idDescargas=".$this->getId());
	}
    
	public function cargarPorId($idDescargas){
		if($idDescargas>0){
			$result = $this->con->query("SELECT * FROM `descargas`  WHERE idDescargas=".$idDescargas);
			$this->idDescargas = $result[0]['idDescargas'];
			$this->contador = $result[0]['contador'];
			$this->Usuario_idUsuario = $result[0]['Usuario_idUsuario'];
			$this->Libro_idLibro = $result[0]['Libro_idLibro'];
			$this->fecha = $result[0]['fecha'];
		}
 	}
	public function listar($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$whereA = array();
		if(!$exactMatch){
			$campos = $this->con->query("DESCRIBE descargas");
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
		$rows =$this->con->query("SELECT $fields,idDescargas FROM `descargas`  WHERE $where $orderBy LIMIT $limit");
		$rowsI = array();
		foreach($rows as $row){
			$rowsI[$row["idDescargas"]] = $row;
		}
		return $rowsI;
	}
	//como listar, pero retorna un array de objetos
	function listarObj($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$rowsr = array();
		$rows = $this->listar($filtros, $orderBy, $limit, $exactMatch, $fields);
		foreach($rows as $row){
			$obj = clone $this;
			$obj->cargarPorId($row["idDescargas"]);
			$rowsr[$row["idDescargas"]] = $obj;
		}
		return $rowsr;
	}
	public function eliminar(){
		return $this->con->query("DELETE FROM `descargas`  WHERE idDescargas=".$this->getId());
	}
}
?>