<?php

    namespace interface;

    interface Empruntable
    {
        public function emprunter():bool;
        public function retourner():bool;
    }