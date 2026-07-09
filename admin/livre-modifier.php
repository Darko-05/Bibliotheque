<?php

    session_start();

    use JetBrains\PhpStorm\NoReturn;

    require_once __DIR__. "/../config/Database.php";
    require_once __DIR__. "/../includes/variables.php";
    require_once __DIR__. "/../repositories/LivreRepository.php";
    require_once __DIR__. "/../repositories/EmpruntRepository.php";
    require_once __DIR__. "/../repositories/CategorieRepository.php";

    if (!isset($_GET["id"])) {
        if (!isset($_SESSION["livre_id"])) {
            $_SESSION["erreur"] = "Impossible de recuperer l'id du livre, veuillez réessayer plus tard !";
            reload();
        }
    }

    if (!isset($_SESSION["utilisateur"]) || $_SESSION["utilisateur"]["role"] !== "admin") {

        $_SESSION["erreur"] = "Veuillez vous connecter en tant qu'administrateur !";

        header("Location: ../connexion.php");
        exit();

    }

    $livre_id = $_GET["id"];

    $pdo = Database::getConnection();
    $livreRepository = new \repositories\LivreRepository($pdo);

    $livre = $livreRepository->findById($livre_id);

    $categorieRepository = new \repositories\CategorieRepository($pdo);
    $categories = $categorieRepository->findAll();

    $empruntRepository = new \repositories\EmpruntRepository($pdo);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        if (!isset($_POST["titre"], $_POST["auteur"], $_POST["categorie"], $_POST["resume"], $_POST["exemplaires_total"])) {
            $_SESSION["erreur"] = "Veuillez remplir correctement tout les champs !";
            reload();
        }

        if (empty($_POST["titre"]) || empty($_POST["auteur"])) {
            $_SESSION["erreur"] = "Veuillez remplir correctement les champs de texte !";
            reload();
        }

        $couverture = null;

        if (isset($_FILES["couverture"])) {

            if (!$_FILES["couverture"]["error"] == 0) {
                $_SESSION["erreur"] = "Erreur de chargement de l'image";
                reload();
            }

            if ($_FILES["couverture"]["size"] > MAX_SIZE) {
                $_SESSION["erreur"] = "Fichier trop volumineux";
                reload();
            }

            if (!in_array(pathinfo($_FILES["couverture"]["name"])["extension"], ["jpg", "png", "gif", "webp", "jpeg", "svg"])) {
                $_SESSION["erreur"] = "Format de fichier invalide !";
                reload();
            }

            if (!is_dir(UPLOAD_DIR)) {
                $_SESSION["erreur"] = "Internal error, dossier d'enregistrement inexistant";
                reload();
            }

            if (!move_uploaded_file($_FILES["couverture"]["tmp_name"], UPLOAD_DIR. basename
                    ($_FILES["couverture"]["name"]))) {
                $_SESSION["erreur"] = "Impossible de sauvegarder le fichier";
                reload();
            }

            $couverture = UPLOAD_DIR
                .basename($_FILES["couverture"]["name"]);

        }

        $titre = htmlspecialchars(trim($_POST["titre"]));
        $auteur = htmlspecialchars(trim($_POST["auteur"]));
        $categorie_id = $_POST["categorie"];
        $resume = htmlspecialchars(trim($_POST["resume"]));
        $exemplairesTotal = $_POST["exemplaires_total"];
        $exemplairesDisponibles = $exemplairesTotal - $empruntRepository->countEnCoursForLivre($livre_id);
        $couverture = $couverture ?? $livreRepository->findById($livre_id)["couverture"] ;

        $dateAjout = date("Y-m-d H:i:s");

        $livreRepository = new \repositories\LivreRepository($pdo);

        $donnees = [
            "titre" => $titre,
            "auteur" => $auteur,
            "categorie_id" => $categorie_id,
            "resume" => $resume,
            "couverture" => $couverture,
            "exemplaires_total" => $exemplairesTotal,
            "exemplaires_disponibles" => $exemplairesDisponibles,
            "date_ajout" => $dateAjout
        ];

        $isUpdated = $livreRepository->update($livre_id, $donnees);

        if ($isUpdated) {
            $_SESSION["succes"] = "Le livre a été modifier avec succès !";
        } else {
            $_SESSION["erreur"] = "Une erreur innatendu s'est produite, veuillez réessayer plus tard !";
        }

        $_SESSION["livre_id"] = $livre_id;

        reload();

    }

    #[NoReturn]
    function reload():void
    {
        header("Location: livres.php");
        exit();
    }

