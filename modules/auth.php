<?php

//-- Security
$app->register(new \Silex\Provider\SecurityServiceProvider(), array(
	'security.firewalls' => array(
		'secured' => array(
			'pattern' => '^/admin',
			'http' => true,
			'users' => array(
				// raw password is test
				'admin' => array('ROLE_ADMIN', 'ee26b0dd4af7e749aa1a8ee3c10ae9923f618980772e473f8819a5d4940e0db27ac185f8a0e1d5f84f88bc887fd67b143732c304cc5fa9ad8e6f57f50028a8ff'),
			),
		)
	)
));
//--

//--
$app['security.encoder.digest'] = $app->share(function ($app) {
    // use the sha512 algorithm
    // don't base64 encode the password
    // use only 1 iteration
    return new \Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder('sha512', false, 1);
});
//--

// end of php code
?>