<?php

namespace App\Controllers;

use App\DAO\UsuarioDAO;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Response as Response;

final class UsuarioController
{
    public function cadastra(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $nome = $data['nome'] ?? '';
        $email = $data['email'] ?? '';
        $senha = $data['senha'] ?? '';

        $response = $response->withHeader('Content-Type', 'application/json');

        if (empty($nome) || empty($email) || empty($senha)) {
            $response->getBody()->write(json_encode(
                [
                    'error' => true,
                    'message' => 'Todos os campos são obrigatórios',
                    'data' => $data
                ]
            ));
            return $response->withStatus(400);
        }

        $usuarioDAO = new UsuarioDAO();
        $result = $usuarioDAO->cadastrarUsuario($data);
        $response->getBody()->write(json_encode($result));
        return $response->withStatus(200);
    }

    public function atualiza(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $nome = $data['nome'] ?? '';
        $email = $data['email'] ?? '';
        $senha = $data['senha'] ?? '';
        $codusuario = $data['codusuario'] ?? '';

        $response = $response->withHeader('Content-Type', 'application/json');

        if (empty($codusuario) || empty($nome) || empty($email) || empty($senha)) {
            $response->getBody()->write(json_encode(
                [
                    'error' => true,
                    'message' => 'Todos os campos são obrigatórios',
                    'data' => $data
                ]
            ));
            return $response->withStatus(400);
        }

        $usuarioDAO = new UsuarioDAO();
        $result = $usuarioDAO->atualizaUsuario($data);
        $response->getBody()->write(json_encode($result));

        return $response->withStatus(200);
    }

    public function busca(Request $request, Response $response): Response
    {
        $usuarioDAO = new UsuarioDAO();
        $params = $request->getQueryParams();
        // Parametros
        $codusuario = $params['codusuario'] ?? '';
        $email = $params['email'] ?? '';
        $nome = $params['nome'];

        $response = $response->withHeader('Content-Type', 'application/json');

        if (!empty($codusuario)) {
            $result = $usuarioDAO->getUsuarioByCodUsuario($codusuario);
            $response->getBody()->write(json_encode(
                [
                    'error' => false,
                    'message' => count($result) > 0 ? 'Registro encontrado' : 'Nenhum registro encontrado',
                    'data' => $result
                ]
            ));
            return $response->withStatus(200);
        } elseif (!empty($email)) {
            $result = $usuarioDAO->getUsuarioByEmail($email);
            $response->getBody()->write(json_encode(
                [
                    'error' => false,
                    'message' => count($result) > 0 ? 'Registro encontrado' : 'Nenhum registro encontrado',
                    'data' => $result
                ]
            ));
            return $response->withStatus(200);
        } elseif (isset($nome)) {
            $result = $usuarioDAO->getUsuariosByNome($nome);
            $response->getBody()->write(json_encode(
                [
                    'error' => false,
                    'message' => count($result) > 0 ? 'Registros encontrados' : 'Nenhum registro encontrado',
                    'data' => $result
                ]
            ));
            return $response->withStatus(200);
        } else {
            $response->getBody()->write(json_encode(
                [
                    'error' => true,
                    'message' => 'Nenhum parâmetro informado',
                    'data' => []
                ]
            ));
            return $response->withStatus(400);
        }
    }

    public function exclui(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $codusuario = $params['codusuario'] ?? '';

        $response = $response->withHeader('Content-Type', 'application/json');

        if (empty($codusuario)) {
            $response->getBody()->write(json_encode(
                [
                    'error' => true,
                    'message' => 'Nenhum parâmetro informado',
                    'data' => []
                ]
            ));

            return $response->withStatus(400);
        }

        $usuarioDAO = new UsuarioDAO();
        $result = $usuarioDAO->deleteUsuario($codusuario);
        $response->getBody()->write(json_encode($result));
        return $response->withStatus(200);
    }
}
