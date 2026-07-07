<?php

    require_once "config/Database.php";
    require_once "repositories/LivreRepository.php";
    require_once "repositories/CategorieRepository.php";

    if (isset($_GET["id"]) && filter_var($_GET["id"], FILTER_VALIDATE_INT)) {

        $id = $_GET["id"];

        $pdo = Database::getConnection();

        $livreRepository = new \repositories\LivreRepository($pdo);

        $livre = $livreRepository->findById($id);

        $categorieRepository = new \repositories\CategorieRepository($pdo);

    }

?>

<?php if (isset($_GET["id"])) : ?>

    <link rel="stylesheet" href="/public/style.css">

    <?php require_once "includes/header.php"; ?>

    <main class="max-w-5xl mx-auto my-16 px-4 font-mono antialiased text-black">

        <a href="index.php" class="inline-block mb-8 bg-white border-2 border-black px-4 py-2 font-bold uppercase tracking-tight shadow-[4px_4px_0_0_rgba(0,0,0,1)] transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0_0_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none">
            &larr; Retour à la liste
        </a>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-12 border-2 border-black p-6 md:p-10 bg-white shadow-[16px_16px_0_0_rgba(0,0,0,1)] rounded-md">

            <div class="md:col-span-4">
                <div class="relative w-full aspect-2/3 border-2 border-black rounded-sm overflow-hidden bg-neutral-100 shadow-[6px_6px_0_0_rgba(0,0,0,1)]">
                    <img
                        src="<?= $livre['couverture'] ?>"
                        alt="Couverture du livre : <?= htmlspecialchars($livre['titre']) ?>"
                        class="w-full h-full object-cover"
                    >
                </div>
            </div>

            <div class="md:col-span-8 flex flex-col justify-between space-y-6">

                <div class="space-y-4">
                <span class="inline-block bg-black text-white text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-sm">
                    <?= $categorieRepository->determinerCategorie($livre["categorie_id"]) ?>
                </span>

                    <h1 class="text-3xl md:text-5xl font-extrabold tracking-tighter uppercase leading-none">
                        <?= $livre["titre"] ?>
                    </h1>

                    <h2 class="text-lg font-bold text-neutral-600 uppercase tracking-wide">
                        Par : <span class="text-black underline decoration-2"><?= $livre["auteur"] ?></span>
                    </h2>

                    <div class="h-0.5 bg-black/20 w-full my-4"></div>

                    <?php if (isset($livre['resume'])) : ?>
                        <p class="text-neutral-700 leading-relaxed text-sm md:text-base">
                            <?= nl2br(htmlspecialchars($livre['resume'])) ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="pt-4 border-t-2 border-dashed border-black/30 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wide text-neutral-500 block mb-1">Disponibilité :</span>
                        <?php if ($livre['exemplaires_disponibles'] > 0) : ?>
                            <div class="inline-block bg-white border border-black text-sm font-bold uppercase tracking-wider px-4 py-2 shadow-[3px_3px_0_0_rgba(0,0,0,1)]">
                                🟢 <?= $livre["exemplaires_disponibles"] ?> Ex. restants
                            </div>
                        <?php else : ?>
                            <div class="inline-block bg-black text-white text-sm font-bold uppercase tracking-wider px-4 py-2 rounded-sm">
                                🛑 Momentanément indisponible
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($livre['exemplaires_disponibles'] > 0 && isset($_SESSION["utilisateur"])) : ?>
                        <a href="emprunter.php?id=<?= $livre['id'] ?>" class="inline-flex items-center justify-center gap-2 bg-black text-white text-md font-bold uppercase tracking-tight px-6 py-4 rounded-sm transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0_0_rgba(255,255,255,0.2)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none select-none text-center">
                            <span>Emprunter ce livre</span>
                            <span class="text-base">&rarr;</span>
                        </a>
                    <?php else : ?>
                        <a href="connexion.php" class="inline-flex items-center justify-center gap-2 bg-white border-2 border-black px-6 py-4 font-mono text-md font-bold uppercase tracking-tight shadow-[4px_4px_0_0_rgba(0,0,0,1)] transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0_0_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none text-black select-none text-center">
                            <span>Espace Connexion</span>
                            <span class="text-base">&rarr;</span>
                        </a>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </main>

<?php else : ?>

    <div class="max-w-md mx-auto my-4 bg-white border-2 border-black p-4 flex items-center gap-3 shadow-[4px_4px_0_0_rgba(0,0,0,1)] rounded-md font-mono text-black">
        <span class="bg-black text-white px-2 py-0.5 font-bold rounded-sm">X</span>
        <p class="text-sm font-bold uppercase tracking-tight">Une erreur s\'est produite.</p>
    </div>

<?php endif; ?>