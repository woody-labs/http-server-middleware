# Http Server Middleware

Implements [PSR-15](https://www.php-fig.org/psr/psr-15/) PHP Standard.


## Presentation

A middleware component is an individual component participating, often together with other middleware components, in the processing of an incoming request and the creation of a resulting response, as defined by [PSR-7](https://www.php-fig.org/psr/psr-7/).

![Middleware schema](https://github.com/woody-labs/http-server-middleware/raw/master/doc/middleware.jpg)


Middleware are called, one by one, to handle the server request.
The first one to create a response will return it to previous middleware.
You can transmit object between middleware using attribute attached to the request.


## Implementation

### Middleware

````php
namespace App;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Woody\Http\Message\Response;
use Woody\Http\Server\Middleware\MiddlewareInterface;

class MyAppMiddleware implements MiddlewareInterface {

    public function isEnabled(bool $debug): bool
    {
        return true;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getUri()->getPath() == '/test') {
            $data = 'Text 1';
        } else {
            $data = 'Text 2';
        }
        
        return new Response(200, ['Content-Type' => 'application/json'], json_encode($data));
    }
}
````


### Middleware declaration

Note: this sample requires `http-interop/response-sender`, 
available [here](https://packagist.org/packages/http-interop/response-sender).

````php
include 'vendor/autoload.php';

use Woody\Http\Server\Middleware\Dispatcher;
use Woody\Http\Message\ServerRequest;

$request = ServerRequest::fromGlobals();

$dispatcher = new Dispatcher();
$dispatcher->pipe(new LogMiddleware());
$dispatcher->pipe(new ExceptionMiddleware());
$dispatcher->pipe(new SecurityMiddleware());
$dispatcher->pipe(new MyAppMiddleware());

$response = $dispatcher->handle($request);

Http\Response\send($response);
````

### Callback declaration

The `dispatcher` can also accept callback functions.

````php
include 'vendor/autoload.php';

use Woody\Http\Message\Response;
use Woody\Http\Server\Middleware\Dispatcher;
use Woody\Http\Message\ServerRequest;

$request = ServerRequest::fromGlobals();

$dispatcher = new Dispatcher();
$dispatcher->pipe(new LogMiddleware());
$dispatcher->pipe(function(ServerRequest $request, Dispatcher $dispatcher) {
    return new Response(200, ['Content-Type' => 'application/json'], json_encode(['user_id' => 42]));
});

$response = $dispatcher->handle($request);

Http\Response\send($response);
````


## Documentation

[Article on Middleware implementation on PHP](https://www.grafikart.fr/tutoriels/middleware-psr15-904)

[French video](https://www.youtube.com/watch?v=w1FviidvxJc)
