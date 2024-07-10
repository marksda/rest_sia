<?php

use Phalcon\Mvc\Micro;
use MyApp\Controllers\AbstractHttpException;
use Phalcon\Http\Request\Exception as PhalconException;

(new Phalcon\Support\Debug())->listen();

try {
    // Loading Configs
    $config = require(__DIR__ . '/../app/config/config.php');

	// Autoloading classes
    require(__DIR__ . '/../app/config/loader.php');
    
    // Loading Di container
    $container = require(__DIR__ . '/../app/config/di.php');

    $app = new Micro($container);

    require(__DIR__ . '/../app/config/routes.php');

	$app->options('/{catch:(.*)}', function() use ($app) { 
		$app->response->setStatusCode(200, "OK")->send();
	});
	
	$app->before(
		function() use ($app) {
			$origin = $app->request->getHeader("ORIGIN") ? $app->request->getHeader("ORIGIN") : '*';
			
			$app->response->setHeader("Access-Control-Allow-Origin", $origin)
				->setHeader("Access-Control-Allow-Methods", 'GET,PUT,POST,DELETE,OPTIONS')
				->setHeader("Access-Control-Allow-Headers", 'Origin, X-Requested-With, Content-Range, Content-Disposition, Content-Type, Authorization')
				->setHeader("Access-Control-Allow-Credentials", true);
		}
	);

	$app->after(
        function () use ($app) {
			if (strtoupper($app->request->getMethod()) != 'OPTIONS') {
				// Getting the return value of method
				$return = $app->getReturnedValue();

				if (is_array($return)) {
					// Transforming arrays to JSON
					$app->response->setContent(json_encode($return));
				} elseif (!strlen($return)) {
					// Successful response without any content
					$app->response->setStatusCode('204', 'No Content');
				} else {
					// Unexpected response
					throw new Exception('Bad Response');
				}

				// $app->response->setContent(json_encode($return));

				// Sending response to the client
				$app->response->send();
			}			
		}
    );

    $app->handle($_SERVER["REQUEST_URI"]);
} catch (AbstractHttpException $e) {
    $response = $app->response;
	$response->setStatusCode($e->getCode(), $e->getMessage());
	$response->setJsonContent($e->getAppError());
	$response->send();
} catch (PhalconException $e) {
	$app->response->setStatusCode(400, 'Bad request')
	              ->setJsonContent([
		              AbstractHttpException::KEY_CODE    => 400,
		              AbstractHttpException::KEY_MESSAGE => 'Bad request'
	              ])
	              ->send();
} catch (Exception $e) {
	// Standard error format
	$result = [
		'error' => 500,
		'error_description' => 'Some error occurred on the server.'
		// AbstractHttpException::KEY_CODE    => 500,
		// AbstractHttpException::KEY_MESSAGE => 'Some error occurred on the server.'
	];

	// Sending error response
	$app->response->setStatusCode(500, 'Internal Server Error')
	              ->setJsonContent($result)
	              ->send();
}