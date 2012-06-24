<?PHP 
class Ejemplar{
	private $idEjemplar;
	private $Libro_idLibro;
	private $id_estado;
	private $nombre_sala;
	private $numero_pasillo;
	private $estante;
	private $numero_cajon;
	protected $con;
	public function __construct(){
		$this->con = DBNative::get();
	}
	//Getters

	public function getId(){
		return $this->idEjemplar;
	}	public function getNombreId(){
		return "idEjemplar";
	}
	public function getIdEjemplar(){
		return $this->idEjemplar;
	}
	public function getLibro_idLibro(){
		return $this->Libro_idLibro;
	}
	public function getId_estado(){
		return $this->id_estado;
	}
	public function getNombre_sala(){
		return $this->nombre_sala;
	}
	public function getNumero_pasillo(){
		return $this->numero_pasillo;
	}
	public function getEstante(){
		return $this->estante;
	}
	public function getNumero_cajon(){
		return $this->numero_cajon;
	}
	public function getByLibro($Libro_idLibro){
		return $this->listarObj(array("Libro_idLibro"=>$Libro_idLibro));
	}
	public function getLibro(){
		$libro = new Libro($this->con);
		$libro->cargarPorId($this->Libro_idLibro);
		return $libro;
	}
	public function getByEstadoEjemplar($id_estado){
		return $this->listarObj(array("id_estado"=>$id_estado));
	}
	public function getEstadoEjemplar(){
		$estado_ejemplar = new EstadoEjemplar($this->con);
		$estado_ejemplar->cargarPorId($this->id_estado);
		return $estado_ejemplar;
	}

	//Setters

	public function setIdEjemplar($idEjemplar){
		$this->idEjemplar = $idEjemplar;
	}
	public function setLibro_idLibro($Libro_idLibro){
		$this->Libro_idLibro = $Libro_idLibro;
	}
	public function setId_estado($id_estado){
		$this->id_estado = $id_estado;
	}
	public function setNombre_sala($nombre_sala){
		$this->nombre_sala = $nombre_sala;
	}
	public function setNumero_pasillo($numero_pasillo){
		$this->numero_pasillo = $numero_pasillo;
	}
	public function setEstante($estante){
		$this->estante = $estante;
	}
	public function setNumero_cajon($numero_cajon){
		$this->numero_cajon = $numero_cajon;
	}
	//LLena todos los atributos de la clase sacando los valores de un array
	function setValues($array){
		foreach($array as $key => $val)
			if(property_exists($this,$key))
				$this->$key = $val;
	}
	
	//Guarda o actualiza el objeto en la base de datos, la accion se determina por la clave primaria
	public function save(){
		if(empty($this->idEjemplar)){			
			$this->idEjemplar = $this->con->autoInsert(array(
			"Libro_idLibro" => $this->getLibro_idLibro(),
			"id_estado" => $this->getId_estado(),
			"nombre_sala" => $this->getNombre_sala(),
			"numero_pasillo" => $this->getNumero_pasillo(),
			"estante" => $this->getEstante(),
			"numero_cajon" => $this->getNumero_cajon(),
			),"ejemplar");
			return;
		}
		return $this->con->autoUpdate(array(
			"Libro_idLibro" => $this->getLibro_idLibro(),
			"id_estado" => $this->getId_estado(),
			"nombre_sala" => $this->getNombre_sala(),
			"numero_pasillo" => $this->getNumero_pasillo(),
			"estante" => $this->getEstante(),
			"numero_cajon" => $this->getNumero_cajon(),
			),"ejemplar","idEjemplar=".$this->getId());
	}
    
	public function cargarPorId($idEjemplar){
		if($idEjemplar>0){
			$result = $this->con->query("SELECT * FROM `ejemplar`  WHERE idEjemplar=".$idEjemplar);
			$this->idEjemplar = $result[0]['idEjemplar'];
			$this->Libro_idLibro = $result[0]['Libro_idLibro'];
			$this->id_estado = $result[0]['id_estado'];
			$this->nombre_sala = $result[0]['nombre_sala'];
			$this->numero_pasillo = $result[0]['numero_pasillo'];
			$this->estante = $result[0]['estante'];
			$this->numero_cajon = $result[0]['numero_cajon'];
		}
 	}
	public function listar($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$whereA = array();
		if(!$exactMatch){
			$campos = $this->con->query("DESCRIBE ejemplar");
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
		$rows =$this->con->query("SELECT $fields,idEjemplar FROM `ejemplar`  WHERE $where $orderBy LIMIT $limit");
		$rowsI = array();
		foreach($rows as $row){
			$rowsI[$row["idEjemplar"]] = $row;
		}
		return $rowsI;
	}
	//como listar, pero retorna un array de objetos
	function listarObj($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$rowsr = array();
		$rows = $this->listar($filtros, $orderBy, $limit, $exactMatch, $fields);
		foreach($rows as $row){
			$obj = clone $this;
			$obj->cargarPorId($row["idEjemplar"]);
			$rowsr[$row["idEjemplar"]] = $obj;
		}
		return $rowsr;
	}
	public function eliminar(){
		return $this->con->query("DELETE FROM `ejemplar`  WHERE idEjemplar=".$this->getId());
	}
}
?>