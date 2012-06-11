<?PHP
class AreaController {
	private $area;
	private $aParams;
	private $motorDePlantilas;

	public function AreaController(DBNative $con, sfTemplateEngine &$engine) {
		$this->area = new Area($con);
		$this->aParams = Array();
		$this->motorDePlantilas = $engine;
	}
	public function manejadorDeAcciones() {
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$this->guardar();
		}
		if (@$_GET["accion"] == "eliminar" && $_GET["id"] > 0) {
			$this->eliminar(intval($_GET["id"]));
		}
		$this->consultar();
		$this->mostarPlantilla();
	}
	private function guardar() {
		$this->area->cargarPorId($_POST["idArea"]);
		$this->area->setValues($_POST);
		$this->area->save();
		$this->aParams["message"] = "The operation was suffescully completed, you are very lucky!! be happy my friend!! bye bye";
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