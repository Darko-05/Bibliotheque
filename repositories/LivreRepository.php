<?php

    namespace repositories;

    use PDO;

    class LivreRepository
    {
        private PDO|null $pdo;

        public function __construct(PDO $pdo)
        {
            $this->pdo = $pdo;
        }

        public function findAll():array
        {
            return $this->pdo->query("SELECT * FROM livres;")->fetchAll(PDO::FETCH_ASSOC);
        }

        public function findById(int $id):array
        {
            $stmt = $this->pdo->prepare("SELECT * FROM livres WHERE id = :id;");
            $stmt->execute([":id" => $id]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function findByCategorie(int $categorieId):array
        {
            $stmt = $this->pdo->prepare("SELECT * FROM livres WHERE categorie_id = :categorie_id;");
            $stmt->execute([":categorie_id" => $categorieId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function findByMotCleAndCategorie(int $categorieId, string $motcle):array
        {
            $stmt = $this->pdo->prepare("SELECT * FROM livres WHERE categorie_id = :categorie_id AND titre LIKE :titre;");
            $stmt->execute([
                ":categorie_id" => $categorieId,
                ":titre" => "%$motcle%"
            ]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function findByMotCle(string $motcle):array
        {
            $stmt = $this->pdo->prepare("SELECT * FROM livres WHERE titre LIKE :titre OR auteur LIKE :auteur;");
            $stmt->execute([
                ":titre" => "%$motcle%",
                ":auteur" => "%$motcle%"
            ]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function create(array $donnees):int
        {
            $stmt = $this->pdo->prepare("INSERT INTO livres (titre, auteur, categorie_id, resume, couverture, exemplaires_total, exemplaires_disponibles, date_ajout) VALUES (:titre, :auteur, :categorie_id, :resume, :couverture, :exemplaires_total, :exemplaires_disponibles, :date_ajout);");
            $stmt->execute([
                ":titre" => $donnees["titre"],
                ":auteur" => $donnees["auteur"],
                ":categorie_id" => $donnees["categorie_id"],
                ":resume" => $donnees["resume"],
                ":couverture" => $donnees["couverture"],
                ":exemplaires_total" => $donnees["exemplaires_total"],
                ":exemplaires_disponibles" => $donnees["exemplaires_disponibles"],
                ":date_ajout" => $donnees["date_ajout"]
            ]);

            return (int) $this->pdo->lastInsertId();
        }

        public function update(int $id, array $donnees):bool
        {
            $stmt = $this->pdo->prepare("UPDATE livres SET titre = :titre, auteur = :auteur, categorie_id = :categorie_id, resume = :resume, couverture = :couverture, exemplaires_total = :exemplaires_total, exemplaires_disponibles = :exemplaires_disponibles WHERE id = :id;");
            return $stmt->execute([
                ":titre" => $donnees["titre"],
                ":auteur" => $donnees["auteur"],
                ":categorie_id" => $donnees["categorie_id"],
                ":resume" => $donnees["resume"],
                ":couverture" => $donnees["couverture"],
                ":exemplaires_total" => $donnees["exemplaires_total"],
                ":exemplaires_disponibles" => $donnees["exemplaires_disponibles"],
                ":id" => $id
            ]);
        }

        public function delete(int $id):bool
        {
            $stmt = $this->pdo->prepare("DELETE FROM livres WHERE id = :id;");
            $stmt->execute([":id" => $id]);

            return $stmt->rowCount() > 0;
        }

        public function incrementerDisponibles(int $id):bool
        {
            $stmt = $this->pdo->prepare("UPDATE livres SET exemplaires_disponibles = exemplaires_disponibles + 1 WHERE id = :id;");
            $stmt->execute([":id" => $id]);

            return $stmt->rowCount() > 0;
        }

        public function decrementerDisponibles(int $id):bool
        {
            $stmt = $this->pdo->prepare("UPDATE livres SET exemplaires_disponibles = exemplaires_disponibles - 1 WHERE id = :id;");
            $stmt->execute([":id" => $id]);

            return $stmt->rowCount() > 0;
        }

    }