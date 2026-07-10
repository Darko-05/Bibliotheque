<?php

    session_start();

    require_once "../config/Database.php";
    require_once "../repositories/MembreRepository.php";
    require_once "../repositories/EmpruntRepository.php";
    require_once "../repositories/LivreRepository.php";

    if (!isset($_SESSION["utilisateur"]) || $_SESSION["utilisateur"]["role"] !== "admin") {
        $_SESSION["erreur"] = "Seul les administrateur sont autorisés a acceder a cette page";
        header("Location: ../connexion.php");
        exit();
    }

    $pdo = Database::getConnection();
    $empruntRepository = new \repositories\EmpruntRepository($pdo);
    $livreRepository = new \repositories\LivreRepository($pdo);
    $membreRepository = new \repositories\MembreRepository($pdo);

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "enregistrer_retour") {
        $empruntId = $_POST["emprunt_id"];

        $emprunt = $empruntRepository->findById($empruntId);

        if ($emprunt) {
            $aujourdhui = date("Y-m-d");
            $isUpdated = $empruntRepository->updateRetour($empruntId, $aujourdhui, 'retourne');

            if ($isUpdated) {
                $livreRepository->incrementerDisponibles($emprunt["livre_id"]);
            } else {
                $_SESSION["erreur"] = "La modification n'a pas pu être effectuer, réessayer plus tard !";
            }
        } else {
            $_SESSION["erreur"] = "Emprunt introuvable.";
        }

        header("Location: emprunts.php");
        exit();
    }

    $empruntsEnCours = $empruntRepository->findEmpruntsEnCours();
    $dateDuJour = date("Y-m-d");

?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="max-w-xl mx-auto my-10 px-4">

    <?php if (isset($_SESSION["erreur"])) : ?>
        <div class="mb-6 border-4 border-black bg-[#FF6B6B] p-4 font-black uppercase text-sm tracking-tight shadow-[6px_6px_0_0_rgba(0,0,0,1)]">
            <?= $_SESSION["erreur"] ?>
        </div>
        <?php unset($_SESSION["erreur"]); ?>
    <?php endif; ?>

    <div class="mb-6 flex justify-start">
        <a href="dashboard.php"
           class="inline-block border-2 border-black bg-white text-xs font-black uppercase tracking-wide px-4 py-2.5 shadow-[4px_4px_0_0_#000] transition-all hover:-translate-x-px hover:-translate-y-px hover:shadow-[5px_5px_0_0_#000]">
            Tableau de bord
        </a>
    </div>

    <section class="bg-white border-4 border-black p-6 shadow-[8px_8px_0_0_#000]">
        <h2 class="text-xl font-black uppercase tracking-tighter mb-6 bg-black text-white inline-block px-3 py-0.5">
            Gestion des Emprunts en cours
        </h2>

        <div class="flex flex-col gap-4">
            <?php if (empty($empruntsEnCours)) : ?>
                <p class="text-sm font-bold text-gray-500 italic">Aucun emprunt en cours actuellement.</p>
            <?php else : ?>
                <?php foreach ($empruntsEnCours as $emprunt) : ?>
                    <?php $estEnRetard = ($dateDuJour > $emprunt["date_retour_prevue"]); ?>

                    <div class="border-2 border-black p-4 shadow-[4px_4px_0_0_#000] flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between <?= $estEnRetard ? 'bg-red-50' : 'bg-white' ?>">

                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="font-black text-base uppercase tracking-tight text-black"><?= $livreRepository->findById($emprunt["livre_id"])["titre"] ?></h3>

                                <?php if ($estEnRetard) : ?>
                                    <span class="border border-black bg-[#FF6B6B] text-[9px] font-black uppercase px-1.5 py-0.5 animate-pulse">
                                        En retard
                                    </span>
                                <?php else : ?>
                                    <span class="border border-black bg-blue-300 text-[9px] font-black uppercase px-1.5 py-0.5">
                                        En cours
                                    </span>
                                <?php endif; ?>
                            </div>

                            <p class="text-xs font-bold text-gray-700 mt-1">Emprunteur : <span class="underline"><?=
                                    $membreRepository->findById($emprunt["membre_id"])["nom"] ?></span></p>
                            <p class="text-xs font-medium text-gray-500 mt-2">
                                À retourner avant le : <span class="font-black <?= $estEnRetard ? 'text-[#FF6B6B]' : 'text-black' ?>"><?= date("d/m/Y", strtotime($emprunt["date_retour_prevue"])) ?></span>
                            </p>
                        </div>

                        <div class="flex gap-2 shrink-0">
                            <form action="" method="POST" class="m-0" onsubmit="return confirm('Confirmer le retour de ce livre ?')">
                                <input type="hidden" name="action" value="enregistrer_retour">
                                <input type="hidden" name="emprunt_id" value="<?= $emprunt['id'] ?>">
                                <button type="submit"
                                        class="border-2 border-black bg-[#A3E635] text-xs font-black uppercase px-3 py-2 shadow-[2px_2px_0_0_#000] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5 transition-all cursor-pointer">
                                    Enregistrer le retour
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

</div>