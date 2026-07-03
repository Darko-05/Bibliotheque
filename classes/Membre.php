<?php

    class Membre extends Utilisateur
    {
        public function role(): string
        {
            return "membre";
        }
    }