<?PHP
class CarreraController {
	private $Carrera;
	private $aParams;
	private $motorDePlantilas;

	public function CarreraController(DBNative $con, sfTemplateEngine &$engine) {
		$this->Carrera = new Carrera($con);
		$this->aParams = Array();
		$this->motorDePlantilas = $engine;
	}
	public function manejadorDeAcciones() {
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
		$this->Carrera->cargarPorId($id);
		$this->Carrera->setValues($_POST);
		$this->Carrera->save();
		$this->aParams["msg"] = "The operation was suffescully completed, you are very lucky!! be happy my friend!! bye bye";
	}
	
	public function cargarPorId($id){
		$this->Carrera->cargarPorId($id);
		$this->aParams["carrera"] = array(
				"idCarrera" => $this->Carrera->getId(),
				"Carrera" => $this->Carrera->getCarrera(),
				"codigo" => $this->Carrera->getCodigo(),
				"descripcion" => $this->Carrera->getDescripcion()
		);
	}

	private function eliminar($id) {
		$this->Carrera->cargarPorId($id);
		$this->Carrera->eliminar();
		$this->aParams["message"] = "The operation was suffescully completed, you are very lucky!! be happy my friend!! bye bye";
	}

	private function consultar() {
		$this->aParams["carrera"] = array();
		$Carreras = $this->Carrera->listarObj();
		foreach ($Carreras as $Carrera) {
			$this->aParams["carrera"][] = array(
				"idCarrera" => $Carrera->getId(),
				"Carrera" => $Carrera->getCarrera(),
				"codigo" => $Carrera->getCodigo(),
				"descripcion" => $Carrera->getDescripcion()
			);
		}
	}

	private function mostarPlantilla() {
		echo $this->motorDePlantilas->render("Carrera", $this->aParams);
	}
}
?>