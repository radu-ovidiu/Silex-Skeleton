<?php

//--
date_default_timezone_set('UTC');
//--
ini_set('default_charset', 'UTF-8');
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
ini_set('display_errors', '1');	// display runtime errors
ini_set('error_log', __DIR__.'/../cache/phperrors.log'); // record them to a log
//--
define('SMART_APP_DEBUG', true);
//--

require_once __DIR__.'/../vendor/autoload.php';

//-- debug
if(SMART_APP_DEBUG === true) {
	\Symfony\Component\Debug\Debug::enable();
} //end if
//--

$app = new \Silex\Application();

if(SMART_APP_DEBUG === true) {
	$app['debug'] = true;
} //end if

$app->register(new \Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new \Silex\Provider\ValidatorServiceProvider());
$app->register(new \Silex\Provider\ServiceControllerServiceProvider());
$app->register(new \Silex\Provider\HttpFragmentServiceProvider());

if(SMART_APP_DEBUG === true) {
	$app->register(new \Silex\Provider\TwigServiceProvider(), array(
		'twig.path' => __DIR__.'/../templates',
	));
} else {
	$app->register(new \Silex\Provider\TwigServiceProvider(), array(
		'twig.path' => __DIR__.'/../templates',
		'twig.options' => array('cache' => __DIR__.'/../cache/twig'),
	));
} //end if else
$app['twig'] = $app->extend('twig', function ($twig, $app) { // add custom globals, filters, tags, ...
	$twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) use ($app) {
		return $app['request_stack']->getMasterRequest()->getBasepath().'/'.$asset;
	}));
	return $twig;
});

if(SMART_APP_DEBUG === true) {
	$app->register(new \Silex\Provider\WebProfilerServiceProvider(), array(
		'profiler.cache_dir' => __DIR__.'/../cache/profiler',
		'web_profiler.debug_toolbar.enable' => true,
		'web_profiler.debug_toolbar.intercept_redirects' => false,
		//'web_profiler.debug_toolbar.excluded_ajax_paths' => '/_profiler'
	));
} //end if

$app->get('/', function() use ($app) {
	return $app['twig']->render('benchmark.html.twig', array());
}); // ->bind('homepage');

$app->run();

// end of php code
?>