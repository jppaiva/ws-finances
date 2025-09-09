<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use App\Controllers\UsuarioController;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });
    
    $app->get('/usuario', [UsuarioController::class, 'busca']);
    
    $app->any('/{routes:.+}', function (Request $request, Response $response) {
        $response->getBody()->write('Rota não existe!');
        return $response->withStatus(404);
    });
};
