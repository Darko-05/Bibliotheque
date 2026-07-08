<?php

    session_start();

    require_once __DIR__. "/../config/Database.php";
    require_once __DIR__. "/../repositories/LivreRepository.php";
    require_once __DIR__. "/../repositories/CategorieRepository.php";
    require_once __DIR__. "/../repositories/MembreRepository.php";
    require_once __DIR__. "/../repositories/EmpruntRepository.php";

    if (!isset($_SESSION["utilisateur"]) && $_SESSION["utilisateur"]["role"] === "admin") {

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
    <link rel="stylesheet" href="/public/style.css">
    <style>

        @import url('https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500;0,6..72,600;1,6..72,500&family=JetBrains+Mono:wght@400;500;600&family=Inter:wght@400;500;600&display=swap');

        .biblio {
            --paper: #E7E2D1;
            --card: #FBF9F2;
            --ink: #23201A;
            --ink-soft: #6B6455;
            --forest: #2E4A3C;
            --forest-dark: #223829;
            --brass: #A6813C;
            --brass-soft: #D8C695;
            --rust: #9B3B32;

            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
            color: var(--ink);
            background: var(--paper);
            background-image: radial-gradient(rgba(35,32,26,0.04) 1px, transparent 1px);
            background-size: 3px 3px;
            min-height: 100vh;
            width: 100%;
            display: flex;
        }
        .biblio *, .biblio *::before, .biblio *::after { box-sizing: border-box; }

        .biblio-font-display { font-family: 'Newsreader', serif; }
        .biblio-font-mono { font-family: 'JetBrains Mono', monospace; }

        /* ---------- Sidebar ---------- */
        .biblio-sidebar {
            width: 260px;
            flex-shrink: 0;
            background: var(--forest-dark);
            padding: 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        @media (max-width: 767px) {
            .biblio-sidebar { display: none; }
        }
        .biblio-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(216,198,149,0.2);
        }
        .biblio-brand-mark {
            width: 32px; height: 32px;
            border-radius: 2px;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 600;
            background: var(--brass); color: var(--forest-dark);
        }
        .biblio-brand-name { font-size: 13px; font-weight: 500; color: rgba(255,255,255,0.9); line-height: 1.3; }
        .biblio-brand-sub { font-size: 10px; letter-spacing: 0.1em; margin-top: 4px; color: var(--brass-soft); }

        .biblio-nav { margin-top: 32px; display: flex; flex-direction: column; gap: 6px; }
        .biblio-nav a {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 12px; border-radius: 3px;
            font-size: 12.5px; font-weight: 500;
            color: rgba(255,255,255,0.55);
            text-decoration: none;
            transition: background-color 0.15s, color 0.15s;
        }
        .biblio-nav a:hover { color: #fff; background: rgba(255,255,255,0.06); }
        .biblio-nav a.active {
            color: #fff; font-weight: 600;
            background: rgba(216,198,149,0.12);
            box-shadow: inset 2px 0 0 var(--brass);
        }
        .biblio-nav a .tag { font-size: 11px; color: var(--brass-soft); }
        .biblio-nav a.active .tag { color: var(--brass); }

        .biblio-profile {
            display: flex; align-items: center; gap: 12px;
            padding-top: 16px; margin-top: 16px;
            border-top: 1px solid rgba(216,198,149,0.15);
        }
        .biblio-profile-mark {
            width: 28px; height: 28px; border-radius: 2px;
            display: flex; align-items: center; justify-content: center;
            font-size: 10px; font-weight: 600;
            background: rgba(255,255,255,0.08); color: var(--brass-soft);
        }
        .biblio-profile-name { font-size: 12px; font-weight: 500; color: rgba(255,255,255,0.85); }
        .biblio-profile-mail { font-size: 10px; margin-top: 2px; color: rgba(255,255,255,0.35); }

        /* ---------- Contenu ---------- */
        .biblio-main {
            flex: 1;
            min-width: 0;
            padding: 32px;
            display: flex;
            flex-direction: column;
            gap: 44px;
        }
        @media (min-width: 1024px) {
            .biblio-main { padding: 56px; }
        }

        .biblio-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            padding-bottom: 24px;
            border-bottom: 1px solid rgba(35,32,26,0.12);
        }
        .biblio-eyebrow {
            font-size: 10.5px; letter-spacing: 0.15em; text-transform: uppercase;
            color: var(--ink-soft); margin-bottom: 8px;
        }
        .biblio-title { font-size: 32px; line-height: 1; color: var(--ink); margin: 0; }

        .biblio-btn {
            display: inline-flex; align-items: center; gap: 8px;
            font-size: 12.5px; font-weight: 500;
            padding: 10px 16px; border-radius: 3px;
            background: var(--forest); color: var(--brass-soft);
            text-decoration: none;
            box-shadow: 0 1px 0 rgba(0,0,0,0.15);
            transition: transform 0.1s;
        }
        .biblio-btn:active { transform: scale(0.97); }
        .biblio-btn .plus { color: var(--brass); }

        /* ---------- Fiches statistiques ---------- */
        .biblio-stats {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 20px;
        }
        @media (min-width: 640px) {
            .biblio-stats { grid-template-columns: repeat(2, 1fr); }
        }
        @media (min-width: 1024px) {
            .biblio-stats { grid-template-columns: repeat(4, 1fr); }
        }

        .biblio-card {
            position: relative;
            background: var(--card);
            border-radius: 4px;
            padding: 24px 24px 24px 32px;
            box-shadow: 0 1px 3px rgba(35,32,26,0.08), 0 1px 0 rgba(35,32,26,0.04);
        }
        .biblio-card::before {
            content: "";
            position: absolute;
            top: 14px; left: 14px;
            width: 9px; height: 9px;
            border-radius: 999px;
            background: var(--paper);
            border: 1px solid rgba(35,32,26,0.18);
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.15);
        }
        .biblio-card.accent { border-left: 3px solid var(--forest); }

        .biblio-card-label {
            font-size: 10px; letter-spacing: 0.05em; text-transform: uppercase;
            color: var(--ink-soft);
        }
        .biblio-card-value { font-size: 30px; margin: 8px 0 0; color: var(--ink); }

        .biblio-card-row { display: flex; align-items: center; justify-content: space-between; margin-top: 8px; }
        .biblio-card-row .biblio-card-value { margin: 0; }

        .biblio-stamp {
            border: 2px solid var(--rust);
            color: var(--rust);
            transform: rotate(-3deg);
            box-shadow: 0 0 0 1px rgba(155,59,50,0.15);
            font-size: 9px; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase;
            padding: 4px 8px; border-radius: 2px;
            white-space: nowrap;
        }

        /* ---------- Index des catégories ---------- */
        .biblio-panel {
            background: var(--card);
            border-radius: 4px;
            padding: 28px;
            box-shadow: 0 1px 3px rgba(35,32,26,0.08);
        }
        .biblio-panel-head {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 20px;
        }
        .biblio-panel-title {
            font-size: 10.5px; letter-spacing: 0.15em; text-transform: uppercase;
            color: var(--ink-soft); margin: 0;
        }
        .biblio-panel-count { font-size: 10px; color: var(--ink-soft); }

        .biblio-tabs { display: flex; flex-wrap: wrap; gap: 10px 8px; }
        .biblio-tab {
            display: inline-flex; align-items: center;
            padding: 8px 20px 8px 12px;
            font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.03em;
            background: var(--paper); color: var(--forest-dark);
            border: 1px solid rgba(35,32,26,0.1);
            cursor: pointer;
            clip-path: polygon(0 0, 78% 0, 100% 100%, 0% 100%);
        }
        .biblio-empty { font-size: 13px; font-style: italic; color: var(--ink-soft); }
    </style>
