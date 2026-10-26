-- Optional: Demo-Benutzer zum Ausprobieren (Login: demo / Passwort: demo1234)
SET NAMES utf8mb4;
USE buchverwaltung;
INSERT INTO benutzer (benutzername, email, passwort_hash) VALUES ('demo', 'demo@example.com', '$2y$10$dXLvd4kLg6lKePmUgHIq.OiiZzwc5y.soSHWPA2spr9Hdwbx0IB1m');
SET @u = LAST_INSERT_ID();
INSERT INTO buch (benutzer_id, genre_id, titel, autor, erscheinungsjahr, beschreibung, status, bewertung, gemocht, rezension, startdatum, enddatum) VALUES
(@u, (SELECT id FROM genre WHERE name='Fantasy'), 'Der Hobbit', 'J. R. R. Tolkien', 1937, 'Bilbo Beutlin wird von Gandalf und dreizehn Zwergen auf ein Abenteuer geschickt.', 'gelesen', 5, 1, 'Ein Klassiker, der sich wunderbar liest.', '2026-08-01', '2026-08-12'),
(@u, (SELECT id FROM genre WHERE name='Science-Fiction'), 'Der Marsianer', 'Andy Weir', 2011, 'Ein Astronaut muss allein auf dem Mars überleben.', 'gelesen', 4, 1, 'Spannend und humorvoll.', '2026-09-02', '2026-09-10'),
(@u, (SELECT id FROM genre WHERE name='Krimi'), 'Das Parfum', 'Patrick Süskind', 1985, NULL, 'abgebrochen', 2, 0, 'Konnte mich nicht fesseln.', '2026-09-15', NULL),
(@u, (SELECT id FROM genre WHERE name='Science-Fiction'), 'Dune', 'Frank Herbert', 1965, 'Epos um den Wüstenplaneten Arrakis.', 'lese_gerade', NULL, NULL, NULL, '2026-10-01', NULL),
(@u, (SELECT id FROM genre WHERE name='Thriller'), 'Der Schwarm', 'Frank Schätzing', 2004, NULL, 'moechte_lesen', NULL, NULL, NULL, NULL, NULL),
(@u, (SELECT id FROM genre WHERE name='Roman'), 'Die Verwandlung', 'Franz Kafka', 1915, NULL, 'moechte_lesen', NULL, NULL, NULL, NULL, NULL);
