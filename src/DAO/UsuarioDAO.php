<?php

namespace App\DAO;

use PDO;
use PDOException;

final class UsuarioDAO extends Conexao {

    public function __construct() {
        parent::__construct();
    }

    public function cadastrarUsuario($data) {
        $sql = "INSERT INTO USUARIO (NOME, EMAIL, SENHA) VALUES (:NOME, :EMAIL, :SENHA)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':NOME', $data['nome'], PDO::PARAM_STR);
        $stmt->bindValue(':EMAIL', $data['email'], PDO::PARAM_STR);
        $stmt->bindValue(':SENHA', md5($data['senha']), PDO::PARAM_STR);
        $stmt->execute();
        $codusuario = $this->pdo->lastInsertId();
        if ($codusuario > 0) {
            $result = array(
                'error' => false, 
                'message' => 'Usuário cadastrado com sucesso', 
                'data' => array('codusuario' => $codusuario)
            );
        } else {
            $result = array(
                'error' => true, 
                'message' => 'Erro ao cadastrar usuário', 
                'data' => []
            );
        }
        return $result;
    }

    public function atualizaUsuario($data) {
        $sql = 'UPDATE USUARIO SET NOME = :NOME, EMAIL = :EMAIL, SENHA = :SENHA WHERE CODUSUARIO = :CODUSUARIO';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':NOME', $data['nome'], PDO::PARAM_STR);
        $stmt->bindValue(':EMAIL', $data['email'], PDO::PARAM_STR);
        $stmt->bindValue(':SENHA', md5($data['senha']), PDO::PARAM_STR);
        $stmt->bindValue(':CODUSUARIO', $data['codusuario'], PDO::PARAM_INT);
        if ($stmt->execute()) {
            $result = array(
                'error' => false, 
                'message' => 'Usuário atualizado com sucesso', 
                'data' => []
            );
        } else {
            $result = array(
                'error' => true, 
                'message' => 'Erro ao atualizar usuário', 
                'data' => []
            );
        }
        
        return $result;
    }

    public function getUsuarios() {
        try {
            $sql = "SELECT CODUSUARIO, NOME, EMAIL FROM usuario";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            // Logar ou tratar erro
            return [];
        }
    }

    public function getUsuarioByEmail($email) {
        try {
            $sql = "SELECT CODUSUARIO, NOME, EMAIL, SENHA
            FROM usuario
            WHERE EMAIL = :EMAIL";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':EMAIL', $email, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            // Logar ou tratar erro
            return [];
        }
    }

    public function getUsuarioByCodUsuario($codusuario) {
        try {
            $sql = "SELECT CODUSUARIO, NOME, EMAIL, SENHA
            FROM usuario
            WHERE EMAIL = :CODUSUARIO";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':CODUSUARIO', $codusuario, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            // Logar ou tratar erro
            return [];
        }
    }
} 