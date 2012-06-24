<?PHP
class AreaController {
	private $libro;
	private $aParams;
	private $motorDePlantilas;

	public function AreaController(sfTemplateEngine &$engine) {
		$this->libro = new LibroModel();
		$this->aParams = Array();
		$this->motorDePlantilas = $engine;
	}
	public function manejadorDeAcciones() {
		if(@$_REQUEST['sEcho'] != ""){
			die($this->libro->getPager(array("idLibro", "ISBN", "Titulo", "Año","Area"))->getJSON());
		}
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$this->guardar($_POST["idLibro"]);
		}
		if (@$_GET["accion"] == "eliminar" && $_GET["id"] > 0) {
			$this->eliminar(intval($_GET["id"]));
		}
		if (@$_GET["accion"] == "editar" && $_GET["id"] > 0) {
			$this->cargarPorId(intval($_GET["id"]));
			die(json_encode($this->aParams["libro"]));
		}
		$this->consultar();
		$this->mostarPlantilla();
	}
	private function guardar($id) {
		$this->libro->cargarPorId($id);
		$this->libro->setValues($_POST);
		$this->libro->save();
		$resp = json_encode(array("msg"=>"El registro fue grabado. ID=".$this->libro->getId(),"id"=>$this->libro->getId()));
		die($resp);
	}
	
	public function cargarPorId($id){
		$this->libro->cargarPorId($id);
		$this->aParams["libro"] = array(
				"idLibro" => $this->libro->getId(),
				"ISBN" => $this->libro->getISBN(),
				"Titulo" => $this->libro->getTitulo(),
				"Año" => $this->libro->getAño_publicación()
				
		);
	}

	private function eliminar($id) {
		$this->libro->cargarPorId($id);
		$this->libro->eliminar();
		$this->aParams["message"] = "El registro fue eliminado";
		$resp = json_encode(array("msg"=>$this->aParams["message"]));
		die($resp);
	}

	private function consultar() {
		$this->aParams["areas"] = array();
		$areas = $this->libro->listarObj();
		foreach ($areas as $area) {
			$this->aParams["areas"][] = array(
				"idArea" => $area->getId(),
				"area" => $area->getArea(),
				"codigo" => $area->getCodigo(),
				"descripcion" => $area->getDescripcion()
			);
		}
	}

	private function mostarPlantilla() {
		echo $this->motorDePlantilas->render("area", $this->aParams);
	}
}
?>