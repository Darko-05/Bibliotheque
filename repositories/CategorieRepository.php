<?php

    namespace repositories;

    use PDO;

    class CategorieRepository
    {
        private PDO|null $pdo = null;

        public function __construct(PDO $pdo)
        {
            $this->pdo = $pdo;
        }

        public function findAll():array
        {
            return $this->pdo->query("SELECT * FROM categories;")->fetchAll(PDO::FETCH_ASSOC);
        }

        public function findById(int $id):array|null
        {
            $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE id = :id;");
            $stmt->execute([":id" => $id]);

            $resultat = $stmt->fetch(PDO::FETCH_ASSOC);

            return $resultat === false ? null : $resultat;
        }

        public function create(array $donnees):int
        {
            $stmt = $this->pdo->prepare("INSERT INTO categories (nom, description) VALUES (:nom, :description);");
            $stmt->execute([
                ":nom" => $donnees["nom"],
                ":description" => $donnees["description"]
            ]);

            return $this->pdo->lastInsertId();
        }

        public function update(int $id, array $donnees):bool
        {
            $stmt = $this->pdo->prepare("UPDATE categories SET nom = :nom, description = :description WHERE id = :id");
            $stmt->execute([
                ":nom" => $donnees["nom"],
                ":description" => $donnees["description"],
                ":id" => $id
            ]);

            return $stmt->rowCount() > 0;
        }

        public function delete(int $id):bool
        {
            $stmt = $this->pdo->prepare("DELETE FROM categories WHERE id = :id;");
            $stmt->execute([":id" => $id]);

            return $stmt->rowCount() > 0;
        }

    }