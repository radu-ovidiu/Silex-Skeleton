<?php

//----------------------------------------------------- PREVENT DIRECT EXECUTION
if(!defined('SILEX_RUNTIME_READY')) { // this must be defined in the first line of the application
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------

//-- Administration  (Sample)

class MiddlewareController extends AbstractMiddlewareController {

	public function Run() {

		$username = (string) $this->app['request']->server->get('PHP_AUTH_USER', '');
		$password = (string) $this->app['request']->server->get('PHP_AUTH_PW', '');

		if(((string)$this->action == 'logout') || ((string)$username == '') || ((string)$username != (string)$this->configs['auth']['username']) || ((string)$password == '') || ((string)$password != (string)$this->configs['auth']['password'])) {
			$response = new \Symfony\Component\HttpFoundation\Response();
			$response->headers->set('WWW-Authenticate', sprintf('Basic realm="%s"', 'Admin Area'));
			$this->app['session']->set('logged_in_user', array('username' => $username));
			if('logout' === $this->action) {
				if((string)$username == '') {
					$response->setStatusCode(307);
					$response->headers->set('Location', $this->app['url_generator']->generate('app'));
				} else {
					$response->setStatusCode(401, 'Authorization Required');
				} //end if else
			} else {
				$response->setStatusCode(401, 'Authorization Required');
			} //end if
			return $response;
		} //end if

		return $this->app['twig']->render('test.html.twig', array('title' => 'Administration Area', 'content' => '[Action: '.$this->action.']'));

	} //END FUNCTION

} //END CLASS

// end of php code
?>