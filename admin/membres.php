<?php

    session_start();

    require_once "../config/Database.php";
    require_once "../repositories/MembreRepository.php";
    require_once "../repositories/EmpruntRepository.php";
    require_once "../repositories/CategorieRepository.php";

    if (!isset($_SESSION["utilisateur"]) || $_SESSION["utilisateur"]["role"] !== "admin") {
        $_SESSION["erreur"] = "Seul les administrateur sont autorisés a acceder a cette page";
        header("Location: ../connexion.php");
        exit();
    }

    $pdo = Database::getConnection();
    $membreRepository = new \repositories\MembreRepository($pdo);
    $empruntRepository = new \repositories\EmpruntRepository($pdo);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $membreId = $_POST["id"];

        if (isset($_POST["statut"])) {
            $nouveauStatut = $_POST["statut"];
            $isUpdated = $membreRepository->updateStatut($membreId, $nouveauStatut);

            if (!$isUpdated) {
                http_response_code(500);
                echo "Erreur BDD";
                exit();
            }
        }

        exit();
    }

    $membres = $membreRepository->findAll();

?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="max-w-xl mx-auto my-10 px-4">

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

    <section class="bg-white border-4 border-black p-6 shadow-[8px_8px_0_0_#000]">
        <h2 class="text-xl font-black uppercase tracking-tighter mb-6 bg-black text-white inline-block px-3 py-0.5">
            Liste des membres
        </h2>

        <div class="flex flex-col gap-4">
            <?php foreach ($membres as $membre) : ?>
                <?php $isActif = ($membre["statut"] === "actif"); ?>
                <div class="border-2 border-black p-4 shadow-[4px_4px_0_0_#000] flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-black text-base uppercase tracking-tight"><?= $membre["nom"] ?></h3>

                            <span id="badge-statut-<?= $membre['id'] ?>"
                                  class="border border-black text-[9px] font-black uppercase px-1.5 py-0.5 <?= $isActif ? 'bg-[#A3E635]' : 'bg-[#FF6B6B]' ?>">
                                <?= $isActif ? 'Actif' : 'Désactivé' ?>
                            </span>
                        </div>
                        <p class="text-xs font-bold text-gray-600 mt-0.5"><?= $membre["email"] ?></p>
                        <p class="text-xs font-black uppercase tracking-wider text-gray-500 mt-2">
                            Emprunts en cours : <span class="text-black text-sm"><?= $empruntRepository->countEmpruntsEnEncoursByMembre($membre['id']) ?></span>
                        </p>
                    </div>

                    <div class="flex gap-2 shrink-0">
                        <?php if ($membre["role"] === "admin") : ?>
                            <div class="w-12 h-7 bg-[#A3E635] border-2 border-black p-0.5 shadow-[2px_2px_0_0_#000] opacity-60 cursor-not-allowed">
                                <div class="w-5 h-5 bg-black border-2 border-black translate-x-5"></div>
                            </div>
                        <?php else : ?>
                            <button type="button"
                                    onclick="toggleMembreStatut(this, <?= $membre['id'] ?>)"
                                    data-statut="<?= $membre['statut'] ?>"
                                    class="w-12 h-7 border-2 border-black p-0.5 transition-all cursor-pointer shadow-[2px_2px_0_0_#000] <?= $isActif ? 'bg-[#A3E635]' : 'bg-gray-200' ?>">
                                <div class="toggle-dot w-5 h-5 border-2 border-black transition-all <?= $isActif ? 'bg-black translate-x-5' : 'bg-white' ?>"></div>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

</div>

<script>
    function toggleMembreStatut(btn, membreId) {
        const actuelActif = btn.getAttribute('data-statut') === 'actif';
        const nouveauStatutStr = actuelActif ? 'desactive' : 'actif';

        const formData = new FormData();
        formData.append('id', membreId);
        formData.append('statut', nouveauStatutStr); // Envoie directement 'actif' ou 'desactive'

        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (response.ok) {
                    btn.setAttribute('data-statut', nouveauStatutStr);
                    const dot = btn.querySelector('.toggle-dot');
                    const badge = document.getElementById(`badge-statut-${membreId}`);

                    if (nouveauStatutStr === 'actif') {
                        btn.className = "w-12 h-7 border-2 border-black p-0.5 transition-all cursor-pointer shadow-[2px_2px_0_0_#000] bg-[#A3E635]";
                        if (dot) dot.className = "toggle-dot w-5 h-5 border-2 border-black transition-all bg-black translate-x-5";
                        if (badge) {
                            badge.className = "border border-black text-[9px] font-black uppercase px-1.5 py-0.5 bg-[#A3E635]";
                            badge.textContent = "Actif";
                        }
                    } else {
                        btn.className = "w-12 h-7 border-2 border-black p-0.5 transition-all cursor-pointer shadow-[2px_2px_0_0_#000] bg-gray-200";
                        if (dot) dot.className = "toggle-dot w-5 h-5 border-2 border-black transition-all bg-white";
                        if (badge) {
                            badge.className = "border border-black text-[9px] font-black uppercase px-1.5 py-0.5 bg-[#FF6B6B]";
                            badge.textContent = "Désactivé";
                        }
                    }
                } else {
                    alert("Erreur lors de la mise à jour du statut.");
                }
            })
            .catch(() => alert("Erreur réseau."));
    }
</script>