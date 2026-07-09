<?php

    session_start();

    require_once __DIR__. "/../config/Database.php";
    require_once __DIR__. "/../repositories/LivreRepository.php";
    require_once __DIR__. "/../repositories/EmpruntRepository.php";
    require_once __DIR__. "/../repositories/CategorieRepository.php";

    if (!isset($_SESSION["utilisateur"]) || $_SESSION["utilisateur"]["role"] !== "admin") {

        $_SESSION["erreur"] = "Veuillez devez être un administrateur pour acceder a cette page !";

        header("Location: ../connexion.php");

        exit();

    }

    $pdo = Database::getConnection();

    $livreRepository = new \repositories\LivreRepository($pdo);

    $livres = $livreRepository->findAll();

    $categoriesRepository = new \repositories\CategorieRepository($pdo);

    if (isset($_GET["action"], $_GET["id"]) && $_GET["action"] === "delete") {

        $livreId = $_GET["id"];

        $empruntRepository = new \repositories\EmpruntRepository($pdo);

        $estEmprunte = $empruntRepository->estEmprunte($livreId);

        if (!$estEmprunte) {

            $estSupprimer = $livreRepository->delete($livreId);

            if ($estSupprimer) {
                $_SESSION["succes"] = "Suppression réussie !";
            } else {
                $_SESSION["erreur"] = "Impossible de supprimer le livre !";
            }

        } else {
            $_SESSION["erreur"] = "Le livre est actuellement en cours d'emprunts, impossible de le supprimer !";
        }

        header("Location: livres.php");
        exit();

    }

?>

<link rel="stylesheet" href="/public/style.css">

<?php

    if (isset($_SESSION["erreur"])) {

        echo "<div style='position: absolute !important; left: 50% !important; transform: translateX(-50%) !important; top: 10px !important; max-width: 550px !important; width: 90% !important; box-sizing: border-box !important;'>
            <div class='border-2 border-black bg-[#FF6B6B] p-4 font-bold uppercase text-sm tracking-tight shadow-[4px_4px_0_0_rgba(0,0,0,1)]'>
                " . $_SESSION["erreur"] . "
            </div>
        </div>";

        unset($_SESSION["erreur"]);

    }

    if (isset($_SESSION["succes"])) {

        echo "<div style='position: absolute !important; left: 50% !important; transform: translateX(-50%) !important; top: 10px !important; max-width: 550px !important; width: 90% !important; box-sizing: border-box !important;'>
            <div class='border-2 border-black bg-[#A3E635] p-4 font-bold uppercase text-sm tracking-tight shadow-[4px_4px_0_0_rgba(0,0,0,1)]'>
                " . $_SESSION["succes"] . "
            </div>
        </div>";

        unset($_SESSION["succes"]);

    }

?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="bg-white border-4 border-black p-6 md:p-8 shadow-[8px_8px_0_0_#000]">

    <a href="dashboard.php"
       class="inline-block border-2 border-black bg-[#A3E635] text-sm font-black uppercase tracking-wider px-5 py-3
       shadow-[4px_4px_0_0_#000] transition-all hover:bg-white hover:-translate-x-0.5 hover:-translate-y-0.5
       hover:shadow-[6px_6px_0_0_#000] mb-6">
        Retour au Tableau de bord
    </a>

    <div class="flex items-center justify-between border-b-4 border-black pb-4 mb-6">
        <h3 class="text-lg font-black uppercase tracking-wider text-black">📚 Inventaire du catalogue</h3>
        <span class="text-xs font-bold bg-black text-white px-3 py-1">
            <?= $livres ? count($livres) : 0; ?> RÉFÉRENCES
        </span>
    </div>

    <?php if ($livres) : ?>
        <div class="space-y-4">
            <?php foreach ($livres as $livre) : ?>

                <div class="border-2 border-black bg-[#F4F2EC] p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-[4px_4px_0_0_#000] transition-all hover:bg-white hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0_0_#000]">

                    <div class="space-y-1 min-w-0 flex-1">
                        <span class="inline-block border border-black bg-[#A3E635] px-2 py-0.5 text-[10px] font-black uppercase tracking-tight">
                            # <?= $categoriesRepository->determinerCategorie($livre["categorie_id"]) ?>
                        </span>
                        <h4 class="text-xl font-black uppercase tracking-tight truncate text-black">
                            <?= $livre["titre"] ?>
                        </h4>
                        <p class="text-sm font-bold text-neutral-600">
                            Auteur : <span class="text-black uppercase"><?= $livre["auteur"] ?></span>
                        </p>
                    </div>

                    <div class="flex gap-3 text-center shrink-0">
                        <div class="border border-black bg-white px-3 py-1.5 shadow-[2px_2px_0_0_#000]">
                            <p class="text-[9px] font-black uppercase text-neutral-500">Disponibles</p>
                            <p class="text-base font-black"><?= $livre["exemplaires_disponibles"] ?></p>
                        </div>
                        <div class="border border-black bg-white px-3 py-1.5 shadow-[2px_2px_0_0_#000] opacity-70">
                            <p class="text-[9px] font-black uppercase text-neutral-500">Total Stock</p>
                            <p class="text-base font-black"><?= $livre["exemplaires_total"] ?></p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0 pt-2 md:pt-0 border-t-2 md:border-t-0 border-black/10">
                        <a href="livre-modifier.php?id=<?= $livre['id'] ?>"
                           class="border-2 border-black bg-white text-xs font-black uppercase tracking-wider px-3 py-2 shadow-[2px_2px_0_0_#000] transition-all hover:bg-[#A3E635] hover:-translate-x-px hover:-translate-y-px hover:shadow-[3px_3px_0_0_#000]">
                                Modifier
                        </a>

                        <a href="livres.php?action=delete&id=<?= $livre['id'] ?>"
                           onclick="return confirm('Supprimer définitivement ce livre ?');"
                           class="border-2 border-black bg-[#FF6B6B] text-white text-xs font-black uppercase tracking-wider px-3 py-2 shadow-[2px_2px_0_0_#000] transition-all hover:-translate-x-px hover:-translate-y-px hover:shadow-[3px_3px_0_0_#000]">
                                Supprimer
                        </a>
                    </div>

                </div>

            <?php endforeach; ?>
        </div>

    <?php else : ?>
        <div class="border-2 border-dashed border-black/30 p-12 text-center bg-[#F4F2EC]/50">
            <p class="text-sm font-bold uppercase text-neutral-400 italic">Aucun livre n'est répertorié pour le moment.</p>
        </div>
    <?php endif; ?>

</div>