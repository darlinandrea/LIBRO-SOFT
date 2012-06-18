<?PHP
class AreaModel extends Area{
	private $con;
	public function AreaModel(DBNative $con){
		parent::__construct($con);
		$this->con = $con;
	}	
	public function getPager(Array $columns, Array $filters = array()){
		$whereA = array();
		foreach($filters as $filter => $value)
				$whereA[] = $filter." = ".$this->con->quote($value);
		$where = implode(" AND ",$whereA);
		if($where == '')
			$where = 1;
		$pager = new Pager($this->con,
				"(SELECT idArea, codigo as Codigo, area as Area, descripcion as Descripcion FROM Area WHERE {$where}) a",
			$columns, $this->getNombreId());
		return $pager;
	}
}