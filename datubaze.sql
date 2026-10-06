CREATE DATABASE IF NOT EXISTS projekts CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE projekts;

-- Lietotāji
CREATE TABLE IF NOT EXISTS lietotaji (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lietotajvards VARCHAR(20) NOT NULL UNIQUE,
    parole_hash VARCHAR(255) NOT NULL,
    izveidots DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Piezīmes 
CREATE TABLE IF NOT EXISTS piezimes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lietotaja_id INT NOT NULL,
    virsraksts VARCHAR(100) NOT NULL,
    teksts TEXT NOT NULL,
    izveidots DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lietotaja_id) REFERENCES lietotaji(id) ON DELETE CASCADE
) ENGINE=InnoDB;

--  pieteikšanās mēģinājumi 
CREATE TABLE IF NOT EXISTS pieteiksanas_meginajumi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lietotajvards VARCHAR(255) NOT NULL,
    ip VARCHAR(45) NOT NULL,
    laiks DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
