<?php require_once "includes/header.php" ?>

<?php

    require_once "config/Database.php";
    require_once "repositories/LivreRepository.php";
    require_once "repositories/EmpruntRepository.php";

    if (isset($_SESSION["utilisateur"]) && $_SESSION["utilisateur"]["role"] === "membre") {

        $membreId = $_SESSION["utilisateur"]["id"];

        $pdo = Database::getConnection();

        $empruntRepository = new \repositories\EmpruntRepository($pdo);

        $emprunts = $empruntRepository->findEmpruntsEnCoursByMembre($membreId);

        $livreRepository = new \repositories\LivreRepository($pdo);

    } else {

        $_SESSION["erreur"] = "Veuillez vous connecter pour voir vos emprunts !";

        header("Location: connexion.php");

        exit();

    }

?>

<div style="position: absolute !important; left: 50% !important; transform: translateX(-50%) !important; top: 120px !important; max-width: 550px !important; width: 90% !important; box-sizing: border-box !important;">

    <h2 class="text-2xl font-extrabold uppercase tracking-tight mb-6 bg-black text-white inline-block px-3 py-1 transform -rotate-1">
        Mes Emprunts
    </h2>

    <div class="space-y-6">
        <?php if ($emprunts) : ?>

            <?php foreach ($emprunts as $emprunt) : ?>

                <div class="bg-white border-2 border-black p-5 rounded-md shadow-[6px_6px_0_0_rgba(0,0,0,1)] select-none">

                    <h3 class="text-xl font-extrabold uppercase tracking-tight mb-3 text-black">
                        <?php echo $emprunt["livre_titre"] ?>
                    </h3>

                    <p class="text-sm font-bold text-neutral-700 uppercase tracking-wide mb-1">
                        Date d'emprunt : <span class="font-black text-black"><?php echo $emprunt["date_emprunt"] ?></span>
                    </p>

                    <p class="text-sm font-bold text-neutral-700 uppercase tracking-wide mb-4">
                        Date de retour prévue : <span class="font-black text-black"><?php echo $emprunt["date_retour_prevue"] ?></span>
                    </p>

                    <?php
                    $aujourdhui = date("Y-m-d");
                    $date_retour = date("Y-m-d", strtotime($aujourdhui));

                    if ($aujourdhui > $date_retour) : ?>
                        <div class="inline-block border-2 border-black bg-[#FF6B6B] px-3 py-1.5 font-bold uppercase text-xs tracking-tight shadow-[2px_2px_0_0_rgba(0,0,0,1)]">
                            ⚠️ Retard : Date dépassée !
                        </div>
                    <?php else : ?>
                        <div class="inline-block border-2 border-black bg-[#A3E635] px-3 py-1.5 font-bold uppercase text-xs tracking-tight shadow-[2px_2px_0_0_rgba(0,0,0,1)]">
                            ✅ En cours
                        </div>
                    <?php endif; ?>

                </div>

            <?php endforeach; ?>

        <?php else : ?>

            <div class="bg-white border-2 border-black p-6 rounded-md shadow-[6px_6px_0_0_rgba(0,0,0,1)] text-center font-bold uppercase text-sm tracking-tight">
                📭 Vous n'avez aucun emprunt en cours.
            </div>

        <?php endif; ?>
    </div>
</div>