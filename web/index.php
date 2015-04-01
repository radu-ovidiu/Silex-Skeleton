<?php

//==
require(__DIR__.'/../etc/config.php');
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

//-- Base
$app->register(new \Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new \Silex\Provider\ValidatorServiceProvider());
$app->register(new \Silex\Provider\ServiceControllerServiceProvider());
$app->register(new \Silex\Provider\HttpFragmentServiceProvider());
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
$app->register(new \Silex\Provider\DoctrineServiceProvider(), array(
	'dbs.options' => $configs['dbs.options']
));
//--
if(SMART_APP_DEBUG === true) {

$app['sqlLogger'] = new \Doctrine\DBAL\Logging\DebugStack();
$app['db.config']->setSQLLogger($app['sqlLogger']);

} //end if
//--
if(!is_file(__DIR__.'/../tmp/db.sqlite')) {
	$app['dbs']['sqlite']->executeQuery(
		'CREATE TABLE "table_main_sample" ("id" character varying(10) NOT NULL, "name" character varying(100) NOT NULL, "description" text NOT NULL, "dtime" text NOT NULL )',
		array()
	);
} //end if
$sql = "SELECT * FROM table_main_sample WHERE id = ?";
$post = $app['dbs']['sqlite']->fetchAssoc($sql, array(1));
//--
//print_r($logger);
//--

//-- MongoDB
/*
$app->register(new \Saxulum\DoctrineMongoDb\Silex\Provider\DoctrineMongoDbProvider(), array(
	'mongodb.options' => array(
		'server' => 'mongodb://localhost:27017',
		'options' => array(
			//'username' => 'root',
			//'password' => '',
			//'db' => ''
		)
	)
));
$test = $app['mongodb']
	->selectDatabase('mydb')
	->selectCollection('mycollection')
	->findOne(array('id' => 'some-id')); // methods in LoggableCollection.php
print_r($test);
*/
//--

//	$app->register(new \Sorien\Provider\DoctrineProfilerServiceProvider());

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

//-- Main Action
$app->get('/', function() use ($app) {
	return $app['twig']->render('benchmark.html.twig', array());
})->bind('homepage'); // give a name to the / route as homepage
//--

//== Run
$app->run();
//==

// end of php code
?>