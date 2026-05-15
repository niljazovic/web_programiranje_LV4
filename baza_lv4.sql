-- =============================================
-- LV4 - Netflix Filmovi - Baza podataka
-- Web programiranje 2024./2025.
-- =============================================

CREATE DATABASE IF NOT EXISTS netflix_lv4 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE netflix_lv4;

-- Tablica korisnika
CREATE TABLE IF NOT EXISTS korisnici (
  id INT AUTO_INCREMENT PRIMARY KEY,
  korisnicko_ime VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(100) NOT NULL UNIQUE,
  lozinka VARCHAR(255) NOT NULL,
  uloga ENUM('korisnik', 'admin') NOT NULL DEFAULT 'korisnik',
  datum_registracije TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tablica filmova
CREATE TABLE IF NOT EXISTS filmovi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  naslov VARCHAR(200) NOT NULL,
  zanr VARCHAR(100) NOT NULL,
  godina YEAR NOT NULL,
  trajanje_min INT NOT NULL,
  ocjena DECIMAL(3,1) NOT NULL DEFAULT 0.0,
  reziser VARCHAR(100),
  zemlja_porijekla VARCHAR(100),
  datum_dodavanja TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tablica željenih filmova (osobna videoteka)
CREATE TABLE IF NOT EXISTS zeljeni_filmovi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_korisnik INT NOT NULL,
  id_film INT NOT NULL,
  datum_dodavanja TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_kosarica (id_korisnik, id_film),
  FOREIGN KEY (id_korisnik) REFERENCES korisnici(id) ON DELETE CASCADE,
  FOREIGN KEY (id_film) REFERENCES filmovi(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Početni podaci - admin korisnik
-- Lozinka: admin123 (bcrypt hash)
-- =============================================
INSERT INTO korisnici (korisnicko_ime, email, lozinka, uloga) VALUES
('admin', 'admin@netflix.hr', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/lewmSGWBm5Fz2kN5O', 'admin');

-- =============================================
-- Početni podaci - filmovi iz CSV-a
-- =============================================
INSERT INTO filmovi (naslov, zanr, godina, trajanje_min, ocjena, reziser, zemlja_porijekla) VALUES
('The Shawshank Redemption', 'Drama', 1994, 142, 9.3, 'Frank Darabont', 'USA'),
('The Godfather', 'Crime, Drama', 1972, 175, 9.2, 'Francis Ford Coppola', 'USA'),
('The Dark Knight', 'Action, Crime', 2008, 152, 9.0, 'Christopher Nolan', 'UK/USA'),
('Schindler''s List', 'Biography, Drama', 1993, 195, 9.0, 'Steven Spielberg', 'USA'),
('The Lord of the Rings: The Return of the King', 'Adventure, Drama', 2003, 201, 9.0, 'Peter Jackson', 'New Zealand/USA'),
('Pulp Fiction', 'Crime, Drama', 1994, 154, 8.9, 'Quentin Tarantino', 'USA'),
('The Good the Bad and the Ugly', 'Western', 1966, 178, 8.8, 'Sergio Leone', 'Italy'),
('Forrest Gump', 'Drama, Romance', 1994, 142, 8.8, 'Robert Zemeckis', 'USA'),
('Fight Club', 'Drama', 1999, 139, 8.8, 'David Fincher', 'USA'),
('Inception', 'Action, Sci-Fi', 2010, 148, 8.8, 'Christopher Nolan', 'USA/UK'),
('The Matrix', 'Action, Sci-Fi', 1999, 136, 8.7, 'The Wachowskis', 'USA'),
('Goodfellas', 'Biography, Crime', 1990, 146, 8.7, 'Martin Scorsese', 'USA'),
('Star Wars: The Empire Strikes Back', 'Action, Adventure', 1980, 124, 8.7, 'Irvin Kershner', 'USA'),
('The Silence of the Lambs', 'Crime, Thriller', 1991, 118, 8.6, 'Jonathan Demme', 'USA'),
('Interstellar', 'Adventure, Drama', 2014, 169, 8.7, 'Christopher Nolan', 'USA/UK'),
('The Pianist', 'Biography, Drama', 2002, 150, 8.5, 'Roman Polanski', 'France/Poland'),
('The Departed', 'Crime, Drama', 2006, 151, 8.5, 'Martin Scorsese', 'USA'),
('Gladiator', 'Action, Adventure', 2000, 155, 8.5, 'Ridley Scott', 'USA/UK'),
('The Lion King', 'Animation, Adventure', 1994, 88, 8.5, 'Roger Allers', 'USA'),
('Whiplash', 'Drama, Music', 2014, 107, 8.5, 'Damien Chazelle', 'USA'),
('The Prestige', 'Drama, Mystery', 2006, 130, 8.5, 'Christopher Nolan', 'USA/UK'),
('The Green Mile', 'Crime, Drama', 1999, 189, 8.6, 'Frank Darabont', 'USA'),
('Avengers: Infinity War', 'Action, Adventure', 2018, 149, 8.4, 'Russo Brothers', 'USA'),
('Joker', 'Crime, Drama', 2019, 122, 8.4, 'Todd Phillips', 'USA/Canada'),
('The Wolf of Wall Street', 'Biography, Crime', 2013, 180, 8.2, 'Martin Scorsese', 'USA'),
('Django Unchained', 'Drama, Western', 2012, 165, 8.5, 'Quentin Tarantino', 'USA'),
('The Truman Show', 'Comedy, Drama', 1998, 103, 8.2, 'Peter Weir', 'USA'),
('Parasite', 'Comedy, Drama', 2019, 132, 8.5, 'Bong Joon-ho', 'South Korea'),
('Toy Story', 'Animation, Comedy', 1995, 81, 8.3, 'John Lasseter', 'USA'),
('The Grand Budapest Hotel', 'Adventure, Comedy', 2014, 99, 8.1, 'Wes Anderson', 'Germany/USA');
