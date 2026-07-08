<?php
        
    session_start();

    require_once "config/Database.php";
    require_once "repositories/MembreRepository.php";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        if (isset($_POST["email"], $_POST["mot_de_passe"]) && filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {

            $email = $_POST["email"];
            $mot_de_passe = $_POST["mot_de_passe"];

            $pdo = Database::getConnection();

            $membreRepository = new \repositories\MembreRepository($pdo);

            $findEmail = $membreRepository->findByEmail($email);

            if ($findEmail && $findEmail["statut"] === "actif") {

                if (password_verify($mot_de_passe, $findEmail["mot_de_passe"])) {

                    if (!isset($_SESSION["utilisateur"])) {

                        $_SESSION["utilisateur"] = [
                            "id" => $findEmail["id"],
                            "nom" => $findEmail["nom"],
                            "email" => $email,
                            "mot_de_passe" => $mot_de_passe,
                            "role" => "membre",
                            "statut" => "actif",
                            "date_inscription" => $findEmail["date_inscription"]
                        ];

                    }

                    if (isset($_POST["remember_me"]) && $_POST["remember_me"] === "on") {

                        setcookie(
                            "email",
                            $email,
                            [
                                "expires" => time() + 30 * 24 * 3600,
                                "secure" => true,
                                "httponly" => true
                            ]
                        );

                    }

                    header("Location: index.php");

                    exit();

                } else {

                    echo "<div style='position: absolute !important; left: 50% !important; transform: translateX(-50%) !important; top: 35px !important; max-width: 550px !important; width: 90% !important; box-sizing: border-box !important;'>
                        <div class='border-2 border-black bg-[#FF6B6B] p-4 font-bold uppercase text-sm tracking-tight shadow-[4px_4px_0_0_rgba(0,0,0,1)]'>
                            ⚠️ Mot de passe incorrect.
                        </div>
                    </div>";

                }

            } else {

                echo "<div style='position: absolute !important; left: 50% !important; transform: translateX(-50%) !important; top: 35px !important; max-width: 550px !important; width: 90% !important; box-sizing: border-box !important;'>
                    <div class='border-2 border-black bg-[#FF6B6B] p-4 font-bold uppercase text-sm tracking-tight shadow-[4px_4px_0_0_rgba(0,0,0,1)]'>
                        ⚠️ Aucun compte associée a cet email ou compte inactif.
                    </div>
                </div>";

            }

        }

    }

    if (isset($_SESSION["erreur"])) {

        echo "<div style='position: absolute !important; left: 50% !important; transform: translateX(-50%) !important; top: 35px !important; max-width: 550px !important; width: 90% !important; box-sizing: border-box !important;'>
            <div class='border-2 border-black bg-[#FF6B6B] p-4 font-bold uppercase text-sm tracking-tight shadow-[4px_4px_0_0_rgba(0,0,0,1)]'>
                {$_SESSION['erreur']}
            </div>
        </div>";

        unset($_SESSION["erreur"]);

    }

?>

<link rel="stylesheet" href="/public/style.css">

<div style="position: absolute !important; left: 50% !important; transform: translateX(-50%) !important; top: 120px !important; max-width: 550px !important; width: 90% !important; box-sizing: border-box !important;">
    <section class="bg-white border-2 border-black p-6 md:p-8 rounded-md shadow-[8px_8px_0_0_rgba(0,0,0,1)] select-none">

        <h2 class="text-2xl font-extrabold uppercase tracking-tight mb-6 bg-black text-white inline-block px-3 py-1 transform -rotate-1">
            Connexion
        </h2>

        <form action="connexion.php" method="POST" class="space-y-5">
            <div>
                <label for="login_email" class="block text-sm font-bold uppercase mb-2">Email :</label>
                <input type="email" id="login_email" name="email" required placeholder="exemple@mail.com"
                       class="w-full bg-white border-2 border-black p-3 font-bold rounded-sm focus:outline-none 
                       focus:bg-neutral-50 shadow-[4px_4px_0_0_rgba(0,0,0,1)] focus:-translate-x-0.5 
                       focus:-translate-y-0.5 focus:shadow-[6px_6px_0_0_rgba(0,0,0,1)] transition-all" value="<?= $_SESSION['utilisateur']['email'] ?? $_COOKIE['email'] ?? ''; ?>">
            </div>

            <div>
                <label for="login_password" class="block text-sm font-bold uppercase mb-2">Mot de passe :</label>
                <input type="password" id="login_password" name="mot_de_passe" required placeholder="••••••••"
                       class="w-full bg-white border-2 border-black p-3 font-bold rounded-sm focus:outline-none 
                       focus:bg-neutral-50 shadow-[4px_4px_0_0_rgba(0,0,0,1)] focus:-translate-x-0.5 
                       focus:-translate-y-0.5 focus:shadow-[6px_6px_0_0_rgba(0,0,0,1)] transition-all">
            </div>

            <div class="flex items-center pt-1">
                <label class="flex items-center cursor-pointer relative select-none">
                    <input type="checkbox" name="remember_me" id="remember_me" class="peer sr-only">
                    <div class="w-6 height-6 h-6 bg-white border-2 border-black rounded-sm shadow-[2px_2px_0_0_rgba(0,0,0,1)] transition-all peer-checked:bg-[#A3E635] peer-checked:-translate-x-px peer-checked:-translate-y-px peer-checked:shadow-[3px_3px_0_0_rgba(0,0,0,1)] flex items-center justify-center font-black text-black">
                        <span class="hidden peer-checked:block text-xs">[ ✓ ]</span>
                    </div>
                    <span class="ml-3 text-sm font-bold uppercase tracking-tight">Se souvenir de moi</span>
                </label>
            </div>

            <button type="submit"
                    class="w-full bg-black text-white font-bold uppercase tracking-tight py-4 rounded-sm transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0_0_rgba(0,0,0,0.15)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none cursor-pointer">
                Se connecter
            </button>
        </form>
    </section>
</div>