<?PHP
require_once 'config/config.inc.php';
require_once 'lib/templating/lib/sfTemplateAutoloader.php';
sfTemplateAutoloader::register(); 
$loader = new sfTemplateLoaderFilesystem(FRONTEND_PATH_TEMPLATES.'/%name%.php'); 
$engine = new sfTemplateEngine($loader);
$helperSet = new sfTemplateHelperSet(array(
    new sfTemplateHelperAssets(),
    new sfTemplateHelperJavascripts(), 
    new sfTemplateHelperStylesheets()
));
$engine->setHelperSet($helperSet);
?>