<?PHP 
require_once 'frontend_tpl_conf.php';
$aParams = array();
switch($_GET["ac"]){
	case "area":
		require FRONTEND_PATH_CONTROLLERS."/AreaController.php";
		(new AreaController($con))->actionHandler();
	break;
	default:
		$aParams['user']='super admin';
		echo $engine->render('index', $aParams);
	break;	
}
?>
