<?php

//-- Home Page
$app->get('/', function() use ($app) {
	return $app['twig']->render('test.html.twig', array('title' => 'Homepage', 'content' => ''));
})->bind('homepage'); // give a name to the / route as homepage
//--

//-- Administration
$app->get('/admin/{action}', function($action) use ($app) {
	return $app['twig']->render('test.html.twig', array('title' => 'Administration Area', 'content' => '[Action: '.$action.']'));
})->value('action', 'default')->bind('administration'); // give a name to the / route as homepage
//--

$app->get('/test/{action}', function($action) use ($app, $configs) {

	switch((string)$action) {
		//--
		case 'session':
			$app['session']->set('mytest', true);
			if($app['session']->get('mytest') !== true) {
				throw new Exception('Session Test Failed !');
			} //end if
			$out = $app['twig']->render('test.html.twig', array('title' => 'Test Session', 'content' => 'OK'));
			break;
		//--
		case 'sqlite':
			//--
			if(is_array($configs['dbs.options'])) {
				if(!is_file($configs['dbs.options']['sqlite']['path'])) {
					$app['dbs']['sqlite']->executeQuery(
						'CREATE TABLE "table_main_sample" ("id" character varying(10) NOT NULL, "name" character varying(100) NOT NULL, "description" text NOT NULL, "dtime" text NOT NULL )',
						array()
					);
					for($i=0; $i<9; $i++) {
						$test = $app['dbs']['sqlite']->executeQuery(
							' INSERT INTO "table_main_sample" ("id","name","description","dtime") VALUES (? , ?, ?, ?)',
							array(($i+1), 'Name "'.($i+1).'"', "Description '".($i+1)."'", date('Y-m-d H:i:s O'))
						)->rowCount();
						if($test != 1) {
							print_r($test);
							break;
						} //end if
					} //end for
				} //end if
				$test = $app['dbs']['sqlite']->fetchAssoc("SELECT * FROM table_main_sample WHERE id = ?", array(1));
				$out = $app['twig']->render('test.html.twig', array('title' => 'Test SQLite', 'content' => ''.print_r($test,1)));
			} //end if
			//--
			break;
		//--
		case 'mongodb':
			if(is_array($configs['mongodb.options'])) {
				$test = $app['mongodb']->selectDatabase('mydb')->selectCollection('mycollection')->findOne(array('_id' => 'some-id')); // methods in LoggableCollection.php
				$out = $app['twig']->render('test.html.twig', array('title' => 'Test MongoDB', 'content' => ''.print_r($test,1)));
			} //end if
			break;
		//--
		default:
			$out = $app['twig']->render('test.html.twig', array('title' => 'Test', 'content' => ''));
	} //end switch

	return $out;

})->value('action', 'default')->bind('test');

// end of php code
?>