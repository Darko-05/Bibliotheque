<?php

    require_once "config/Database.php";
    require_once "includes/variables.php";
    require_once "repositories/LivreRepository.php";
    require_once "repositories/CategorieRepository.php";

    $pdo = Database::getConnection();
    
    $livresRepository = new \repositories\LivreRepository($pdo);
    
    $livres = $livresRepository->findAll();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        if (isset($_POST["motCle"], $_POST["categorie"])) {

            $categorieId = $_POST["categorie"];
            $motCle = trim($_POST["motCle"]);

            $livres = $livresRepository->findByMotCleAndCategorie($categorieId, $motCle);

        } elseif (isset($_POST["categorie"])) {

            $categorieId = $_POST["categorie"];

            $livres = $livresRepository->findByCategorie($categorieId);

        } elseif (isset($_POST["motCle"])) {

            $motCle = trim($_POST["motCle"]);

            $livres = $livresRepository->findByMotCle($motCle);

        }

    }

    $categorieRepository = new \repositories\CategorieRepository($pdo);

    $categories = $categorieRepository->findAll();

?>

<?php require_once "includes/header.php"; ?>

<main class="max-w-5xl mx-auto px-4">
    
    <div class="max-w-2xl mx-auto my-12 p-1 font-mono antialiased text-black">
    
        <!-- Header graphique (SVG ou texte brut) -->
        <div class="mb-10 text-center">
            <h2 class="text-4xl font-extrabold tracking-tighter uppercase leading-none transform -rotate-1">
                Rechercher / <span class="bg-black text-white px-2 py-1">Catégoriser</span>
            </h2>
            <div class="h-1 bg-black mt-2 w-3/4 mx-auto"></div>
            <p class="text-sm mt-3 uppercase tracking-wider text-neutral-600">Base de données interne</p>
        </div>
    
        <!-- Le Formulaire -->
        <form action="index.php" method="post" class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t-2 border-black pt-10">
    
            <!-- Groupe 1: Mot Clé -->
            <div class="relative group">
                <label for="motCle" class="absolute -top-3 left-6 px-3 bg-white text-sm font-bold uppercase tracking-tight text-black border border-black z-10 transition-transform group-focus-within:-translate-y-1">
                    Entrez un mot clé :
                </label>
                <div class="relative overflow-hidden rounded-md border-2 border-black bg-white group-focus-within:shadow-[8px_8px_0_0_rgba(0,0,0,1)] transition-shadow">
                    <input
                        type="text"
                        name="motCle"
                        id="motCle"
                        placeholder="Tapez ici..."
                        class="w-full px-6 py-6 font-mono text-lg bg-transparent text-black placeholder-neutral-400 focus:outline-none transition-all"
                    >
                </div>
            </div>
    
            <!-- Groupe 2: Catégorie -->
            <div class="relative group">
                <label for="categorie" class="absolute -top-3 left-6 px-3 bg-white text-sm font-bold uppercase tracking-tight text-black border border-black z-10 transition-transform group-focus-within:-translate-y-1">
                    Choisissez une catégorie :
                </label>
                <div class="relative overflow-hidden rounded-md border-2 border-black bg-white group-focus-within:shadow-[8px_8px_0_0_rgba(0,0,0,1)] transition-shadow">
                    <select
                        name="categorie"
                        id="categorie"
                        class="w-full px-6 py-6 font-mono text-lg bg-transparent text-black appearance-none cursor-pointer focus:outline-none"
                    >
                        <option value="" selected disabled></option>
                        <?php foreach ($categories as $category) : ?>
                            <option value="<?= $category['id'] ?>" class="font-mono text-black"><?= $category["nom"] ?></option>
                        <?php endforeach; ?>
                    </select>
    
                    <!-- Flèche personnalisée 'Ligne' -->
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-6 text-black">
                        <svg width="18" height="18" viewBox="0 0 100 100" class="stroke-current stroke-8">
                            <line x1="20" y1="35" x2="50" y2="65" />
                            <line x1="50" y1="65" x2="80" y2="35" />
                        </svg>
                    </div>
                </div>
            </div>
    
            <!-- Bouton / Pas de couleur, juste du contraste -->
            <div class="md:col-span-2 text-center mt-6">
                <button type="submit" class="inline-flex items-center gap-3 px-10 py-5 bg-black text-white text-xl font-bold uppercase tracking-tighter rounded-md transform transition-all active:scale-95 active:shadow-inner hover:-translate-y-1">
                    <span>Exécuter la requête</span>
                    <span class="text-3xl font-light">&rarr;</span>
                </button>
            </div>
    
        </form>
    </div>
    
    <section class="max-w-7xl mx-auto my-16 px-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 font-mono antialiased text-black">
    
        <?php if (count($livres) > 0) : ?>
    
            <?php foreach ($livres as $livre) : ?>
    
                <a href="livre.php?id=<?= $livre['id'] ?>" class="group relative block select-none">
    
                    <div class="group relative bg-white border-2 border-black rounded-md p-5 shadow-[6px_6px_0_0_rgba(0,0,0,
                    1)] transition-all hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[10px_10px_0_0_rgba(0,0,0,1)]
                    flex flex-col justify-between">
    
                        <div>
                            <div class="relative w-full aspect-2/3 border-2 border-black rounded-sm overflow-hidden bg-neutral-100 mb-4">
                                <img
                                        src="<?= $livre['couverture'] ?>"
                                        alt="Couverture du livre : <?= htmlspecialchars($livre['titre']) ?>"
                                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                >
    
                                <span class="absolute top-3 left-3 bg-white border border-black text-xs font-bold uppercase tracking-tight px-2 py-1 shadow-[2px_2px_0_0_rgba(0,0,0,1)]">
                                <?= $categorieRepository->determinerCategorie($livre["categorie_id"]) ?>
                            </span>
                            </div>
    
                            <div class="space-y-1.5">
                                <h3 class="text-xl font-extrabold tracking-tight uppercase line-clamp-2 min-h-14 leading-tight">
                                    <?= $livre["titre"] ?>
                                </h3>
    
                                <h4 class="text-sm font-bold text-neutral-600 uppercase tracking-wide">
                                    Auteur : <span class="text-black underline decoration-1"><?= $livre["auteur"] ?></span>
                                </h4>
                            </div>
                        </div>
    
                        <div class="mt-6 pt-4 border-t border-black/20">
                            <?php if ($livre['exemplaires_disponibles'] > 0) : ?>
                                <div class="inline-block w-full text-center bg-white border border-black text-xs font-bold uppercase tracking-wider py-2.5 shadow-[3px_3px_0_0_rgba(0,0,0,1)]">
                                    🟢 <?= $livre["exemplaires_disponibles"] ?> Ex. disponibles
                                </div>
                            <?php else : ?>
                                <div class="inline-block w-full text-center bg-black text-white text-xs font-bold uppercase tracking-wider py-2.5 rounded-sm">
                                    🛑 Indisponible
                                </div>
                            <?php endif; ?>
                        </div>
    
                    </div>
    
                </a>
    
            <?php endforeach; ?>
    
        <?php else : ?>
    
            <div class="col-span-full w-full bg-white border-2 border-black p-8 md:p-12 text-center shadow-[8px_8px_0_0_rgba(0,0,0,1)] rounded-md my-6">
    
                <div class="inline-flex items-center justify-center w-16 h-16 bg-black text-white text-3xl font-bold rounded-sm mb-4 transform -rotate-3">
                    ?
                </div>
    
                <h3 class="text-2xl font-extrabold uppercase tracking-tight mb-2">
                    Aucun livre trouvé
                </h3>
    
                <p class="text-neutral-600 max-w-md mx-auto text-sm uppercase tracking-wide leading-relaxed">
                    La base de données n'a renvoyé aucun résultat pour ce mot-clé ou cette catégorie.
                    <br>
                    <span class="text-black font-bold underline decoration-1">Vérifie l'orthographe</span> ou tente une autre recherche.
                </p>
    
            </div>
    
        <?php endif; ?>
    
    </section>

</main>