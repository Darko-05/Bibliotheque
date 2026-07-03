<?php

    class Admin extends Utilisateur
    {

        public function role(): string
        {
            return "admin";
        }
    }