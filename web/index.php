<?php

//==
require(__DIR__.'/../etc/config.php');
//==
require(__DIR__.'/../lib/uxm/lib-uxm-utils.php');
//==

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

//--
require_once(__DIR__.'/../vendor/autoload.php');
//--

//-- Symfony Debug
if(SMART_APP_DEBUG === true) {
	\Symfony\Component\Debug\Debug::enable();
} //end if
//--

//== App
$app = new \Silex\Application();
//==

//-- App Debug
if(SMART_APP_DEBUG === true) {
	$app['debug'] = true;
} //end if
//--

//==
require(__DIR__.'/../modules/auth.php');
//==

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
		'twig.path' => __DIR__.'/../templates',
	));
} else {
	$app->register(new \Silex\Provider\TwigServiceProvider(), array(
		'twig.path' => __DIR__.'/../templates',
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
require(__DIR__.'/../modules/app.php');
//==

//== Run
$app->run();
//==

// end of php code
?>