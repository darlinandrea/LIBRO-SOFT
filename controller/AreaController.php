<?PHP
class AreaController {
	private $area;
	private $aParams;
	private $motorDePlantilas;

	public function AreaController(DBNative $con, sfTemplateEngine &$engine) {
		$this->area = new AreaModel($con);
		$this->aParams = Array();
		$this->motorDePlantilas = $engine;
	}
	public function manejadorDeAcciones() {
		if(@$_REQUEST['sEcho'] != ""){
			die($this->area->getPager(array("Codigo","Area","Descripcion"))->getJSON());
		}
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$this->guardar($_POST["id"]);
		}
		if (@$_GET["accion"] == "eliminar" && $_GET["id"] > 0) {
			$this->eliminar(intval($_GET["id"]));
		}
		if (@$_GET["accion"] == "editar" && $_GET["id"] > 0) {
			$this->cargarPorId(intval($_GET["id"]));
		}
		$this->consultar();
		$this->mostarPlantilla();
	}
	private function guardar($id) {
		$this->area->cargarPorId($id);
		$this->area->setValues($_POST);
		$this->area->save();
		$this->aParams["msg"] = "The operation was suffescully completed, you are very lucky!! be happy my friend!! bye bye";
	}
	
	public function cargarPorId($id){
		$this->area->cargarPorId($id);
		$this->aParams["area"] = array(
				"idArea" => $this->area->getId(),
				"area" => $this->area->getArea(),
				"codigo" => $this->area->getCodigo(),
				"descripcion" => $this->area->getDescripcion()
		);
	}

	private function eliminar($id) {
		$this->area->cargarPorId($id);
		$this->area->eliminar();
		$this->aParams["message"] = "The operation was suffescully completed, you are very lucky!! be happy my friend!! bye bye";
	}

	private function consultar() {
		$this->aParams["areas"] = array();
		$areas = $this->area->listarObj();
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