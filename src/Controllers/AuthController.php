<?php

namespace App\Controllers;

use App\DAO\UsuarioDAO;
use Firebase\JWT\JWT;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Response as Response;

final class AuthController {
    public function login(Request $request, Response $response, $args): Response
    {
        $data = $request->getParsedBody();
        $email = $data['email'] ?? '';
        $senha = $data['senha'] ?? '';

        if (empty($email) || empty($senha)) {
            $response->getBody()->write(json_encode(
                [
                    'error' => true,
                    'message' => 'Usuário e senha devem ser informados.',
                    'data' => $data
                ]
            ));
            return $response;
        }

        $usuarioDAO = new UsuarioDAO();
        $result = $usuarioDAO->getUsuarioByEmail($email);

        try {
            if (empty($result)) {
                $usuario = $result[0];
                // Verifica senha   
                if ($usuario['SENHA'] == md5($senha)) {
                    // 3. Criar payload do JWT
                    $payload = [
                        'iat' => time(),                   // timestamp atual
                        'exp' => time() + 3600,            // expira em 1 hora
                        'sub' => $usuario['CODUSUARIO'],   // id do usuário
                        'email' => $usuario['EMAIL']
                    ];

                    $secretKey = 'SUA_CHAVE_SECRETA_AQUI';
                    $jwt = JWT::encode($payload, $secretKey, 'HS256');

                    $response->getBody()->write(json_encode(
                        [
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
                        ]
                    ));
                    return $response;
                } else {
                        $response->getBody()->write(json_encode(
                            [
                                'error' => true,
                                'message' => 'Senha incorreta',
                                'data' => $data
                            ]
                        ));
                        return $response;
                }
            } else {
                $response->getBody()->write(json_encode(
                    [
                        'error' => true,
                        'message' => 'Usuário não encontrado',
                        'data' => $data
                    ]
                ));
                return $response;
            }
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(
                [
                    'error' => true,
                    'message' => 'Erro ao realizar login. Erro: ' . $e->getMessage(),
                    'data' => []
                ]
            ));
            return $response;
        }

        return $response;
    }

    public function logout(Request $request, Response $response, $args): Response
    {
        return $response;
    }
}