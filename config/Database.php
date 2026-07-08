<?php

class Database
{
    private static PDO|null $pdo = null;

    private function __construct() {}

    public static function getConnection():PDO|null
    {
        if (self::$pdo === null) {
            $host     = getenv('DB_HOST') ?: 'localhost';
            $port     = getenv('DB_PORT') ?: '5432';
            $dbname   = getenv('DB_NAME') ?: 'bibliotheque';
            $user     = getenv('DB_USER') ?: 'postgres';
            $password = getenv('DB_PASSWORD') ?: 'Blacknight2#';

            try {
                self::$pdo = new PDO (
                    "pgsql:host=$host;port=$port;dbname=$dbname",
                    $user,
                    $password,
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ]
                );
            } catch (PDOException $e) {
                echo "Erreur de connexion : ". $e->getMessage();
            }
        }

        return self::$pdo;
    }

}