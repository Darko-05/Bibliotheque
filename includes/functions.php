<?php

    function formaterExtrait(string $texte, int $longueur):string
    {
        return strlen($texte) > $longueur ? $texte. "..." : $texte;
    }

    function estAdmin(array|null $utilisateur):bool
    {
        return $utilisateur && $utilisateur["role"] === "admin";
    }

    function estConnecte():bool
    {
        return isset($_SESSION["utilisateur"]);
    }

    /**
     * @throws DateMalformedStringException
     */
    function calculerStatutEmprunt(string $dateRetourPrevue, string|null $dateRetourEffective):string
    {
        if ($dateRetourEffective) {
            return "retourne";
        }

        $dateRetourPrevue = new DateTime($dateRetourPrevue);
        $dateAudjourdhui = new DateTime();

        if ($dateAudjourdhui < $dateRetourPrevue) {
            return "en_cours";
        }

        return "en_retard";
    }

    /**
     * @throws DateMalformedStringException
     */
    function formaterDate(string $date):string
    {
        return new DateTime($date)->format("d/m/Y");
    }