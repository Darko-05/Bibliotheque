<?php

    class Database
    {
        private static PDO|null $pdo = null;

        private function __construct() {}

        public static function getConnection():PDO|null
        {
            if (self::$pdo === null) {
                try {
                    self::$pdo = new PDO (
                        "pgsql:host=localhost;dbname=bibliotheque",
                        "postgres",
                        "Blacknight2#",
                        [
                            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // lève une exception si erreur
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // retourne des tableaux associatifs
                            PDO::ATTR_EMULATE_PREPARES   => false,                   // requêtes préparées natives
                        ]
                    );
                } catch (PDOException $e) {
                    echo "Erreur de connexion : ". $e->getMessage();
                }
            }

            return self::$pdo;
        }

    }