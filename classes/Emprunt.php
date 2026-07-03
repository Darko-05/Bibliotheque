<?php

    class Emprunt
    {
        private int $livreId;
        private int $membreId;
        private DateTime $dateEmprunt;
        private DateTime $dateRetourPrevue;
        private DateTime $dateRetourEffective;
        private string $statut;

        /**
         * @param int $livreId
         * @param int $membreId
         * @param DateTime $dateEmprunt
         * @param DateTime $dateRetourPrevue
         * @param DateTime $dateRetourEffective
         * @param string $statut
         */
        public function __construct(int $livreId, int $membreId, DateTime $dateEmprunt, DateTime $dateRetourPrevue, DateTime $dateRetourEffective, string $statut)
        {
            $this->livreId = $livreId;
            $this->membreId = $membreId;
            $this->dateEmprunt = $dateEmprunt;
            $this->dateRetourPrevue = $dateRetourPrevue;
            $this->dateRetourEffective = $dateRetourEffective;
            $this->statut = $statut;
        }

        public function getLivreId(): int
        {
            return $this->livreId;
        }

        public function getMembreId(): int
        {
            return $this->membreId;
        }

        public function getDateEmprunt(): DateTime
        {
            return $this->dateEmprunt;
        }

        public function getDateRetourPrevue(): DateTime
        {
            return $this->dateRetourPrevue;
        }

        public function getDateRetourEffective(): DateTime
        {
            return $this->dateRetourEffective;
        }

        public function getStatut(): string
        {
            return $this->statut;
        }

        public function setDateRetourEffective(DateTime $dateRetourEffective): void
        {
            $this->dateRetourEffective = $dateRetourEffective;
        }

        public function setStatut(string $statut): void
        {
            $this->statut = $statut;
        }

    }