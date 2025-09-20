<?php

namespace App\Controllers;

use App\DAO\UsuarioDAO;
use Firebase\JWT\JWT;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Response as Response;

final class AuthController 
{
    public function login(Request $request, Response $response, $args): Response
    {
        $data = $request->getParsedBody();
        $email = $data['email'] ?? '';
        $senha = $data['senha'] ?? '';

        // Caso e-mail ou senha não sejam informados
        if (empty($email) || empty($senha)) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Usuário e senha devem ser informados.',
                'data' => []
            ], 400);
        }

        $usuarioDAO = new UsuarioDAO();
        $result = $usuarioDAO->getUsuarioByEmail($email);

        try {
            if (!empty($result)) {
                $usuario = $result[0];

                if (password_verify($senha, $usuario['SENHA'])) {
                    // Criar payload do JWT
                    $payload = [
                        'iat' => time(),                   
                        'exp' => time() + 3600,            
                        'sub' => $usuario['CODUSUARIO'],   
                        'email' => $usuario['EMAIL']
                    ];

                    // Melhor armazenar em variável de ambiente (.env)
                    $secretKey = $_ENV['JWT_SECRET'];
                    $jwt = JWT::encode($payload, $secretKey, 'HS256');

                    return $this->jsonResponse($response, [
                        'error' => false,
                        'message' => 'Login realizado com sucesso',
                        'data' => [
                            'token' => $jwt,
                            'usuario' => [
                                'CODUSUARIO' => $usuario['CODUSUARIO'],
                                'NOME' => $usuario['NOME'],
                                'EMAIL' => $usuario['EMAIL']
                            ]
                        ]
                    ], 200);
                } else {
                    return $this->jsonResponse($response, [
                        'error' => true,
                        'message' => 'Senha incorreta',
                        'data' => []
                    ], 401);
                }
            } else {
                return $this->jsonResponse($response, [
                    'error' => true,
                    'message' => 'Usuário não encontrado',
                    'data' => []
                ], 404);
            }
        } catch (\Exception $e) {
            return $this->jsonResponse($response, [
                'error' => true,
                'message' => 'Erro ao realizar login. Erro: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function logout(Request $request, Response $response, $args): Response
    {
        return $response;
    }

    private function jsonResponse(Response $response, array $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}
