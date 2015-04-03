<?php

//----------------------------------------------------- PREVENT DIRECT EXECUTION
if(!defined('SILEX_RUNTIME_READY')) { // this must be defined in the first line of the application
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------

//--
function autoload__SmartSilex($classname) {
	//--
	$classname = (string) ''.$classname;
	//--
	if(strpos($classname, '\\') !== false) { // libraries with name spaces
		//--
		$parts = explode('\\', $classname);
		//--
		$max = count($parts) - 1; // the last is the class
		$dir = __DIR__.'/libs/';
		for($i=0; $i<$max; $i++) {
			$dir .= $parts[$i].'/';
		} //end for
		$file = $parts[$max];
		//--
		$path = $dir.$file.'.php';
		//--
		if(!is_file($path)) {
			return;
		} //end if
		//--
		require_once($path);
		//--
	} elseif(substr($classname, 0, 5) == 'Model') { // models
		//--
		$path = __DIR__.'/models/'.$classname.'.php';
		//--
		if(!is_file($path)) {
			return;
		} //end if
		//--
		require_once($path);
		//--
	} //end if else
	//--
} //END FUNCTION
//--
spl_autoload_register('autoload__SmartSilex', true, false); // throw / append
//--

// end of php code
?>