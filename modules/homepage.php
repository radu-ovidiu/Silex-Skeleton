<?php

//----------------------------------------------------- PREVENT DIRECT EXECUTION
if(!defined('SILEX_RUNTIME_READY')) { // this must be defined in the first line of the application
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------

//-- HomePage (Sample)

class MiddlewareController extends AbstractMiddlewareController {

	public function Run() {

		return $this->app['twig']->render('test.html.twig', array('title' => 'Home Page', 'content' => date('Y-m-d H:i:s')));

	} //END FUNCTION

} //END CLASS

// end of php code
?>