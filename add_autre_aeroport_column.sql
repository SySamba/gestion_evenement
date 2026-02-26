-- Ajouter la colonne autre_aeroport à la table evenements
ALTER TABLE evenements ADD COLUMN autre_aeroport VARCHAR(255) NULL AFTER nom_aeroport;
