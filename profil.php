<?php require_once "includes/header.php"; ?>

<?php

    require_once "config/Database.php";
    require_once "repositories/MembreRepository.php";

    if (!isset($_SESSION["utilisateur"]) || $_SESSION["utilisateur"]["role"] !== "membre") {

        $_SESSION["erreur"] = "Veuillez vous connecter !";

        header("Location: connexion.php");

        exit();

    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $pdo = Database::getConnection();

        $membreRepository = new \repositories\MembreRepository($pdo);

        $updated = null;

        if (isset($_POST["email"])) {

            $email = trim($_POST["email"]);
            $oldEmail = $_SESSION["utilisateur"]["email"];

            if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {

                if ($email !== $oldEmail) {

                    $findByEmail = $membreRepository->findByEmail($email);

                    if (!$findByEmail) {

                        $updated = $membreRepository->updateEmail($email, $_SESSION["utilisateur"]["id"]);

                        if ($updated) {

                            $_SESSION["utilisateur"]["email"] = $email;

                        } else {

                            echo "<div style='position: absolute !important; left: 50% !important; transform: translateX(-50%) !important; top: 35px !important; max-width: 550px !important; width: 90% !important; box-sizing: border-box !important;'>
                                <div class='border-2 border-black bg-[#FF6B6B] p-4 font-bold uppercase text-sm tracking-tight shadow-[4px_4px_0_0_rgba(0,0,0,1)]'>
                                    Une erreur s'est produite, veuillez réessayer plus tard !
                                </div>
                            </div>";

                        }

                    } else {

                        echo "<div style='position: absolute !important; left: 50% !important; transform: translateX(-50%) !important; top: 35px !important; max-width: 550px !important; width: 90% !important; box-sizing: border-box !important;'>
                            <div class='border-2 border-black bg-[#FF6B6B] p-4 font-bold uppercase text-sm tracking-tight shadow-[4px_4px_0_0_rgba(0,0,0,1)]'>
                                Cet email est déja utilisé !
                            </div>
                        </div>";

                    }

                }


            } else {

                echo "<div style='position: absolute !important; left: 50% !important; transform: translateX(-50%) !important; top: 35px !important; max-width: 550px !important; width: 90% !important; box-sizing: border-box !important;'>
                    <div class='border-2 border-black bg-[#FF6B6B] p-4 font-bold uppercase text-sm tracking-tight shadow-[4px_4px_0_0_rgba(0,0,0,1)]'>
                        Email non valide !
                    </div>
                </div>";

            }

        }

        if (isset($_POST["nom"])) {

            $nom = trim($_POST["nom"]);

            if (!empty($nom)) {

                $updated = $membreRepository->updateName($nom, $_SESSION["utilisateur"]["id"]);

                if ($updated) {

                    $_SESSION["utilisateur"]["nom"] = $nom;

                } else {

                    echo "<div style='position: absolute !important; left: 50% !important; transform: translateX(-50%) !important; top: 35px !important; max-width: 550px !important; width: 90% !important; box-sizing: border-box !important;'>
                        <div class='border-2 border-black bg-[#FF6B6B] p-4 font-bold uppercase text-sm tracking-tight shadow-[4px_4px_0_0_rgba(0,0,0,1)]'>
                            Une erreur s'est produite, veuillez réessayer plus tard !
                        </div>
                    </div>";

                }

            } else {

                echo "<div style='position: absolute !important; left: 50% !important; transform: translateX(-50%) !important; top: 35px !important; max-width: 550px !important; width: 90% !important; box-sizing: border-box !important;'>
                    <div class='border-2 border-black bg-[#FF6B6B] p-4 font-bold uppercase text-sm tracking-tight shadow-[4px_4px_0_0_rgba(0,0,0,1)]'>
                        Nom invalide !
                    </div>
                </div>";

            }

        } else {

            echo "<div style='position: absolute !important; left: 50% !important; transform: translateX(-50%) !important; top: 35px !important; max-width: 550px !important; width: 90% !important; box-sizing: border-box !important;'>
                    <div class='border-2 border-black bg-[#FF6B6B] p-4 font-bold uppercase text-sm tracking-tight shadow-[4px_4px_0_0_rgba(0,0,0,1)]'>
                        Vous n'avez modifier aucun champ !
                    </div>
                </div>";

        }

        if ($updated) {

            echo "<div style='position: absolute !important; left: 50% !important; transform: translateX(-50%) !important; top: 35px !important; max-width: 550px !important; width: 90% !important; box-sizing: border-box !important;'>
                <div class='border-2 border-black bg-[#A3E635] p-4 font-bold uppercase text-sm tracking-tight shadow-[4px_4px_0_0_rgba(0,0,0,1)]'>
                    Modification réeussi !
                </div>
            </div>";

        }

    }

?>

<div style="position: absolute !important; left: 50% !important; transform: translateX(-50%) !important; top: 120px !important; max-width: 550px !important; width: 90% !important; box-sizing: border-box !important;">
    <section class="bg-white border-2 border-black p-6 md:p-8 rounded-md shadow-[8px_8px_0_0_rgba(0,0,0,1)] select-none">

        <h2 class="text-2xl font-extrabold uppercase tracking-tight mb-6 bg-black text-white inline-block px-3 py-1 transform -rotate-1">
            Mon Profil
        </h2>

        <form action="profil.php" method="POST" class="space-y-5">
            <div>
                <label for="profile_name" class="block text-sm font-bold uppercase mb-2">Nom :</label>
                <input type="text" id="profile_name" name="nom"
                       value="<?= $_SESSION['utilisateur']['nom']; ?>"
                       class="w-full bg-white border-2 border-black p-3 font-bold rounded-sm focus:outline-none focus:bg-neutral-50 shadow-[4px_4px_0_0_rgba(0,0,0,1)] focus:-translate-x-0.5 focus:-translate-y-0.5 focus:shadow-[6px_6px_0_0_rgba(0,0,0,1)] transition-all">
            </div>

            <div>
                <label for="profile_email" class="block text-sm font-bold uppercase mb-2">Email :</label>
                <input type="email" id="profile_email" name="email"
                       value="<?= $_SESSION['utilisateur']['email']; ?>"
                       class="w-full bg-white border-2 border-black p-3 font-bold rounded-sm focus:outline-none focus:bg-neutral-50 shadow-[4px_4px_0_0_rgba(0,0,0,1)] focus:-translate-x-0.5 focus:-translate-y-0.5 focus:shadow-[6px_6px_0_0_rgba(0,0,0,1)] transition-all">
            </div>

            <div>
                <label class="block text-sm font-bold uppercase mb-2">Rôle :</label>
                <div class="w-full bg-neutral-100 border-2 border-black p-3 font-black rounded-sm shadow-[4px_4px_0_0_rgba(0,0,0,1)] cursor-not-allowed select-none text-neutral-700 uppercase tracking-wide">
                    🔑 <?= $_SESSION['utilisateur']['role']; ?>
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-black text-white font-bold uppercase tracking-tight py-4 rounded-sm transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0_0_rgba(0,0,0,0.15)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none cursor-pointer">
                Mettre à jour le profil
            </button>
        </form>
    </section>
</div>
