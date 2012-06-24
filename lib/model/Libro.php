<?PHP 
class Libro{
	private $idLibro;
	private $id_area_conocimiento;
	private $ISBN;
	private $titulo;
	private $año_publicación;
	private $idioma;
	private $palabras_claves;
	private $id_editorial;
	private $caratula;
	private $archivo;
	private $fecha_ingreso;
	protected $con;
	public function __construct(){
		$this->con = DBNative::get();
	}
	//Getters

	public function getId(){
		return $this->idLibro;
	}	public function getNombreId(){
		return "idLibro";
	}
	public function getIdLibro(){
		return $this->idLibro;
	}
	public function getId_area_conocimiento(){
		return $this->id_area_conocimiento;
	}
	public function getISBN(){
		return $this->ISBN;
	}
	public function getTitulo(){
		return $this->titulo;
	}
	public function getAño_publicación(){
		return $this->año_publicación;
	}
	public function getIdioma(){
		return $this->idioma;
	}
	public function getPalabras_claves(){
		return $this->palabras_claves;
	}
	public function getId_editorial(){
		return $this->id_editorial;
	}
	public function getCaratula(){
		return $this->caratula;
	}
	public function getArchivo(){
		return $this->archivo;
	}
	public function getFecha_ingreso(){
		return $this->fecha_ingreso;
	}
	public function getByEditorial($id_editorial){
		return $this->listarObj(array("id_editorial"=>$id_editorial));
	}
	public function getEditorial(){
		$editorial = new Editorial($this->con);
		$editorial->cargarPorId($this->id_editorial);
		return $editorial;
	}
	public function getByArea($id_area_conocimiento){
		return $this->listarObj(array("id_area_conocimiento"=>$id_area_conocimiento));
	}
	public function getArea(){
		$area = new Area($this->con);
		$area->cargarPorId($this->id_area_conocimiento);
		return $area;
	}

	//Setters

	public function setIdLibro($idLibro){
		$this->idLibro = $idLibro;
	}
	public function setId_area_conocimiento($id_area_conocimiento){
		$this->id_area_conocimiento = $id_area_conocimiento;
	}
	public function setISBN($ISBN){
		$this->ISBN = $ISBN;
	}
	public function setTitulo($titulo){
		$this->titulo = $titulo;
	}
	public function setAño_publicación($año_publicación){
		$this->año_publicación = $año_publicación;
	}
	public function setIdioma($idioma){
		$this->idioma = $idioma;
	}
	public function setPalabras_claves($palabras_claves){
		$this->palabras_claves = $palabras_claves;
	}
	public function setId_editorial($id_editorial){
		$this->id_editorial = $id_editorial;
	}
	public function setCaratula($caratula){
		$this->caratula = $caratula;
	}
	public function setArchivo($archivo){
		$this->archivo = $archivo;
	}
	public function setFecha_ingreso($fecha_ingreso){
		$this->fecha_ingreso = $fecha_ingreso;
	}
	//LLena todos los atributos de la clase sacando los valores de un array
	function setValues($array){
		foreach($array as $key => $val)
			if(property_exists($this,$key))
				$this->$key = $val;
	}
	
	//Guarda o actualiza el objeto en la base de datos, la accion se determina por la clave primaria
	public function save(){
		if(empty($this->idLibro)){			
			$this->idLibro = $this->con->autoInsert(array(
			"id_area_conocimiento" => $this->getId_area_conocimiento(),
			"ISBN" => $this->getISBN(),
			"titulo" => $this->getTitulo(),
			"año_publicación" => $this->getAño_publicación(),
			"idioma" => $this->getIdioma(),
			"palabras_claves" => $this->getPalabras_claves(),
			"id_editorial" => $this->getId_editorial(),
			"caratula" => $this->getCaratula(),
			"archivo" => $this->getArchivo(),
			"fecha_ingreso" => $this->getFecha_ingreso(),
			),"libro");
			return;
		}
		return $this->con->autoUpdate(array(
			"id_area_conocimiento" => $this->getId_area_conocimiento(),
			"ISBN" => $this->getISBN(),
			"titulo" => $this->getTitulo(),
			"año_publicación" => $this->getAño_publicación(),
			"idioma" => $this->getIdioma(),
			"palabras_claves" => $this->getPalabras_claves(),
			"id_editorial" => $this->getId_editorial(),
			"caratula" => $this->getCaratula(),
			"archivo" => $this->getArchivo(),
			"fecha_ingreso" => $this->getFecha_ingreso(),
			),"libro","idLibro=".$this->getId());
	}
    
	public function cargarPorId($idLibro){
		if($idLibro>0){
			$result = $this->con->query("SELECT * FROM `libro`  WHERE idLibro=".$idLibro);
			$this->idLibro = $result[0]['idLibro'];
			$this->id_area_conocimiento = $result[0]['id_area_conocimiento'];
			$this->ISBN = $result[0]['ISBN'];
			$this->titulo = $result[0]['titulo'];
			$this->año_publicación = $result[0]['año_publicación'];
			$this->idioma = $result[0]['idioma'];
			$this->palabras_claves = $result[0]['palabras_claves'];
			$this->id_editorial = $result[0]['id_editorial'];
			$this->caratula = $result[0]['caratula'];
			$this->archivo = $result[0]['archivo'];
			$this->fecha_ingreso = $result[0]['fecha_ingreso'];
		}
 	}
	public function listar($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$whereA = array();
		if(!$exactMatch){
			$campos = $this->con->query("DESCRIBE libro");
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
		$rows =$this->con->query("SELECT $fields,idLibro FROM `libro`  WHERE $where $orderBy LIMIT $limit");
		$rowsI = array();
		foreach($rows as $row){
			$rowsI[$row["idLibro"]] = $row;
		}
		return $rowsI;
	}
	//como listar, pero retorna un array de objetos
	function listarObj($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$rowsr = array();
		$rows = $this->listar($filtros, $orderBy, $limit, $exactMatch, $fields);
		foreach($rows as $row){
			$obj = clone $this;
			$obj->cargarPorId($row["idLibro"]);
			$rowsr[$row["idLibro"]] = $obj;
		}
		return $rowsr;
	}
	public function eliminar(){
		return $this->con->query("DELETE FROM `libro`  WHERE idLibro=".$this->getId());
	}
}
?>