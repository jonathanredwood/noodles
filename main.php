<?php
/**
 * Every request that does not go directly to a file is directed to main.php
 */
// Autoloading
function autoload($class) {
	$directories = array('core/', 'thirdparty/');
	
	foreach($directories as $directory){
		if(file_exists($directory.$class.'.php')){
			require_once $directory.$class.'.php';
		}
	}
}

spl_autoload_register('autoload');

date_default_timezone_set('UTC');

session_start();
ob_start('ob_gzhandler');
set_time_limit(30);
$_SESSION['currentpage'] = '/';

$core = new Core();
$core->performance->addPoint('Session & Environment');

if(extension_loaded('memcache')){
	ini_set('session.save_handler', 'memcache');
	ini_set('session.save_path', "tcp://localhost:11211");
	$core->performance->addPoint('Memcache sessions set');
}

/*
// Mobile Detection
if(!isset($_SESSION['mobile'])){
	$mob = new Mobile_Detect();
	if($mob->isMobile()){
		$_SESSION['mobile'] = true;
	}else{
		$_SESSION['mobile'] = false;
	}
}
if(isset($_GET['mob'])){
	if($_GET['mob'] == 1) $_SESSION['mobile'] = true;
	if($_GET['mob'] == 0) $_SESSION['mobile'] = false;
}
*/

//get current request
$request = explode('?',$_SERVER['REQUEST_URI']);
$request = trim($request[0], '/');
if(empty($request)) $request = 'index';

// Redirect to ban appeals page if banned
if(isset($_SESSION['steamid']) && $login->isBanned($_SESSION['steamid'])){
	$request = 'banappeal';
}

$core->db->prepare("SELECT pages.title, pages.copy, pages.url, applications.name as appname
						FROM pages LEFT JOIN applications ON pages.application = applications.id WHERE pages.url = :request");
$core->db->bind_value(':request', $request, 'string');
$core->db->execute();
$pagedata = $core->db->prepQueryFirst();

if(!$pagedata){
	//404
}

$request =  $pagedata['appname'];

if(!file_exists( 'applications/'. $pagedata['appname'] . '_app.php' ) && !file_exists( 'templates/'. $pagedata['appname'] . '_tpl.php' )){
	//if the application requested cannot be found then use pagenotfound
	$pagedata = $core->db->queryFirst("SELECT pages.title, pages.copy, pages.url, applications.name as appname
						FROM pages LEFT JOIN applications ON pages.application = applications.id WHERE pages.url = 'pagenotfound'");
	$request =  $pagedata['appname'];
}

if(file_exists( 'applications/'. $request . '_app.php' )){
	//Load and instantiate Applications
	require_once 'applications/'. $request . '_app.php';
	ob_start();
	$application = new PageApplication();
	$application->injectCore($core);
	$application->injectData($pagedata);
	$application->run();

	$content = $application->getContent();
	$errors = ob_get_contents(); //catches all application errors
	ob_end_clean();
	
	$core->performance->addPoint('Application ('.$request.')');
	/*
	if($_SESSION['mobile']){
		if($application->theme == 'default'){
			$application->theme = 'mobile';
		}
	}
	*/
	//build html
	$template = new Template($request, $content, $application->theme, $application->showSkin);
	$template->injectCore($core);
	
	// outputs the markup as minified to save bandwidth while not in dev mode
	if(isset($_SESSION['development']) && $_SESSION['development']){
		// unminified
		echo $template->buildOutput();
	}else{
		// minfied
		$search = array( '/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s');
		$replace = array('>', '<', '\\1');
		echo preg_replace($search, $replace, $template->buildOutput());
	}
}

if(!isset($_SESSION['development'])){
	$_SESSION['development'] = false;
}

if(isset($_GET['dev'])){
	if($_GET['dev'] == 1) $_SESSION['development'] = true;
	if($_GET['dev'] == 0) $_SESSION['development'] = false;
}
$dev = $_SESSION['development'];
if(!$application->showSkin){
	$dev = false;
}

//tidy up
$core->db->close();
session_write_close();
$core->performance->addPoint('End');

if($dev){
	$devpanel = new DeveloperPanel;
	if(!empty($errors)){
		$devpanel->addPanel('<pre>'.$errors.'</pre>');
	}
	$devpanel->addTable($core->performance->consoleData());
	$devpanel->addTable($core->db->consoleData());
		
	$filearray = array();
	foreach(get_included_files() as $file){
		$filearray[] = array('' => array('show' => true, 'text' => str_ireplace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/',$file))));
	}
	$devpanel->addTable(array('title'=>'Includes', 'id'=>'', 'data'=>$filearray));	

	echo $devpanel->draw($application->theme);
}
?>