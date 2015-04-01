<?php

//--
define('SMART_APP_DEBUG', true);
//--

//--
date_default_timezone_set('UTC');
//--

//--
$configs = [
	'dbs.options' => [
		'sqlite' => [
			'driver' 	=> 'pdo_sqlite',
			'host' 		=> null,
			'port' 		=> null,
			'dbname' 	=> null,
			'user' 		=> null,
			'password' 	=> null,
			'path' => 	__DIR__.'/../tmp/db.sqlite',
			//'charset' 	=> 'utf8',
		]
	]
];
//--

// end of php code
?>