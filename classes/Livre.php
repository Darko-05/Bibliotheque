<?php

    class Livre implements \interface\Empruntable
    {
        private string $titre;
        private string $auteur;
        private int $categorieId;
        private int $exemplairesTotal;
        private int $exemplairesDisponibles;
        private static int $totalEmprunts = 0;

        /**
         * @param string $titre
         * @param string $auteur
         * @param int $categorieId
         * @param int $exemplairesTotal
         * @param int $exemplairesDisponibles
         */
        public function __construct(string $titre, string $auteur, int $categorieId, int $exemplairesTotal, int $exemplairesDisponibles)
        {
            $this->titre = $titre;
            $this->auteur = $auteur;
            $this->categorieId = $categorieId;
            $this->exemplairesTotal = $exemplairesTotal;
            $this->exemplairesDisponibles = $exemplairesDisponibles;
        }

        public function getTitre(): string
        {
            return $this->titre;
        }

        public function setTitre(string $titre): void
        {
            $this->titre = $titre;
        }

        public function getAuteur(): string
        {
            return $this->auteur;
        }

        public function setAuteur(string $auteur): void
        {
            $this->auteur = $auteur;
        }

        public function getCategorieId(): int
        {
            return $this->categorieId;
        }

        public function setCategorieId(int $categorieId): void
        {
            $this->categorieId = $categorieId;
        }

        public function getExemplairesTotal(): int
        {
            return $this->exemplairesTotal;
        }

        public function setExemplairesTotal(int $exemplairesTotal): void
        {
            $this->exemplairesTotal = $exemplairesTotal;
        }

        public function getExemplairesDisponibles(): int
        {
            return $this->exemplairesDisponibles;
        }

        public function setExemplairesDisponibles(int $exemplairesDisponibles): void
        {
            $this->exemplairesDisponibles = $exemplairesDisponibles;
        }

        public static function getTotalEmprunts(): int
        {
            return self::$totalEmprunts;
        }

        public static function setTotalEmprunts(int $totalEmprunts): void
        {
            self::$totalEmprunts = $totalEmprunts;
        }

        public function emprunter(): bool
        {
            if ($this->exemplairesDisponibles > 0) {

                $this->exemplairesDisponibles--;

                self::$totalEmprunts++;
                return true;
            }

            return false;
        }

        public function retourner(): bool
        {
            $this->exemplairesDisponibles++;

            return true;
        }
    }