# Bibliothèque

Application de gestion de bibliothèque développée en PHP (POO) avec PostgreSQL, déployée sur Railway.

## Fonctionnalités

**Visiteur**
- Consultation du catalogue, recherche et filtrage par catégorie
- Inscription et connexion (avec option "se souvenir de moi")

**Membre**
- Emprunt de livres disponibles avec gestion de quota (3 emprunts max) et blocage en cas de retard
- Suivi des emprunts en cours (avec indicateur de retard) et de l'historique des retours
- Modification du profil (nom, email)

**Admin**
- Tableau de bord avec statistiques (livres, membres actifs, emprunts en cours/en retard)
- Gestion des livres : ajout, modification, suppression (avec upload de couverture et protection contre la suppression d'un livre encore emprunté)
- Gestion des catégories
- Gestion des membres : activation/désactivation des comptes
- Gestion des emprunts : enregistrement des retours

**Règles de gestion**
- Durée d'emprunt de 14 jours, quota de 3 emprunts simultanés par membre
- Blocage des emprunts en cas de retard non régularisé
- Cohérence stricte entre exemplaires totaux et exemplaires disponibles

## Architecture

Le projet suit une architecture orientée objet avec :

- **Hiérarchie de classes** : `Utilisateur` (abstraite) → `Membre`, `Admin`
- **Interface** : `Empruntable` implémentée par `Livre`
- **Entités** : `Livre`, `Emprunt`
- **Pattern Repository** : un repository dédié par entité pour l'accès aux données

## Stack technique

| Composant | Technologie |
|---|---|
| Backend | PHP 8.4 (POO pure) |
| Base de données | PostgreSQL |
| Accès BDD | PDO (requêtes préparées) |
| Frontend | HTML / CSS personnalisé |
| Déploiement | Railway |

## Installation

```bash
git clone <url-du-repo>
cd bibliotheque
composer install
```

Configurer les variables d'environnement (connexion PostgreSQL) puis lancer le serveur PHP intégré :

```bash
php -S localhost:8000
```

## Auteur

Développé par **Darko** — étudiant en Génie Logiciel (IFRI-UAC).
