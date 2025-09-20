<?php

namespace App\Controllers;

use App\DAO\UsuarioDAO;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Response as Response;

final class UsuarioController {
    public function cadastra(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $nome = $data['nome'] ?? '';
        $email = $data['email'] ?? '';
        $senha = $data['senha'] ?? '';
        if (empty($nome) || empty($email) || empty($senha)) {
            $response->getBody()->write(json_encode(
                [
                    'error' => true,
                    'message' => 'Todos os campos são obrigatórios',
                    'data' => $data
                ]
            ));
            return $response;
        }

        $usuarioDAO = new UsuarioDAO();
        $result = $usuarioDAO->cadastrarUsuario($data);
        $response->getBody()->write(json_encode($result));
        return $response;
    }


    public function atualiza(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $nome = $data['nome'] ?? '';
        $email = $data['email'] ?? '';
        $senha = $data['senha'] ?? '';
        $codusuario = $data['codusuario'] ?? '';

        if (empty($codusuario) || empty($nome) || empty($email) || empty($senha)) {
            $response->getBody()->write(json_encode(
                [
                    'error' => true,
                    'message' => 'Todos os campos são obrigatórios',
                    'data' => $data
                ]
            ));
            return $response;
        }

        $usuarioDAO = new UsuarioDAO();
        $result = $usuarioDAO->atualizaUsuario($data);
        $response->getBody()->write(json_encode($result));

        return $response;
    }

    public function busca(Request $request, Response $response): Response {
        $usuarioDAO = new UsuarioDAO();
        $result = $usuarioDAO->getUSuarios();
        $response->getBody()->write(json_encode(
            [
                'error' => false,
                'message' => count($result) > 0 ? 'Registros encontrados': 'Nenhum registro encontrado',
                'data' => $result
            ]
        ));
        return $response;
    }

    public function excluir(Request $request, Response $response): Response {
        return $response;
    }   
}