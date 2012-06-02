<?PHP 
require_once 'frontend_tpl_conf.php';
$aParams = array();
$aParams['user']='super admin';
echo $engine->render('index', $aParams);
?>		          
