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
                ":livre_id" => $donnees["livre_id"],
                ":membre_id" => $donnees["membre_id"],
                ":date_emprunt" => $donnees["date_emprunt"],
                ":date_retour_prevue" => $donnees["date_retour_prevue"],
                ":date_retour_effective" => $donnees["date_retour_effective"],
                ":statut" => $donnees["statut"]
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

        public function countEmpruntsEnCours():int
        {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM emprunts WHERE statut = :statut;");
            $stmt->execute([":statut" => "en_cours"]);

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

        public function findEmpruntsEnCoursByMembre(int $membreId):array
        {
            $stmt = $this->pdo->prepare("
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
            WHERE e.statut = :statut AND membre_id = :membre_id
            ORDER BY e.date_emprunt DESC;
            ");
            $stmt->execute([
                ":statut" => "en_cours",
                ":membre_id" => $membreId
            ]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function findEmpruntsRetourneByMembre(int $membreId):array
        {
            $stmt = $this->pdo->prepare("
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
            WHERE e.statut = :statut AND membre_id = :membre_id
            ORDER BY e.date_emprunt DESC;
            ");
            $stmt->execute([
                ":statut" => "retourne",
                ":membre_id" => $membreId
            ]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function countEmpruntsEnRetard():int
        {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM emprunts WHERE statut = :statut;");
            $stmt->execute([":statut" => "en_retard"]);

            return (int) $stmt->fetchColumn();
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