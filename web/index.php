<?php

//==
define('SILEX_RUNTIME_READY', true);
//==

//==
require(__DIR__.'/../etc/config.php');
//==
require(__DIR__.'/../lib/uxm/lib-uxm-utils.php');
//==

//--
require_once(__DIR__.'/../vendor/autoload.php');
//--

//-- Symfony Debug
if(SMART_APP_DEBUG === true) {
	\Symfony\Component\Debug\Debug::enable();
} //end if
//--

//--
ini_set('default_charset', 'UTF-8');
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
if(SMART_APP_DEBUG === true) {
	ini_set('display_errors', '1');	// display runtime errors
} else {
	ini_set('display_errors', '0');	// hide runtime errors
} //end if else
ini_set('error_log', __DIR__.'/../tmp/phperrors.log'); // record them to a log
//--

//== App
$app = new \Silex\Application();
//==

//-- App Debug
if(SMART_APP_DEBUG === true) {
	$app['debug'] = true;
} //end if
//--

//-- Base
$app->register(new \Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new \Silex\Provider\ValidatorServiceProvider());
$app->register(new \Silex\Provider\ServiceControllerServiceProvider());
$app->register(new \Silex\Provider\HttpFragmentServiceProvider());
//--

//-- Session
$app->register(new \Silex\Provider\SessionServiceProvider());
$app['session.storage.save_path'] = __DIR__.'/../tmp';
//--

//-- Monolog
if(SMART_APP_DEBUG === true) {
	$app->register(new \Silex\Provider\MonologServiceProvider(), array(
		'monolog.level' => \Monolog\Logger::DEBUG,
		'monolog.logfile' => __DIR__.'/../tmp/monolog-dev.log',
	));
} else {
	$app->register(new \Silex\Provider\MonologServiceProvider(), array(
		'monolog.level' => \Monolog\Logger::WARNING,
		'monolog.logfile' => __DIR__.'/../tmp/monolog-prod.log',
	));
} //end if else
//--

//-- Twig Base
if(SMART_APP_DEBUG === true) {
	$app->register(new \Silex\Provider\TwigServiceProvider(), array(
		'twig.path' => __DIR__.'/../middlewares/views',
	));
} else {
	$app->register(new \Silex\Provider\TwigServiceProvider(), array(
		'twig.path' => __DIR__.'/../middlewares/views',
		'twig.options' => array('cache' => __DIR__.'/../tmp/twig'),
	));
} //end if else
//-- Twig Asset
$app['twig'] = $app->extend('twig', function ($twig, $app) {
	$twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) use ($app) {
		return $app['request_stack']->getMasterRequest()->getBasepath().'/'.$asset;
	}));
	return $twig;
});
//--

//-- Doctrine DBAL
if(@is_array($configs['dbs.options'])) {
	//--
	$app->register(new \Silex\Provider\DoctrineServiceProvider(), array(
		'dbs.options' => $configs['dbs.options']
	));
	//--
	if(SMART_APP_DEBUG === true) {
		$app['sqlLogger'] = new \Doctrine\DBAL\Logging\DebugStack();
		$app['db.config']->setSQLLogger($app['sqlLogger']);
	} //end if
	//--
} //end if
//--

//-- MongoDB
if(@is_array($configs['mongodb.options'])) {
	$app->register(new \Saxulum\DoctrineMongoDb\Silex\Provider\DoctrineMongoDbProvider(), array(
		'mongodb.options' => $configs['mongodb.options']
	));
} //end if
//--

//-- Web Profiler
if(SMART_APP_DEBUG === true) {
	$app['twig.loader.filesystem']->addPath(__DIR__.'/../lib/uxm/web-profiler/Silex/Provider/Resources/views', 'UxmWebProfiler');
	require(__DIR__.'/../lib/uxm/web-profiler/Silex/Provider/WebProfilerServiceProvider.php');
	$app->register(new \UXM\Silex\WebProfiler\WebProfilerServiceProvider(), array(
		'profiler.cache_dir' => __DIR__.'/../tmp/profiler',
		'web_profiler.debug_toolbar.enable' => true,
		'web_profiler.debug_toolbar.intercept_redirects' => false
	));
} //end if
//--

//==
$app->get('/{page}/{action}', function($page, $action) use ($app, $configs) {
	//--
	if(!preg_match('/^[a-z0-9_]+$/', $page)) {
		//throw new Exception('Invalid Page / Action ...');
		return '<h1>Invalid Page / Action ...</h1>';
	} //end if
	//--
	$module = __DIR__.'/../middlewares/controllers/'.$page.'.php';
	//--
	if(!is_file($module)) {
		return '<h1>The Page / Action does not exists ...</h1>';
	} //end if
	//--
	require($module);
	//--
	if(!class_exists('MiddlewareController')) {
		return '<h1>Invalid Page / Class ...</h1>';
	} //end if
	//--
	if((string)get_parent_class('MiddlewareController') != 'AbstractMiddlewareController') {
		return '<h1>Invalid Page / Parent Class ...</h1>';
	} //end if
	//--
	return (new MiddlewareController($app, $configs, $page, $action))->Run();
	//--
})->value('page', 'homepage')->value('action', 'default')->bind('app');
//==

//== Run
$app->run();
//==

//==
abstract class AbstractMiddlewareController {

	protected $app;
	protected $configs;
	protected $page;
	protected $action;

	final public function __construct(\Silex\Application $app, $configs, $page, $action) {
		//--
		$this->app = $app;
		$this->configs = (array) $configs;
		$this->page = ''.$page;
		$this->action = ''.$action;
		//--
	} //END FUNCTION

	public function Run() {
		//--
		// this must be extended
		//--
	} //END FUNCTION

} //END CLASS
//==

// end of php code
?>