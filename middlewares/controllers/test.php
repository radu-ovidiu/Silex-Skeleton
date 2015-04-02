<?php

//----------------------------------------------------- PREVENT DIRECT EXECUTION
if(!defined('SILEX_RUNTIME_READY')) { // this must be defined in the first line of the application
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------

//-- Tests (Sample)

class MiddlewareController extends AbstractMiddlewareController {

	public function Run() {

		switch((string)$this->action) {
			//--
			case 'session':
				$this->app['session']->set('mytest', true);
				if($this->app['session']->get('mytest') !== true) {
					throw new Exception('Session Test Failed !');
				} //end if
				$out = $this->app['twig']->render('test.html.twig', array('title' => 'Test Session', 'content' => 'OK'));
				break;
			//--
			case 'sqlite':
				//--
				if(@is_array($this->configs['dbs.options'])) {
					//--
					$db = new \UXM\Db($this->app['dbs']['sqlite']);
					//--
					if(!is_file($this->configs['dbs.options']['sqlite']['path'])) {
						//--
						$db->writeQuery('BEGIN');
						$db->writeQuery(
							'CREATE TABLE "table_main_sample" ("id" character varying(10) NOT NULL, "name" character varying(100) NOT NULL, "description" text NOT NULL, "dtime" text NOT NULL )',
							array()
						);
						for($i=0; $i<9; $i++) {
							$test = $db->writeQuery(
								' INSERT INTO "table_main_sample" ("id","name","description","dtime") VALUES (?,?,?,?)',
								array(($i+1), 'Name "'.($i+1).'"', "Description '".($i+1)."'", date('Y-m-d H:i:s O'))
							);
							if($test != 1) {
								print_r($test);
								break;
							} //end if
						} //end for
						//--
						$db->writeQuery('COMMIT');
						//--
					} //end if
					//--
					$test = $db->readQuery("SELECT * FROM table_main_sample WHERE id < ?", array(4));
					//--
					$out = $this->app['twig']->render('test.html.twig', array('title' => 'Test SQLite', 'content' => ''.print_r($test,1)));
					//--
				} //end if
				//--
				break;
			//--
			case 'mongodb':
				if(@is_array($this->configs['mongodb.options'])) {
					$test = $this->app['mongodb']->selectDatabase('mydb')->selectCollection('mycollection')->findOne(array('_id' => 'some-id')); // methods in LoggableCollection.php
					$out = $this->app['twig']->render('test.html.twig', array('title' => 'Test MongoDB', 'content' => ''.print_r($test,1)));
				} //end if
				break;
			//--
			default:
				$request = (array) $_REQUEST;
				$request['a'] = (string) ''.$_REQUEST['a'];
				if($request['a'] == '') {
					$request['a'] = 'default';
				} //end if
				$out = $this->app['twig']->render('test.html.twig', array('title' => 'Test', 'content' => ''.print_r($request,1)));
		} //end switch

		return $out;

	} //END FUNCTION

} //END CLASS

// end of php code
?>