</head>
<body>

<div class="biblio">

    <!-- ================= SIDEBAR — façade de tiroir ================= -->
    <aside class="biblio-sidebar">
        <div>
            <div class="biblio-brand">
                <div class="biblio-brand-mark">B</div>
                <div>
                    <div class="biblio-brand-name">Bibliothèque</div>
                    <div class="biblio-brand-sub">CATALOGUE&nbsp;ADMIN</div>
                </div>
            </div>

            <nav class="biblio-nav">
                <a href="admin/livres.php"><span class="tag">Liv</span> Gérer les livres</a>
                <a href="admin/categories.php" class="active"><span class="tag">Cat</span> Gérer les catégories</a>
                <a href="admin/membres.php"><span class="tag">Mbr</span> Gérer les membres</a>
                <a href="admin/emprunts.php"><span class="tag">Emp</span> Gérer les emprunts</a>
            </nav>
        </div>

        <div class="biblio-profile">
            <div class="biblio-profile-mark">A</div>
            <div>
                <div class="biblio-profile-name">Admin</div>
                <div class="biblio-profile-mail">admin@biblio.com</div>
            </div>
        </div>
    </aside>

    <!-- ================= CONTENU ================= -->
    <main class="biblio-main">

        <div class="biblio-header">
            <div>
                <p class="biblio-eyebrow biblio-font-mono">Consultation du <?= date('d.m.Y'); ?></p>
                <h1 class="biblio-title biblio-font-display">Tableau de bord</h1>
            </div>
            <a href="admin/livres.php?action=add" class="biblio-btn biblio-font-mono">
                <span class="plus">+</span> AJOUTER&nbsp;UN&nbsp;LIVRE
            </a>
        </div>

        <div class="biblio-stats">
            <div class="biblio-card">
                <p class="biblio-card-label biblio-font-mono">Livres enregistrés</p>
                <p class="biblio-card-value biblio-font-display"><?= $nombreTotalLivres; ?></p>
            </div>

            <div class="biblio-card">
                <p class="biblio-card-label biblio-font-mono">Membres actifs</p>
                <p class="biblio-card-value biblio-font-display"><?= $nombreMembresActifs; ?></p>
            </div>

            <div class="biblio-card accent">
                <p class="biblio-card-label biblio-font-mono">Emprunts en cours</p>
                <p class="biblio-card-value biblio-font-display"><?= $nombreEmpruntsEnCours; ?></p>
            </div>

            <div class="biblio-card">
                <p class="biblio-card-label biblio-font-mono">Retards signalés</p>
                <div class="biblio-card-row">
                    <p class="biblio-card-value biblio-font-display"><?= $nombreEmpruntsEnRetards; ?></p>
                    <?php if ($nombreEmpruntsEnRetards > 0): ?>
                        <span class="biblio-stamp biblio-font-mono">En&nbsp;retard</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="biblio-panel">
            <div class="biblio-panel-head">
                <h3 class="biblio-panel-title biblio-font-mono">Index des catégories</h3>
                <span class="biblio-panel-count biblio-font-mono"><?= isset($categories) ? count($categories) : 0; ?> réf.</span>
            </div>

            <div class="biblio-tabs">
                <?php if (!empty($categories)) : ?>
                    <?php foreach ($categories as $categorie) : ?>
                        <span class="biblio-tab biblio-font-mono"><?= $categorie['nom']; ?></span>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p class="biblio-empty">Aucune catégorie répertoriée.</p>
                <?php endif; ?>
            </div>
        </div>

    </main>
</div>

</body>
</html>