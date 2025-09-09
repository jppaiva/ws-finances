<?php

namespace App\DAO;

use PDO;

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

    public function getUsuarios() {
        $sql = "SELECT * FROM usuario";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
} 