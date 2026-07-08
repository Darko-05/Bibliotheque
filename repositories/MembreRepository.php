<?php

    namespace repositories;

    use PDO;

    class MembreRepository
    {
        private PDO|null $pdo = null;

        public function __construct(PDO $pdo)
        {
            $this->pdo = $pdo;
        }

        public function findByEmail(string $email):array|null
        {
            $stmt = $this->pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
            $stmt->execute([":email" => $email]);

            $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultat === false ? null : $resultat;
        }

        public function findById(int $id):array|null
        {
            $stmt = $this->pdo->prepare("SELECT * FROM utilisateurs WHERE id = :id;");
            $stmt->execute([":id" => $id]);

            $resultat = $stmt->fetch(PDO::FETCH_ASSOC);

            return $resultat === false ? null : $resultat;
        }

        public function findAll():array
        {
            return $this->pdo->query("SELECT * FROM utilisateurs;")->fetchAll(PDO::FETCH_ASSOC);
        }

        public function create(array $donnees):int
        {
            $stmt = $this->pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, role, statut, date_inscription) VALUES (:nom, :email, :mot_de_passe, :role, :statut, :date_inscription);");
            $stmt->execute([
                ":nom" => $donnees["nom"],
                ":email" => $donnees["email"],
                ":mot_de_passe" => $donnees["mot_de_passe"],
                ":role" => $donnees["role"],
                ":statut" => $donnees["statut"],
                ":date_inscription" => $donnees["date_inscription"]
            ]);

            return $this->pdo->lastInsertId();
        }

        public function updateEmail(string $email, int $membreId):bool
        {
            $stmt = $this->pdo->prepare("UPDATE utilisateurs SET email = :email WHERE id = :id;");
            $stmt->execute([
                ":email" => $email,
                ":id" => $membreId
            ]);

            return $stmt->rowCount() > 0;
        }

        public function updateName(string $nom, int $membreId):bool
        {
            $stmt = $this->pdo->prepare("UPDATE utilisateurs SET nom = :nom WHERE id = :id;");
            $stmt->execute([
                ":nom" => $nom,
                ":id" => $membreId
            ]);

            return $stmt->rowCount() > 0;
        }

        public function updateStatut(int $id, string $statut):bool
        {
            $stmt = $this->pdo->prepare("UPDATE utilisateurs SET statut = :statut WHERE id = :id");
            $stmt->execute([
                ":statut" => $statut,
                ":id" => $id
            ]);

            return $stmt->rowCount() > 0;
        }
    }