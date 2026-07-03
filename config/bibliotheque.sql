-- ============================
-- Création des tables
-- ============================

CREATE TABLE utilisateurs
(
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL CHECK (role IN ('membre', 'admin')),
    statut VARCHAR(20) NOT NULL DEFAULT 'actif'
        CHECK (statut IN ('actif', 'desactive')),
    date_inscription DATE DEFAULT CURRENT_DATE
);

CREATE TABLE categories
(
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
);

CREATE TABLE livres
(
    id SERIAL PRIMARY KEY,
    titre VARCHAR(150) NOT NULL,
    auteur VARCHAR(150) NOT NULL,
    categorie_id INT NOT NULL,
    resume TEXT,
    couverture VARCHAR(255),
    exemplaires_total INT NOT NULL DEFAULT 1,
    exemplaires_disponibles INT NOT NULL DEFAULT 1,
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_livre_categorie
        FOREIGN KEY (categorie_id)
            REFERENCES categories(id)
);

CREATE TABLE emprunts
(
    id SERIAL PRIMARY KEY,
    livre_id INT NOT NULL,
    membre_id INT NOT NULL,
    date_emprunt DATE DEFAULT CURRENT_DATE,
    date_retour_prevue DATE NOT NULL,
    date_retour_effective DATE,
    statut VARCHAR(20) NOT NULL
        CHECK (statut IN ('en_cours', 'retourne', 'en_retard')),

    CONSTRAINT fk_emprunt_livre
        FOREIGN KEY (livre_id)
            REFERENCES livres(id),

    CONSTRAINT fk_emprunt_membre
        FOREIGN KEY (membre_id)
            REFERENCES utilisateurs(id)
);

-- ============================
-- Données de base
-- ============================

INSERT INTO categories (nom, description)
VALUES
    ('Roman', 'Romans de fiction'),
    ('Science-fiction', 'Livres de science-fiction'),
    ('Informatique', 'Programmation et informatique'),
    ('Histoire', 'Ouvrages historiques'),
    ('Religion', 'Livres religieux');

INSERT INTO utilisateurs (nom, email, mot_de_passe, role)
VALUES
    (
        'Administrateur',
        'admin@bibliotheque.com',
        '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMN',
        'admin'
    ),
    (
        'Jean Dupont',
        'jean@example.com',
        '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMN',
        'membre'
    );

INSERT INTO livres
(
    titre,
    auteur,
    categorie_id,
    resume,
    couverture,
    exemplaires_total,
    exemplaires_disponibles
)
VALUES
    (
        'Le Petit Prince',
        'Antoine de Saint-Exupéry',
        1,
        'Un classique de la littérature française.',
        NULL,
        3,
        3
    ),
    (
        'Dune',
        'Frank Herbert',
        2,
        'Roman de science-fiction.',
        NULL,
        2,
        2
    ),
    (
        'Apprendre PHP',
        'John Doe',
        3,
        'Introduction au langage PHP.',
        NULL,
        5,
        5
    ),
    (
        'Histoire du monde',
        'Pierre Martin',
        4,
        'Panorama de l''histoire mondiale.',
        NULL,
        2,
        2
    ),
    (
        'La Bible',
        'Divers auteurs',
        5,
        'Texte biblique.',
        NULL,
        4,
        4
    );