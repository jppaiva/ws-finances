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

    public function busca(Request $request, Response $response): Response
    {
        $usuarioDAO = new UsuarioDAO();
        $params = $request->getQueryParams();
        // Parametros
        $codusuario = $params['codusuario'] ?? '';
        $email = $params['email'] ?? '';
        $nome = $params['nome'];

        if (!empty($codusuario)) {
            $response = $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
            $result = $usuarioDAO->getUsuarioByCodUsuario($codusuario);
            $response->getBody()->write(json_encode(
                [
                    'error' => false,
                    'message' => count($result) > 0 ? 'Registro encontrado' : 'Nenhum registro encontrado',
                    'data' => $result
                ]
            ));
        } elseif (!empty($email)) {
            $response = $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
            $result = $usuarioDAO->getUsuarioByEmail($email);
            $response->getBody()->write(json_encode(
                [
                    'error' => false,
                    'message' => count($result) > 0 ? 'Registro encontrado' : 'Nenhum registro encontrado',
                    'data' => $result
                ]
            ));
        } elseif (isset($nome)) {
            $response = $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
            $result = $usuarioDAO->getUsuariosByNome($nome);
            $response = $response->getBody()->write(json_encode(
                [
                    'error' => false,
                    'message' => count($result) > 0 ? 'Registros encontrados' : 'Nenhum registro encontrado',
                    'data' => $result
                ]
            ));
        } else {
            $response = $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
            $response->getBody()->write(json_encode(
                [
                    'error' => true,
                    'message' => 'Nenhum parâmetro informado',
                    'data' => []
                ]
            ));
        }
        return $response;
    }

    public function excluir(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $codusuario = $params['codusuario'] ?? '';

        if (empty($codusuario)) {
            $response = $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
            $response->getBody()->write(json_encode(
                [
                    'error' => true,
                    'message' => 'Nenhum parâmetro informado',
                    'data' => []
                ]
            ));

            return $response;
        }

        $response = $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
        $usuarioDAO = new UsuarioDAO();
        $result = $usuarioDAO->deleteUsuario($codusuario);
        $response->getBody()->write(json_encode($result));
        return $response;
    }   
}