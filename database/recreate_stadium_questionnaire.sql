-- Recreate script for Stadium_Questionnaire
-- Compatible with MySQL 8+
CREATE DATABASE Stadium_Questionnaire CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Users
CREATE TABLE Utilisateur (
    id INT NOT NULL AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    pseudo VARCHAR(100) NOT NULL,
    mdp VARCHAR(255) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_utilisateur_email (email),
    UNIQUE KEY uq_utilisateur_pseudo (pseudo)
) ENGINE=InnoDB;

-- Quiz containers
CREATE TABLE Questionnaire (
    id INT NOT NULL AUTO_INCREMENT,
    nom VARCHAR(255) NOT NULL,
    theme VARCHAR(100) NOT NULL,
    utilisateur_id INT NOT NULL,
    est_publie TINYINT(1) NOT NULL DEFAULT 0,
    date_publication DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (id),
    INDEX idx_questionnaire_user (utilisateur_id),
    CONSTRAINT fk_questionnaire_utilisateur
        FOREIGN KEY (utilisateur_id)
        REFERENCES Utilisateur(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Questions
CREATE TABLE Question (
    id INT NOT NULL AUTO_INCREMENT,
    questionnaire_id INT NOT NULL,
    numero INT NOT NULL,
    libelle TEXT NOT NULL,
    type_reponse VARCHAR(50) NOT NULL,
    reponse_vrai_faux TINYINT(1) NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_question_questionnaire_numero (questionnaire_id, numero),
    INDEX idx_question_questionnaire (questionnaire_id),
    CONSTRAINT fk_question_questionnaire
        FOREIGN KEY (questionnaire_id)
        REFERENCES Questionnaire(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT chk_question_type
        CHECK (type_reponse IN ('VraiFaux', 'ListeValeurs'))
) ENGINE=InnoDB;

-- Multiple-choice values
CREATE TABLE Reponse (
    id INT NOT NULL AUTO_INCREMENT,
    question_id INT NOT NULL,
    valeur VARCHAR(255) NOT NULL,
    est_correcte TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    INDEX idx_reponse_question (question_id),
    CONSTRAINT fk_reponse_question
        FOREIGN KEY (question_id)
        REFERENCES Question(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- User submitted answers
CREATE TABLE ReponseUtilisateur (
    id INT NOT NULL AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    question_id INT NOT NULL,
    questionnaire_id INT NOT NULL,
    reponse_texte TEXT NOT NULL,
    reponse_bool TINYINT(1) NULL,
    est_correcte TINYINT(1) NOT NULL,
    date_reponse DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_rep_utilisateur (utilisateur_id),
    INDEX idx_rep_questionnaire (questionnaire_id),
    INDEX idx_rep_question (question_id),
    INDEX idx_rep_score (utilisateur_id, questionnaire_id, est_correcte),
    CONSTRAINT fk_rep_user
        FOREIGN KEY (utilisateur_id)
        REFERENCES Utilisateur(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_rep_question
        FOREIGN KEY (question_id)
        REFERENCES Question(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_rep_questionnaire
        FOREIGN KEY (questionnaire_id)
        REFERENCES Questionnaire(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Questionnaire access tracking for weekly connection stats
CREATE TABLE QuestionnaireConnexion (
    id INT NOT NULL AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    questionnaire_id INT NOT NULL,
    date_connexion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_qconn_user (utilisateur_id),
    INDEX idx_qconn_questionnaire (questionnaire_id),
    INDEX idx_qconn_date (date_connexion),
    CONSTRAINT fk_qconn_user
        FOREIGN KEY (utilisateur_id)
        REFERENCES Utilisateur(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_qconn_questionnaire
        FOREIGN KEY (questionnaire_id)
        REFERENCES Questionnaire(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Admin users (one-to-one with Utilisateur)
CREATE TABLE Admin (
    utilisateur_id INT NOT NULL,
    date_promotion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (utilisateur_id),
    CONSTRAINT fk_admin_utilisateur
        FOREIGN KEY (utilisateur_id)
        REFERENCES Utilisateur(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Admin logs
CREATE TABLE LogAdmin (
    id INT NOT NULL AUTO_INCREMENT,
    utilisateur_id INT NULL,
    action VARCHAR(100) NOT NULL,
    details TEXT NOT NULL,
    date_log DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_log_admin_date (date_log),
    INDEX idx_log_admin_user (utilisateur_id),
    CONSTRAINT fk_log_admin_utilisateur
        FOREIGN KEY (utilisateur_id)
        REFERENCES Utilisateur(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Reported questions from web/api/report_question.php
CREATE TABLE Signalement (
    id INT NOT NULL AUTO_INCREMENT,
    question_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    description TEXT NOT NULL,
    date_signalement DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_signalement_question (question_id),
    INDEX idx_signalement_utilisateur (utilisateur_id),
    CONSTRAINT fk_signalement_question
        FOREIGN KEY (question_id)
        REFERENCES Question(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_signalement_utilisateur
        FOREIGN KEY (utilisateur_id)
        REFERENCES Utilisateur(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Seed data (replace hashes with real bcrypt values if needed)
INSERT INTO Utilisateur (id, pseudo, email, mdp) VALUES
    (1, 'admin', 'admin@example.com', '$2y$10$exampleexampleexampleexampleexampleexampleexampleexampleexample');

INSERT INTO Admin (utilisateur_id) VALUES (1);

INSERT INTO Questionnaire (id, nom, theme, utilisateur_id, est_publie, date_publication) VALUES
    (1, 'Quiz Reseau', 'Réseau', 1, 1, NOW());

INSERT INTO Question (id, questionnaire_id, numero, libelle, type_reponse, reponse_vrai_faux) VALUES
    (1, 1, 1, '192.168.1.1 est-elle une adresse IP privee ?', 'VraiFaux', 1),
    (2, 1, 2, 'Quelle couche du modele OSI gere le routage ?', 'ListeValeurs', NULL);

INSERT INTO Reponse (question_id, valeur, est_correcte) VALUES
    (2, 'Couche Reseau', 1),
    (2, 'Couche Physique', 0),
    (2, 'Couche Session', 0);
