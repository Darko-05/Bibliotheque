<?php

    session_start();

    require_once __DIR__. "/../config/Database.php";
    require_once __DIR__. "/../repositories/LivreRepository.php";
    require_once __DIR__. "/../repositories/CategorieRepository.php";
    require_once __DIR__. "/../repositories/MembreRepository.php";
    require_once __DIR__. "/../repositories/EmpruntRepository.php";

    if (!isset($_SESSION["utilisateur"]) || $_SESSION["utilisateur"]["role"] !== "admin") {

        $_SESSION["erreur"] = "Vous devez être un administrateur pour acceder a cette page !";

        header("Location: connexion.php");

        exit();

    }

    $pdo = Database::getConnection();

    $livreRepository = new \repositories\LivreRepository($pdo);
    $membreRepository = new \repositories\MembreRepository($pdo);
    $empruntRepository = new \repositories\EmpruntRepository($pdo);
    $categorieRepository = new \repositories\CategorieRepository($pdo);

    $nombreTotalLivres = count($livreRepository->findAll());
    $nombreMembresActifs = $membreRepository->countMembresActif();
    $nombreEmpruntsEnCours = $empruntRepository->countEmpruntsEnCours();
    $nombreEmpruntsEnRetards = $empruntRepository->countEmpruntsEnRetard();

    $categories = $categorieRepository->findAll();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord — Bibliothèque</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700;800;900&display=swap">
    <script src="https://cdn.tailwindcss.com"></script>

</head>
<body class="bg-[#F4F2EC] font-sans text-black min-h-screen flex antialiased selection:bg-[#A3E635]">

