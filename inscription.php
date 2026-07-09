<?php

    session_start();

    require_once "config/Database.php";
    require_once "repositories/MembreRepository.php";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        if (isset($_POST["nom"], $_POST["email"], $_POST["mot_de_passe"]) && filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {

            $nom = trim($_POST["nom"]);
            $email = trim($_POST["email"]);
            $mot_de_passe = $_POST["mot_de_passe"];

            $pdo = Database::getConnection();

            $membreRepository = new \repositories\MembreRepository($pdo);

            $findEmail = $membreRepository->findByEmail($email);

            if ($findEmail === null) {

                $dateInscription = date("Y-m-d");

                $passwordHash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

                $donnees = [
                    "nom" => $nom,
                    "email" => $email,
                    "mot_de_passe" => $passwordHash,
                    "role" => "membre",
                    "statut" => "actif",
                    "date_inscription" => $dateInscription
                ];

                $newID = $membreRepository->create($donnees);

                if ($newID > 0) {
                    $_SESSION["utilisateur"] = [
                        "id" => $newID,
                        "nom" => $nom,
                        "email" => $email,
                        "role" => "membre",
                        "statut" => "actif",
                        "date_inscription" => $dateInscription
                    ];

                    $_SESSION['succes'] = "Inscription réussie !";
                    header("Location: connexion.php");
                    exit();
                }

            } else {
                $_SESSION["erreur"] = "⚠️ Un utilisateur existe déjà avec cet email";
            }

        } else {
           $_SESSION["erreur"] = "⚠️ Tous les champs sont obligatoires.";
        }

    }

?>

<link rel="stylesheet" href="/public/style.css">

<?php

    if (!empty($message_erreur)) {
        echo "<div style='position: absolute !important; left: 50% !important; transform: translateX(-50%) !important; top: 35px !important; max-width: 550px !important; width: 90% !important; box-sizing: border-box !important;'>
            <div class='border-2 border-black bg-[#FF6B6B] p-4 font-bold uppercase text-sm tracking-tight shadow-[4px_4px_0_0_rgba(0,0,0,1)]'>
                " . $message_erreur . "
            </div>
        </div>";
    }

?>

<section class="bg-white border-2 border-black p-6 md:p-8 rounded-md shadow-[8px_8px_0_0_rgba(0,0,0,1)] select-none"
         style="position: absolute !important; left: 50% !important; transform: translateX(-50%) !important; top: 120px !important; max-width: 550px !important; width: 90% !important; box-sizing: border-box !important;">
    <h2 class="text-2xl font-extrabold uppercase tracking-tight mb-6 bg-black text-white inline-block px-3 py-1 transform -rotate-1">
        Inscription
    </h2>

    <form action="inscription.php" method="POST" class="space-y-5">
        <div>
            <label for="register_name" class="block text-sm font-bold uppercase mb-2">Nom :</label>
            <input type="text" id="register_name" name="nom" required placeholder="Ex: Darko"
                   class="w-full bg-white border-2 border-black p-3 font-bold rounded-sm focus:outline-none focus:bg-neutral-50 shadow-[4px_4px_0_0_rgba(0,0,0,1)] focus:-translate-x-0.5 focus:-translate-y-0.5 focus:shadow-[6px_6px_0_0_rgba(0,0,0,1)] transition-all">
        </div>

        <div>
            <label for="register_email" class="block text-sm font-bold uppercase mb-2">Email :</label>
            <input type="email" id="register_email" name="email" required placeholder="exemple@mail.com"
                   class="w-full bg-white border-2 border-black p-3 font-bold rounded-sm focus:outline-none focus:bg-neutral-50 shadow-[4px_4px_0_0_rgba(0,0,0,1)] focus:-translate-x-0.5 focus:-translate-y-0.5 focus:shadow-[6px_6px_0_0_rgba(0,0,0,1)] transition-all">
        </div>

        <div>
            <label for="register_password" class="block text-sm font-bold uppercase mb-2">Mot de passe :</label>
            <input type="password" id="register_password" name="mot_de_passe" required placeholder="••••••••"
                   class="w-full bg-white border-2 border-black p-3 font-bold rounded-sm focus:outline-none focus:bg-neutral-50 shadow-[4px_4px_0_0_rgba(0,0,0,1)] focus:-translate-x-0.5 focus:-translate-y-0.5 focus:shadow-[6px_6px_0_0_rgba(0,0,0,1)] transition-all">
        </div>

        <button type="submit"
                class="w-full bg-black text-white font-bold uppercase tracking-tight py-4 rounded-sm transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0_0_rgba(0,0,0,0.15)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none cursor-pointer">
            Créer mon compte
        </button>
    </form>
</section>