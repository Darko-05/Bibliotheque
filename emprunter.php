<?php

    session_start();

    use JetBrains\PhpStorm\NoReturn;

    require_once "config/Database.php";
    require_once "repositories/LivreRepository.php";
    require_once "repositories/EmpruntRepository.php";

    $errorType = "erreur";

    if (isset($_SESSION["utilisateur"]) && $_SESSION["utilisateur"]["role"] === "membre") {

        if (isset($_GET["id"])) {

            $id = $_GET["id"];

            $pdo = Database::getConnection();

            $livreRepository = new \repositories\LivreRepository($pdo);

            $findLivre = $livreRepository->findById($id);

            if ($findLivre && $findLivre["exemplaires_disponibles"] > 0) {

                $idMembre = $_SESSION["utilisateur"]["id"];

                $empruntRepository = new \repositories\EmpruntRepository($pdo);

                $nombreEmpruntsMembre = $empruntRepository->countEnCoursByMembre($idMembre);

                $livreLocation = "livre.php?id={$findLivre['id']}";

                if ($nombreEmpruntsMembre < 3) {

                    $hasEmpruntsEnRetard = $empruntRepository->hasEmpruntEnRetard($idMembre);

                    if (!$hasEmpruntsEnRetard) {

                        $dateEmprunt = date("Y-m-d");
                        $dateRetourPrevue = date("Y-m-d", strtotime("$dateEmprunt + 14 days"));

                        $nouveauEmprunt = [
                            "livre_id" => $findLivre["id"],
                            "membre_id" => $idMembre,
                            "date_emprunt" => $dateEmprunt,
                            "date_retour_prevue" => $dateRetourPrevue,
                            "date_retour_effective" => null,
                            "statut" => "en_cours"
                        ];

                        $newID = $empruntRepository->create($nouveauEmprunt);

                        if ($newID) {

                            $est_decrementer = $livreRepository->decrementerDisponibles($findLivre["id"]);

                            if ($est_decrementer) {

                                $msgType = "succes";
                                $msg = "Emprunt effectuer avec succès !";
                                $location = "mes_emprunts.php";

                                redirectWithMessage($msgType, $msg, $location);

                            }

                        } else {

                            $msg = "Une erreur s'est produite lors de l'opération, veuillez réessayer plus tard !";

                            redirectWithMessage($errorType, $msg, $livreLocation);

                        }

                    } else {

                        $msg = "Vous avez au moins un emprunt en retard, veuillez retourner un livre !";

                        redirectWithMessage($errorType, $msg, $livreLocation);

                    }

                } else {

                    $msg = "Limite atteinte : Vous avez actuellement 3 emprunts en cours !";

                    redirectWithMessage($errorType, $msg, $livreLocation);

                }


            } else {

                $msg = "Le livre est actuellement indisponible.";
                $location = "index.php";

                redirectWithMessage($errorType, $msg, $location);

            }

        } else {

            $msg = "Une erreur s'est produite !";
            $location = "index.php";

            redirectWithMessage($errorType, $msg, $location);

        }


    } else {

        $msg = "Vous devez être un membre pour emprunter un livre !";
        $location = "connexion.php";

        redirectWithMessage($errorType, $msg, $location);

    }

    #[NoReturn]
    function redirectWithMessage(string $messageType, string $message, string $location):void
    {
        $_SESSION[$messageType] = $message;

        header("Location: $location");

        exit();
    }