<!-- ================= 1. SIDEBAR NEO-BRUTALISTE (Fixée à gauche) ================= -->
<aside class="hidden md:flex flex-col w-72 bg-white border-r-4 border-black p-6 justify-between shrink-0 h-screen sticky top-0">
    <div class="space-y-10">
        <!-- Brand / Logo -->
        <div class="border-4 border-black bg-[#A3E635] p-4 shadow-[4px_4px_0_0_#000] transform -rotate-1">
            <span class="block text-xl font-black uppercase tracking-tight">📚 BIBLIO.ZIP</span>
            <span class="block text-[10px] font-bold tracking-widest text-black/60 uppercase mt-0.5">Espace Administrateur</span>
        </div>

        <!-- Liens de Navigation Style Onglets Découpés -->
        <nav class="space-y-2">
            <a href="livres.php" class="flex items-center gap-3 px-4 py-3 border-2 border-black bg-white font-extrabold text-sm uppercase tracking-tight shadow-[2px_2px_0_0_#000] transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[4px_4px_0_0_#000]">
                 Gérer les livres
            </a>
            <a href="categories.php" class="flex items-center gap-3 px-4 py-3 border-2 border-black bg-[#A3E635] font-extrabold text-sm uppercase tracking-tight shadow-[4px_4px_0_0_#000] -translate-x-0.5 -translate-y-0.5">
                 Gérer les catégories
            </a>
            <a href="membres.php?" class="flex items-center gap-3 px-4 py-3 border-2 border-black bg-white
            font-extrabold text-sm uppercase tracking-tight shadow-[2px_2px_0_0_#000] transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[4px_4px_0_0_#000]">
                 Gérer les membres
            </a>
            <a href="emprunts.php" class="flex items-center gap-3 px-4 py-3 border-2 border-black bg-white font-extrabold text-sm uppercase tracking-tight shadow-[2px_2px_0_0_#000] transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[4px_4px_0_0_#000]">
                 Gérer les emprunts
            </a>
        </nav>
    </div>

    <!-- Profil Connecté -->
    <div class="border-t-4 border-black pt-4 space-y-3">
        <!-- Infos Profil -->
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 border-2 border-black bg-[#FF6B6B] flex items-center justify-center font-black text-sm shadow-[2px_2px_0_0_#000]">
                A
            </div>
            <div class="leading-tight">
                <p class="text-sm font-black uppercase">Administrateur</p>
                <p class="text-[11px] font-bold text-neutral-500">Session active</p>
            </div>
        </div>

        <!-- Bouton Déconnexion Neo-Brutalist -->
        <a href="../deconnexion.php" class="flex items-center justify-center gap-2 w-full bg-[#FF6B6B] text-white font-black
        text-xs uppercase tracking-wider py-2.5 border-2 border-black shadow-[2px_2px_0_0_#000] transition-all hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none cursor-pointer">
            Déconnexion
        </a>
    </div>
</aside>

<!-- ================= 2. SECTION PRINCIPALE RESPONSIVE ================= -->
<main class="flex-1 p-6 md:p-12 lg:p-16 space-y-12 max-w-7xl mx-auto w-full">

    <!-- EN-TÊTE PRINCIPAL DE LA PAGE -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 border-b-4 border-black pb-8">
        <div>
            <p class="text-xs font-black uppercase tracking-widest text-neutral-500 mb-2">// INDEX DE CONTRÔLE</p>
            <h1 class="text-4xl md:text-5xl font-black uppercase tracking-tighter text-black">
                Tableau de bord
            </h1>
        </div>

        <!-- Bouton d'action "Figma component style" Neo-Brutalist -->
        <a href="livre-ajouter.php" class="inline-flex items-center justify-center bg-black text-white font-black
         text-xs uppercase tracking-wider px-6 py-4 border-2 border-black shadow-[4px_4px_0_0_#A3E635] transition-all hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none cursor-pointer">
            + Ajouter une référence
        </a>
    </div>

    <!-- GRILLE DE COMPTEURS IMPOSANTS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Carte 1 -->
        <div class="bg-white border-4 border-black p-6 shadow-[6px_6px_0_0_#000] transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[8px_8px_0_0_#000]">
            <p class="text-xs font-black uppercase tracking-wider text-neutral-500">Livres enregistrés</p>
            <p class="text-4xl font-black text-black mt-3 tracking-tighter"><?= $nombreTotalLivres; ?></p>
        </div>

        <!-- Carte 2 -->
        <div class="bg-white border-4 border-black p-6 shadow-[6px_6px_0_0_#000] transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[8px_8px_0_0_#000]">
            <p class="text-xs font-black uppercase tracking-wider text-neutral-500">Membres actifs</p>
            <p class="text-4xl font-black text-black mt-3 tracking-tighter"><?= $nombreMembresActifs; ?></p>
        </div>

        <!-- Carte 3 -->
        <div class="bg-[#A3E635] border-4 border-black p-6 shadow-[6px_6px_0_0_#000] transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[8px_8px_0_0_#000]">
            <p class="text-xs font-black uppercase tracking-wider text-black/70">Emprunts en cours</p>
            <p class="text-4xl font-black text-black mt-3 tracking-tighter"><?= $nombreEmpruntsEnCours; ?></p>
        </div>

        <!-- Carte 4 (Alerte Retard Stampée) -->
        <div class="bg-[#FF6B6B] border-4 border-black p-6 shadow-[6px_6px_0_0_#000] transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[8px_8px_0_0_#000] flex flex-col justify-between gap-4">
            <div>
                <p class="text-xs font-black uppercase tracking-wider text-white/80">Retards signalés</p>
                <p class="text-4xl font-black text-white mt-3 tracking-tighter"><?= $nombreEmpruntsEnRetards; ?></p>
            </div>
            <?php if ($nombreEmpruntsEnRetards > 0): ?>
                <div class="self-start bg-black text-white text-[9px] font-black uppercase tracking-widest px-2 py-1 -rotate-2">
                    ⚠️ LITIGE ACTIF
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ZONE INFÉRIEURE : INDEX DES CATÉGORIES STYLISÉE -->
    <div class="bg-white border-4 border-black p-6 md:p-8 shadow-[8px_8px_0_0_#000]">
        <div class="flex items-center justify-between border-b-2 border-black pb-4 mb-6">
            <h3 class="text-sm font-black uppercase tracking-wider text-black">Index thématique des catégories</h3>
            <span class="text-xs font-bold bg-black text-white px-2 py-0.5"><?= isset($categories) ? count($categories) : 0; ?> REF</span>
        </div>

        <div class="flex flex-wrap gap-3">
            <?php if (!empty($categories)) : ?>
                <?php foreach ($categories as $categorie) : ?>
                    <span class="border-2 border-black bg-[#F4F2EC] px-4 py-2 font-black uppercase text-xs tracking-tight shadow-[3px_3px_0_0_#000] transition-all hover:bg-white hover:-translate-x-px hover:-translate-y-px hover:shadow-[4px_4px_0_0_#000] cursor-pointer">
                            # <?= $categorie['nom']; ?>
                        </span>
                <?php endforeach; ?>
            <?php else : ?>
                <p class="text-xs font-bold uppercase text-neutral-400 italic">Aucune catégorie répertoriée dans la base.</p>
            <?php endif; ?>
        </div>
    </div>

</main>

</body>
</html>