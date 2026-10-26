-- Datenbank für die Buchverwaltung 
SET NAMES utf8mb4;
DROP DATABASE IF EXISTS buchverwaltung;
CREATE DATABASE buchverwaltung CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE buchverwaltung;

-- Tabelle 1: Benutzer
CREATE TABLE benutzer 
(
  id            INT AUTO_INCREMENT PRIMARY KEY,
  benutzername  VARCHAR(30)  NOT NULL UNIQUE,
  email         VARCHAR(100) NOT NULL UNIQUE,
  passwort_hash VARCHAR(255) NOT NULL
);

-- Tabelle 2: Genre
CREATE TABLE genre 
(
  id   INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL UNIQUE
);

-- Tabelle 3: Buch (1:N zu benutzer und zu genre)
CREATE TABLE buch 
(
  id               INT AUTO_INCREMENT PRIMARY KEY,
  benutzer_id      INT NOT NULL,
  genre_id         INT NULL,
  titel            VARCHAR(150) NOT NULL,
  autor            VARCHAR(100) NOT NULL,
  erscheinungsjahr SMALLINT NULL,
  beschreibung     TEXT NULL,
  cover            VARCHAR(255) NULL,                  -- Link zu einem Cover-Bild
  status           ENUM('moechte_lesen','lese_gerade','gelesen','abgebrochen')
                   NOT NULL DEFAULT 'moechte_lesen',
  bewertung        TINYINT NULL CHECK (bewertung BETWEEN 0 AND 5),   -- Sterne
  gemocht          TINYINT(1) NULL,                    -- 1 = gemocht, 0 = nicht gemocht, NULL = keine Angabe
  rezension        TEXT NULL,
  startdatum       DATE NULL,
  enddatum         DATE NULL,
  FOREIGN KEY (benutzer_id) REFERENCES benutzer(id) ON DELETE CASCADE,
  FOREIGN KEY (genre_id)    REFERENCES genre(id)    ON DELETE SET NULL
);

INSERT INTO genre (name) VALUES
  ('Fantasy'), ('Science-Fiction'), ('Krimi'), ('Thriller'), ('Roman'),
  ('Liebesroman'), ('Sachbuch'), ('Biografie'), ('Horror'), ('Jugendbuch'), ('Comic/Manga');
