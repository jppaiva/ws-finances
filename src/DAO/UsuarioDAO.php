<?php

namespace App\DAO;

use PDO;
use PDOException;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\Stmt\Return_;

final class UsuarioDAO extends Conexao
{
    public function __construct()
    {
        parent::__construct();
    }

    public function cadastrarUsuario($data)
    {
        $sql = "INSERT INTO USUARIO (NOME, EMAIL, SENHA) VALUES (:NOME, :EMAIL, :SENHA)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':NOME', $data['nome'], PDO::PARAM_STR);
        $stmt->bindValue(':EMAIL', $data['email'], PDO::PARAM_STR);
        $stmt->bindValue(':SENHA', password_hash($data['senha'], PASSWORD_DEFAULT), PDO::PARAM_STR);
        $stmt->execute();
        $codusuario = $this->pdo->lastInsertId();
        if ($codusuario > 0) {
            $result = [
                'error' => false,
                'message' => 'Usuário cadastrado com sucesso',
                'data' => ['codusuario' => $codusuario]
            ];
        } else {
            $result = [
                'error' => true,
                'message' => 'Erro ao cadastrar usuário',
                'data' => []
            ];
        }
        return $result;
    }

    public function atualizaUsuario($data)
    {
        $sql = 'UPDATE USUARIO SET NOME = :NOME, EMAIL = :EMAIL, SENHA = :SENHA WHERE CODUSUARIO = :CODUSUARIO';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':NOME', $data['nome'], PDO::PARAM_STR);
        $stmt->bindValue(':EMAIL', $data['email'], PDO::PARAM_STR);
        $stmt->bindValue(':SENHA', md5($data['senha']), PDO::PARAM_STR);
        $stmt->bindValue(':CODUSUARIO', $data['codusuario'], PDO::PARAM_INT);
        if ($stmt->execute()) {
            $result = [
                'error' => false,
                'message' => 'Usuário atualizado com sucesso',
                'data' => []
            ];
        } else {
            $result = [
                'error' => true,
                'message' => 'Erro ao atualizar usuário',
                'data' => []
            ];
        }

        return $result;
    }

    public function getUsuariosByNome(string $nome): array
    {
        try {
            $sql = "SELECT CODUSUARIO, NOME, EMAIL
                    FROM usuario
                    WHERE NOME LIKE :NOME";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':NOME', '%' . $nome . '%', PDO::PARAM_STR);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getUsuarioByEmail($email)
    {
        try {
            $sql = "SELECT CODUSUARIO, NOME, EMAIL, SENHA
            FROM usuario
            WHERE EMAIL = :EMAIL";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':EMAIL', $email, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC) ? : [];
        } catch (PDOException $e) {
            // Logar ou tratar erro
            return [];
        }
    }

    public function getUsuarioByCodUsuario($codusuario)
    {
        try {
            $sql = "SELECT CODUSUARIO, NOME, EMAIL, SENHA
            FROM usuario
            WHERE EMAIL = :CODUSUARIO";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':CODUSUARIO', $codusuario, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC) ? : [];
        } catch (PDOException $e) {
            // Logar ou tratar erro
            return [];
        }
    }

    public function deleteUsuario($codusuario)
    {
        try {
            $sql = "UPDATE USUARIO SET EXCLUIDO = 1 WHERE CODUSUARIO = :CODUSUARIO";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':CODUSUARIO', $codusuario, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return [
                    'error' => false,
                    'message' => 'Usuário excluído com sucesso',
                    'data' => []
                ];
            } else {
                return [
                    'error' => true,
                    'message' => 'Falha ao excluir, usuário inexistente',
                    'data' => []
                ];
            }
        } catch (PDOException $e) {
            return [
                'error' => true,
                'message' => 'Erro ao excluir usuário. erro: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }
}