?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="max-w-xl mx-auto my-10 px-4">

    <!-- 1. GESTION DES MESSAGES (Insérés dans le flux normal du container) -->
    <?php if (isset($_SESSION["succes"])) : ?>
        <div class="mb-6 border-4 border-black bg-[#A3E635] p-4 font-black uppercase text-sm tracking-tight shadow-[6px_6px_0_0_rgba(0,0,0,1)]">
            <?= $_SESSION["succes"] ?>
        </div>
        <?php unset($_SESSION["succes"]); ?>

    <?php endif; ?>

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


    <section class="bg-white border-4 border-black p-8 shadow-[8px_8px_0_0_#000]">

        <h2 class="text-2xl font-black uppercase tracking-tighter mb-8 bg-[#A3E635] border-2 border-black inline-block px-4 py-1">
            Ajouter un livre
        </h2>

        <form action="" method="POST" enctype="multipart/form-data" class="flex flex-col gap-6">

            <input type="hidden" name="id" value="<?= $livre_id ?>">

            <div class="flex flex-col gap-2">
                <label for="titre" class="font-black uppercase text-xs tracking-widest">Titre du livre</label>
                <input type="text" id="titre" name="titre" required
                   class="border-2 border-black p-3 font-bold shadow-[4px_4px_0_0_#000] focus:shadow-none
                   focus:translate-x-1 focus:translate-y-1 transition-all outline-none"
                   value="<?= $livre['titre'] ?>
                ">
            </div>

            <div class="flex flex-col gap-2">
                <label for="auteur" class="font-black uppercase text-xs tracking-widest">Auteur</label>
                <input type="text" id="auteur" name="auteur" required
                       class="border-2 border-black p-3 font-bold shadow-[4px_4px_0_0_#000] focus:shadow-none
                       focus:translate-x-1 focus:translate-y-1 transition-all outline-none"
                value="<?= $livre['auteur'] ?>">
            </div>

            <div class="flex flex-col gap-2">
                <label for="categorie" class="font-black uppercase text-xs tracking-widest">Catégorie</label>
                <select id="categorie" name="categorie" required
                        class="border-2 border-black p-3 font-bold shadow-[4px_4px_0_0_#000] appearance-none cursor-pointer outline-none bg-white">
                    <option value="">Sélectionner une catégorie</option>
                    <?php foreach ($categories as $category) : ?>
                    <option value="<?= $category['id'] ?>" <?= $category['id'] == $livre['categorie_id'] ? 'selected' :
                            ''?>><?=
                        $category['nom'] ?></option>


                        <?php echo "<pre>";
                        print_r($category);
                        echo "</pre>"; ?>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex flex-col gap-2">
                <label for="resume" class="font-black uppercase text-xs tracking-widest">Résumé</label>
                <textarea id="resume" name="resume" rows="4"
                          class="border-2 border-black p-3 font-bold shadow-[4px_4px_0_0_#000] focus:shadow-none
                          focus:translate-x-1 focus:translate-y-1 transition-all outline-none
                          resize-none"><?= $livre["resume"] ?></textarea>
            </div>

            <div class="flex flex-col gap-2">
                <label for="exemplaires" class="font-black uppercase text-xs tracking-widest">Nombre d'exemplaires</label>
                <input type="number" id="exemplaires" name="exemplaires_total" min="0" required
                       class="border-2 border-black p-3 font-bold shadow-[4px_4px_0_0_#000] focus:shadow-none
                       focus:translate-x-1 focus:translate-y-1 transition-all outline-none" value="<?= $livre['exemplaires_total'] ?>">
            </div>

            <div class="flex flex-col gap-2">
                <label for="couverture" class="font-black uppercase text-xs tracking-widest">Image de couverture</label>
                <input type="file" id="couverture" name="couverture" accept="image/*"
                       class="border-2 border-black p-2 font-bold shadow-[4px_4px_0_0_#000] file:border-0
                       file:bg-black file:text-white file:font-black file:uppercase file:px-4 file:py-2 file:mr-4
                       file:cursor-pointer hover:file:bg-[#A3E635] hover:file:text-black" value="<?= $livre['couverture'] ?>">
            </div>

            <button type="submit"
                    class="mt-4 border-2 border-black bg-black text-white p-4 font-black uppercase tracking-widest shadow-[4px_4px_0_0_#A3E635] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all active:bg-[#A3E635] active:text-black cursor-pointer">
                Enregistrer le livre
            </button>

        </form>
    </section>
</div>