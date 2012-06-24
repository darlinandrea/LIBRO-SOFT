<?PHP
class LibroModel  extends Libro{
	public function _construct(){
		parent::__construct();
	}	
	public function getPager(Array $columns, Array $filters = array()){
		$whereA = array();
		foreach($filters as $filter => $value)
				$whereA[] = $filter." = ".$this->con->quote($value);
		$where = implode(" AND ",$whereA);
		if($where == '')
			$where = 1;
		$pager = new Pager($this->con,
				"(SELECT idLibro, ISBN, titulo as Titulo, año_publicación as Año, area as Area
				FROM Libro l
				INNER JOIN Area a on l.id_area_conocimiento = a.idArea  
				WHERE {$where}) a",
			$columns, $this->getNombreId());
		return $pager;
	}
}