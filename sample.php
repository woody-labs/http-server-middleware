<?php

include 'vendor/autoload.php';

use Woody\Http\Message\Response;
use Woody\Http\Server\Middleware\Dispatcher;
use Woody\Http\Message\ServerRequest;

$request = ServerRequest::fromGlobals();

$dispatcher = new Dispatcher();
$dispatcher->pipe(function(ServerRequest $request, Dispatcher $dispatcher) {
    return new Response(200, ['Content-Type' => 'application/json'], json_encode(['user_id' => 42]));
});

$response = $dispatcher->handle($request);

Http\Response\send($response);
