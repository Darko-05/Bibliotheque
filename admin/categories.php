<?php

    use JetBrains\PhpStorm\NoReturn;

    session_start();

    require_once "../config/Database.php";
    require_once "../repositories/LivreRepository.php";
    require_once "../repositories/CategorieRepository.php";

    if (!isset($_SESSION["utilisateur"]) || $_SESSION["utilisateur"]["role"] !== "admin") {
        $_SESSION["erreur"] = "Seul les administrateur sont autorisés a acceder a cette page";
        header("Location: ../connexion.php");
        exit();
    }

    $standardLocation = "categories.php";

    $pdo = Database::getConnection();
    $categoriesRepository = new \repositories\CategorieRepository($pdo);
    $categories = $categoriesRepository->findAll();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (!isset($_POST["nom"], $_POST["description"])) {
            $msg = "Vous devez remplir tout les champs.";
            redirectToPage("erreur", $msg, $standardLocation);
        }

        if (empty($_POST["nom"]) || empty($_POST["description"])) {
            $msg = "Veuillez entrer des valeurs valides";
            redirectToPage("erreur", $msg, $standardLocation);
        }

        $nom = trim($_POST["nom"]);
        $description = trim($_POST["description"]);

        if (isset($_SESSION["editer"])) {
            $categoryId = $_SESSION["editer"]["id"];
            $isUpdated = $categoriesRepository->update($categoryId, [
                "nom" => $nom,
                "description" => $description
            ]);

            unset($_SESSION["editer"]);

            if ($isUpdated) {
                $msg = "Categorie modifier avec succès !";
                redirectToPage("succes", $msg, $standardLocation);
            } else {
                $msg = "Une erreur s'est produite lors de la modification.";
                redirectToPage("erreur", $msg, $standardLocation);
            }

        } else {
            $isAdded = $categoriesRepository->create([
                "nom" => $nom,
                "description" => $description
            ]);

            if ($isAdded) {
                $msg = "Categorie ajouter avec succès !";
                redirectToPage("succes", $msg, $standardLocation);
            } else {
                $msg = "Une erreur s'est produite lors de l'ajout de la nouvelle catégorie.";
                redirectToPage("erreur", $msg, $standardLocation);
            }
        }

    }

    if (isset($_GET["action"]) && $_GET["action"] === "supprimer") {
        $categoryId = $_GET["id"];

        $livreRepository = new \repositories\LivreRepository($pdo);

        $existsByCategorie = count($livreRepository->findByCategorie($categoryId)) > 0;

        if ($existsByCategorie) {
            $msg = "Impossible de supprimer la categorie car elle est associée a un ou des livres.";
            redirectToPage("erreur", $msg, $standardLocation);
        } else {

            $isDeleted = $categoriesRepository->delete($categoryId);

            if ($isDeleted) {
                $msg = "Categorie supprimer avec succès";
                redirectToPage("succes", $msg, $standardLocation);
            } else {
                $msg = "Erreur lors de la suppression, veuillez réessayer plus tard !";
                redirectToPage("erreur", $msg, $standardLocation);
            }

        }
    }

    if (isset($_GET["action"]) && $_GET["action"] === "editer") {
        $_SESSION["editer"]["id"] = $_GET["id"];
        $_SESSION["editer"]["action"] = true;
        header("Location: categories.php");
        exit();
    }

    $category = [];

    if (isset($_SESSION["editer"])) {
        $category = $categoriesRepository->findById($_SESSION["editer"]["id"]);
    }

    #[NoReturn]
    function redirectToPage(string $msgType, string $msg, string $location):void {
        $_SESSION[$msgType] = $msg;
        header("Location: $location");
        exit();
    }

?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="max-w-xl mx-auto my-10 px-4">

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

    <section class="bg-white border-4 border-black p-6 shadow-[8px_8px_0_0_#000] mb-10">
        <h2 class="text-xl font-black uppercase tracking-tighter mb-6 bg-[#A3E635] border-2 border-black inline-block px-3 py-0.5">
            Gestion de la catégorie
        </h2>

        <form action="" method="POST" class="flex flex-col gap-4">
            <div class="flex flex-col gap-1">
                <label for="nom" class="font-black uppercase text-xs tracking-widest">Nom de la catégorie</label>
                <input type="text" id="nom" name="nom" required
                   class="border-2 border-black p-2.5 font-bold shadow-[4px_4px_0_0_#000] focus:shadow-none focus:translate-x-1 focus:translate-y-1 transition-all outline-none"
                    value="<?= $category !== [] ? $category['nom'] : '' ?>">
            </div>

            <div class="flex flex-col gap-1">
                <label for="description" class="font-black uppercase text-xs tracking-widest">Description</label>
                <textarea id="description" name="description" rows="3"
                  class="border-2 border-black p-2.5 font-bold shadow-[4px_4px_0_0_#000] focus:shadow-none
                  focus:translate-x-1 focus:translate-y-1 transition-all outline-none resize-none">
                    <?= $category !== [] ? $category['description'] : '' ?>
                </textarea>
            </div>

            <button type="submit"
                    class="mt-2 border-2 border-black bg-black text-white p-3 font-black uppercase tracking-widest text-sm shadow-[4px_4px_0_0_#A3E635] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all cursor-pointer">
                Enregistrer la catégorie
            </button>
        </form>
    </section>

    <section class="bg-white border-4 border-black p-6 shadow-[8px_8px_0_0_#000]">
        <h2 class="text-xl font-black uppercase tracking-tighter mb-6 bg-black text-white inline-block px-3 py-0.5">
            Liste des catégories
        </h2>

        <?php foreach ($categories as $category) : ?>
            <div class="flex flex-col gap-4">
                <div class="border-2 border-black p-4 shadow-[4px_4px_0_0_#000] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="font-black text-lg uppercase tracking-tight"><?= $category["nom"] ?></h3>
                        <p class="text-sm font-medium text-gray-700 mt-1"><?= $category["description"] ?></p>
                    </div>

                    <div class="flex gap-2 shrink-0">
                        <a href="?action=editer&id=<?= $category['id'] ?>"
                           class="border-2 border-black bg-[#A3E635] text-xs font-black uppercase px-3 py-2 shadow-[2px_2px_0_0_#000] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5 transition-all">
                            Modifier
                        </a>
                        <a href="?action=supprimer&id=<?= $category['id'] ?>"
                           class="border-2 border-black bg-[#FF6B6B] text-xs font-black uppercase px-3 py-2 shadow-[2px_2px_0_0_#000] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5 transition-all"
                            onclick="return confirm('Supprimer cette catégorie ?')">
                            Supprimer
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </section>

</div>