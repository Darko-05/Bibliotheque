<?php

    namespace repositories;

    use PDO;

    class EmpruntRepository
    {
        private PDO|null $pdo = null;

        public function __construct(PDO $pdo)
        {
            $this->pdo = $pdo;
        }

        public function create(array $donnees):int
        {
            $stmt = $this->pdo->prepare("INSERT INTO emprunts (livre_id, membre_id, date_emprunt, date_retour_prevue, date_retour_effective, statut) VALUES (:livre_id, :membre_id, :date_emprunt, :date_retour_prevue, :date_retour_effective, :statut);");
            $stmt->execute([
                ":nom" => $donnees["livre_id"],
                ":email" => $donnees["membre_id"],
                ":mot_de_passe" => password_hash($donnees["date_emprunt"], PASSWORD_DEFAULT),
                ":role" => $donnees["date_retour_prevue"],
                ":statut" => $donnees["date_retour_effective"],
                ":date_inscription" => $donnees["statut"]
            ]);

            return $this->pdo->lastInsertId();
        }

        public function findEnCoursByMembre(int $membreId):array
        {
            $stmt = $this->pdo->prepare("SELECT * FROM emprunts WHERE membre_id = :membre_id AND statut = :statut;");
            $stmt->execute([
               ":membre_id" => $membreId,
               ":statut" => "en_cours"
            ]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function countEnCoursByMembre(int $membreId):int
        {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM emprunts WHERE membre_id = :membre_id AND statut = :statut;");
            $stmt->execute([
                ":membre_id" => $membreId,
                ":statut" => "en_cours"
            ]);

            return (int) $stmt->fetchColumn();
        }

        public function hasEmpruntEnRetard(int $membreId):bool
        {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM emprunts WHERE membre_id = :membre_id AND statut = :statut;");
            $stmt->execute([
                ":membre_id" => $membreId,
                ":statut" => "en_retard"
            ]);

            return (int) $stmt->fetchColumn() > 0;
        }

        public function findHistoriqueByMembre(int $membreId):array
        {
            $stmt = $this->pdo->prepare("SELECT * FROM emprunts WHERE membre_id = :membre_id AND statut = :statut;");
            $stmt->execute([
                ":membre_id" => $membreId,
                ":statut" => "retourne"
            ]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function findAll():array
        {
            return $this->pdo->prepare("
                SELECT 
                e.id,
                e.date_emprunt,
                e.date_retour_prevue,
                e.date_retour_effective,
                e.statut,
                l.titre AS livre_titre,
                l.auteur AS livre_auteur,
                u.nom AS membre_nom,
                u.email AS membre_email
            FROM emprunts e
            JOIN livres l ON e.livre_id = l.id
            JOIN utilisateurs u ON e.membre_id = u.id
            ORDER BY e.date_emprunt DESC;
            ")->fetchAll(PDO::FETCH_ASSOC);
        }

        public function findEnRetard():array
        {
            $stmt = $this->pdo->prepare("SELECT * FROM emprunts WHERE statut = :statut;");
            $stmt->execute([":statut" => "en_retard"]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function marquerRetourne(int $id):bool
        {
            $stmt = $this->pdo->prepare("UPDATE emprunts SET date_retour_effective = :date_retour_effective, statut = :statut WHERE id = :id;");
            $stmt->execute([
                ":date_retour_effective" => date("Y-m-d"),
                ":statut" => "retourne",
                ":id" => $id
            ]);

            return $stmt->rowCount() > 0;
        }
    }