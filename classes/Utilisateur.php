<?php

    abstract class Utilisateur
    {
        protected int $id;
        protected string $nom;
        protected string $email;

        /**
         * @param int $id
         * @param string $nom
         * @param string $email
         */
        public function __construct(int $id, string $nom, string $email)
        {
            $this->id = $id;
            $this->nom = $nom;
            $this->email = $email;
        }

        public function getId(): int
        {
            return $this->id;
        }

        public function setId(int $id): void
        {
            $this->id = $id;
        }

        public function getNom(): string
        {
            return $this->nom;
        }

        public function setNom(string $nom): void
        {
            $this->nom = $nom;
        }

        public function getEmail(): string
        {
            return $this->email;
        }

        public function setEmail(string $email): void
        {
            $this->email = $email;
        }

        public abstract function role():string;

    }