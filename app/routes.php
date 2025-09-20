<?php

declare(strict_types=1);

use App\Controllers\AuthController;
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
    $app->post('/usuario', [UsuarioController::class, 'cadastra']);
    $app->put('/usuario', [UsuarioController::class, 'atualiza']);
    $app->delete('/usuario', [UsuarioController::class, 'exclui']);

    $app->post('/login', [AuthController::class, 'login']);
    $app->put('/logoff', [AuthController::class, 'logoff']);
    
    $app->any('/{routes:.+}', function (Request $request, Response $response) {
        $response->getBody()->write('Rota não existe!');
        return $response->withStatus(404);
    });
};
