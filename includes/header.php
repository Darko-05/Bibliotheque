<?php

    session_start();

?>

<link rel="stylesheet" href="/public/style.css">

<header class="w-full bg-white border-b-2 border-black p-4 mb-16 font-mono antialiased text-black select-none">
    <!-- flex et justify-start forcent l'alignement à gauche, gap-4 espace les boutons -->
    <nav class="flex flex-wrap items-center gap-4" style="justify-content: flex-end !important; width: 100% !important;">

        <!-- Bouton commun à tout le monde -->
        <a href="../index.php" class="bg-white border-2 border-black px-4 py-2 text-xs font-bold uppercase
        tracking-tight shadow-[4px_4px_0_0_rgba(0,0,0,1)] transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0_0_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none">
            Accueil
        </a>

        <?php if (isset($_SESSION['utilisateur']) && $_SESSION['utilisateur']['role'] === 'admin') : ?>

            <!-- ÉTAT 1 : ADMIN -->
            <a href="../admin/dashboard.php" class="bg-[#FFDE4D] border-2 border-black px-4 py-2 text-xs font-bold
            uppercase tracking-tight shadow-[4px_4px_0_0_rgba(0,0,0,1)] transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0_0_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none">
                Dashboard admin
            </a>
            <a href="../deconnexion.php" class="bg-black text-white px-4 py-2 border-2 border-black text-xs font-bold uppercase
            tracking-tight transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5">
                Déconnexion
            </a>

        <?php elseif (isset($_SESSION['utilisateur'])) : ?>

            <!-- ÉTAT 2 : UTILISATEUR CONNECTÉ -->
            <a href="../mes_emprunts.php" class="bg-white border-2 border-black px-4 py-2 text-xs font-bold uppercase tracking-tight
            shadow-[4px_4px_0_0_rgba(0,0,0,1)] transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0_0_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none">
                Mes emprunts
            </a>
            <a href="../historique.php" class="bg-white border-2 border-black px-4 py-2 text-xs font-bold uppercase tracking-tight
            shadow-[4px_4px_0_0_rgba(0,0,0,1)] transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0_0_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none">
                Historique
            </a>
            <a href="../profil.php" class="bg-white border-2 border-black px-4 py-2 text-xs font-bold uppercase
            tracking-tight shadow-[4px_4px_0_0_rgba(0,0,0,1)] transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0_0_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none">
                Profil
            </a>
            <a href="../deconnexion.php" class="bg-black text-white px-4 py-2 border-2 border-black text-xs font-bold uppercase
            tracking-tight transition-all hover:-translate-x-0.5 hover:-translate-y-0.5">
                Déconnexion
            </a>

        <?php else : ?>

            <!-- ÉTAT 3 : VISITEUR (Déconnecté) -->
            <a href="../connexion.php" class="bg-white border-2 border-black px-4 py-2 text-xs font-bold uppercase
            tracking-tight shadow-[4px_4px_0_0_rgba(0,0,0,1)] transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0_0_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none">
                Connexion
            </a>
            <a href="../inscription.php" class="bg-black text-white px-4 py-2 border-2 border-black text-xs font-bold uppercase tracking-tight transition-all hover:-translate-x-0.5 hover:-translate-y-0.5">
                Inscription
            </a>

        <?php endif; ?>

    </nav>
</header>