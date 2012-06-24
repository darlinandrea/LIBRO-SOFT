<?PHP 
class SolucitudCompraLibros{
	private $idSolucitud_Compra_Libros;
	private $id_usuario;
	private $estado;
	private $isbn;
	private $descripcion;
	private $titulo;
	private $fecha_solicitud;
	protected $con;
	public function __construct(){
		$this->con = DBNative::get();
	}
	//Getters

	public function getId(){
		return $this->idSolucitud_Compra_Libros;
	}	public function getNombreId(){
		return "idSolucitud_Compra_Libros";
	}
	public function getIdSolucitud_Compra_Libros(){
		return $this->idSolucitud_Compra_Libros;
	}
	public function getId_usuario(){
		return $this->id_usuario;
	}
	public function getEstado(){
		return $this->estado;
	}
	public function getIsbn(){
		return $this->isbn;
	}
	public function getDescripcion(){
		return $this->descripcion;
	}
	public function getTitulo(){
		return $this->titulo;
	}
	public function getFecha_solicitud(){
		return $this->fecha_solicitud;
	}
	public function getByUsuario($id_usuario){
		return $this->listarObj(array("id_usuario"=>$id_usuario));
	}
	public function getUsuario(){
		$usuario = new Usuario($this->con);
		$usuario->cargarPorId($this->id_usuario);
		return $usuario;
	}
	public function getByEstadoSolicitudCompra($estado){
		return $this->listarObj(array("estado"=>$estado));
	}
	public function getEstadoSolicitudCompra(){
		$estado_solicitud_compra = new EstadoSolicitudCompra($this->con);
		$estado_solicitud_compra->cargarPorId($this->estado);
		return $estado_solicitud_compra;
	}

	//Setters

	public function setIdSolucitud_Compra_Libros($idSolucitud_Compra_Libros){
		$this->idSolucitud_Compra_Libros = $idSolucitud_Compra_Libros;
	}
	public function setId_usuario($id_usuario){
		$this->id_usuario = $id_usuario;
	}
	public function setEstado($estado){
		$this->estado = $estado;
	}
	public function setIsbn($isbn){
		$this->isbn = $isbn;
	}
	public function setDescripcion($descripcion){
		$this->descripcion = $descripcion;
	}
	public function setTitulo($titulo){
		$this->titulo = $titulo;
	}
	public function setFecha_solicitud($fecha_solicitud){
		$this->fecha_solicitud = $fecha_solicitud;
	}
	//LLena todos los atributos de la clase sacando los valores de un array
	function setValues($array){
		foreach($array as $key => $val)
			if(property_exists($this,$key))
				$this->$key = $val;
	}
	
	//Guarda o actualiza el objeto en la base de datos, la accion se determina por la clave primaria
	public function save(){
		if(empty($this->idSolucitud_Compra_Libros)){			
			$this->idSolucitud_Compra_Libros = $this->con->autoInsert(array(
			"id_usuario" => $this->getId_usuario(),
			"estado" => $this->getEstado(),
			"isbn" => $this->getIsbn(),
			"descripcion" => $this->getDescripcion(),
			"titulo" => $this->getTitulo(),
			"fecha_solicitud" => $this->getFecha_solicitud(),
			),"solucitud_compra_libros");
			return;
		}
		return $this->con->autoUpdate(array(
			"id_usuario" => $this->getId_usuario(),
			"estado" => $this->getEstado(),
			"isbn" => $this->getIsbn(),
			"descripcion" => $this->getDescripcion(),
			"titulo" => $this->getTitulo(),
			"fecha_solicitud" => $this->getFecha_solicitud(),
			),"solucitud_compra_libros","idSolucitud_Compra_Libros=".$this->getId());
	}
    
	public function cargarPorId($idSolucitud_Compra_Libros){
		if($idSolucitud_Compra_Libros>0){
			$result = $this->con->query("SELECT * FROM `solucitud_compra_libros`  WHERE idSolucitud_Compra_Libros=".$idSolucitud_Compra_Libros);
			$this->idSolucitud_Compra_Libros = $result[0]['idSolucitud_Compra_Libros'];
			$this->id_usuario = $result[0]['id_usuario'];
			$this->estado = $result[0]['estado'];
			$this->isbn = $result[0]['isbn'];
			$this->descripcion = $result[0]['descripcion'];
			$this->titulo = $result[0]['titulo'];
			$this->fecha_solicitud = $result[0]['fecha_solicitud'];
		}
 	}
	public function listar($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$whereA = array();
		if(!$exactMatch){
			$campos = $this->con->query("DESCRIBE solucitud_compra_libros");
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
		$rows =$this->con->query("SELECT $fields,idSolucitud_Compra_Libros FROM `solucitud_compra_libros`  WHERE $where $orderBy LIMIT $limit");
		$rowsI = array();
		foreach($rows as $row){
			$rowsI[$row["idSolucitud_Compra_Libros"]] = $row;
		}
		return $rowsI;
	}
	//como listar, pero retorna un array de objetos
	function listarObj($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$rowsr = array();
		$rows = $this->listar($filtros, $orderBy, $limit, $exactMatch, $fields);
		foreach($rows as $row){
			$obj = clone $this;
			$obj->cargarPorId($row["idSolucitud_Compra_Libros"]);
			$rowsr[$row["idSolucitud_Compra_Libros"]] = $obj;
		}
		return $rowsr;
	}
	public function eliminar(){
		return $this->con->query("DELETE FROM `solucitud_compra_libros`  WHERE idSolucitud_Compra_Libros=".$this->getId());
	}
}
?>