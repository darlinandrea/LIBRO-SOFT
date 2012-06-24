<?PHP 
class LibroAutor{
	private $idLibro_Autor;
	private $id_libro;
	private $id_autor;
	protected $con;
	public function __construct(){
		$this->con = DBNative::get();
	}
	//Getters

	public function getId(){
		return $this->idLibro_Autor;
	}	public function getNombreId(){
		return "idLibro_Autor";
	}
	public function getIdLibroAutor(){
		return $this->idLibro_Autor;
	}
	public function getIdLibro(){
		return $this->id_libro;
	}
	public function getIdAutor(){
		return $this->id_autor;
	}
	public function getByLibro($id_libro){
		return $this->listarObj(array("id_libro"=>$id_libro));
	}
	public function getLibro(){
		$libro = new Libro($this->con);
		$libro->cargarPorId($this->id_libro);
		return $libro;
	}
	public function getByAutor($id_autor){
		return $this->listarObj(array("id_autor"=>$id_autor));
	}
	public function getAutor(){
		$autor = new Autor($this->con);
		$autor->cargarPorId($this->id_autor);
		return $autor;
	}

	//Setters

	public function setIdLibro_Autor($idLibro_Autor){
		$this->idLibro_Autor = $idLibro_Autor;
	}
	public function setId_libro($id_libro){
		$this->id_libro = $id_libro;
	}
	public function setId_autor($id_autor){
		$this->id_autor = $id_autor;
	}
	//LLena todos los atributos de la clase sacando los valores de un array
	function setValues($array){
		foreach($array as $key => $val)
			if(property_exists($this,$key))
				$this->$key = $val;
	}
	
	//Guarda o actualiza el objeto en la base de datos, la accion se determina por la clave primaria
	public function save(){
		if(empty($this->idLibro_Autor)){			
			$this->idLibro_Autor = $this->con->autoInsert(array(
			"id_libro" => $this->getId_libro(),
			"id_autor" => $this->getId_autor(),
			),"libro_autor");
			return;
		}
		return $this->con->autoUpdate(array(
			"id_libro" => $this->getId_libro(),
			"id_autor" => $this->getId_autor(),
			),"libro_autor","idLibro_Autor=".$this->getId());
	}
    
	public function cargarPorId($idLibro_Autor){
		if($idLibro_Autor>0){
			$result = $this->con->query("SELECT * FROM `libro_autor`  WHERE idLibro_Autor=".$idLibro_Autor);
			$this->idLibro_Autor = $result[0]['idLibro_Autor'];
			$this->id_libro = $result[0]['id_libro'];
			$this->id_autor = $result[0]['id_autor'];
		}
 	}
	public function listar($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$whereA = array();
		if(!$exactMatch){
			$campos = $this->con->query("DESCRIBE libro_autor");
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
		$rows =$this->con->query("SELECT $fields,idLibro_Autor FROM `libro_autor`  WHERE $where $orderBy LIMIT $limit");
		$rowsI = array();
		foreach($rows as $row){
			$rowsI[$row["idLibro_Autor"]] = $row;
		}
		return $rowsI;
	}
	//como listar, pero retorna un array de objetos
	function listarObj($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$rowsr = array();
		$rows = $this->listar($filtros, $orderBy, $limit, $exactMatch, $fields);
		foreach($rows as $row){
			$obj = clone $this;
			$obj->cargarPorId($row["idLibro_Autor"]);
			$rowsr[$row["idLibro_Autor"]] = $obj;
		}
		return $rowsr;
	}
	public function eliminar(){
		return $this->con->query("DELETE FROM `libro_autor`  WHERE idLibro_Autor=".$this->getId());
	}
}
?>