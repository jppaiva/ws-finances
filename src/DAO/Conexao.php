<?php

namespace App\DAO;

use PDO;
use PDOException;

abstract class Conexao
{
    /**
     * @var PDO
     */
    protected $pdo;

    public function __construct()
    {
        try {
            $this->pdo = new PDO(
                'mysql:host=localhost;dbname=finances',
                'root',
                '',
                [
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES UTF8, lc_time_names = 'pt_BR'",
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                    PDO::ATTR_CASE => PDO::CASE_NATURAL,
                    PDO::MYSQL_ATTR_FOUND_ROWS => true,
                    PDO::ATTR_STRINGIFY_FETCHES => true
                ]
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo '<pre>';
            print_r($e);
            echo '<pre>';
        }
    }

    public function fecharConexao()
    {
        $this->pdo = null;
    }
}
