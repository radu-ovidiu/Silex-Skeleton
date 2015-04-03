<?php

//----------------------------------------------------- PREVENT DIRECT EXECUTION
if(!defined('SILEX_RUNTIME_READY')) { // this must be defined in the first line of the application
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------

//--
define('SMART_APP_DEBUG', true);
//--

//--
date_default_timezone_set('UTC');
//--

//--
$configs = [
	'auth' => [
		'username' => 'admin',
		'password' => 'test'
	],
	'dbs.options' => [
		'sqlite' => [
			'driver' 	=> 'pdo_sqlite',
			'host' 		=> null,
			'port' 		=> null,
			'dbname' 	=> null,
			'user' 		=> null,
			'password' 	=> null,
			'path' => 	__DIR__.'/../tmp/db.sqlite3',
			//'charset' 	=> 'utf8',
		]
	],
/*
	'mongodb.options' => [
		'server' => 'mongodb://localhost:27017',
		'options' => [
			//'username' => 'root',
			//'password' => '',
			//'db' => ''
		]
	]
*/
];
//--

// end of php code
?>