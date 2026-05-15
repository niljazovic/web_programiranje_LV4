-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 15, 2026 at 08:55 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `netflix_lv4`
--

-- --------------------------------------------------------

--
-- Table structure for table `filmovi`
--

CREATE TABLE `filmovi` (
  `id` int(11) NOT NULL,
  `naslov` varchar(200) NOT NULL,
  `zanr` varchar(100) NOT NULL,
  `godina` year(4) NOT NULL,
  `trajanje_min` int(11) NOT NULL,
  `ocjena` decimal(3,1) NOT NULL DEFAULT 0.0,
  `reziser` varchar(100) DEFAULT NULL,
  `zemlja_porijekla` varchar(100) DEFAULT NULL,
  `datum_dodavanja` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `filmovi`
--

INSERT INTO `filmovi` (`id`, `naslov`, `zanr`, `godina`, `trajanje_min`, `ocjena`, `reziser`, `zemlja_porijekla`, `datum_dodavanja`) VALUES
(1, 'The Shawshank Redemption', 'Drama', '1994', 142, 9.3, 'Frank Darabont', 'USA', '2026-05-15 06:31:00'),
(2, 'The Godfather', 'Crime, Drama', '1972', 175, 9.2, 'Francis Ford Coppola', 'USA', '2026-05-15 06:31:00'),
(3, 'The Dark Knight', 'Action, Crime', '2008', 152, 9.0, 'Christopher Nolan', 'UK/USA', '2026-05-15 06:31:00'),
(4, 'Schindler\'s List', 'Biography, Drama', '1993', 195, 9.0, 'Steven Spielberg', 'USA', '2026-05-15 06:31:00'),
(5, 'The Lord of the Rings: The Return of the King', 'Adventure, Drama', '2003', 201, 9.0, 'Peter Jackson', 'New Zealand/USA', '2026-05-15 06:31:00'),
(6, 'Pulp Fiction', 'Crime, Drama', '1994', 154, 8.9, 'Quentin Tarantino', 'USA', '2026-05-15 06:31:00'),
(7, 'The Good the Bad and the Ugly', 'Western', '1966', 178, 8.8, 'Sergio Leone', 'Italy', '2026-05-15 06:31:00'),
(8, 'Forrest Gump', 'Drama, Romance', '1994', 142, 8.8, 'Robert Zemeckis', 'USA', '2026-05-15 06:31:00'),
(9, 'Fight Club', 'Drama', '1999', 139, 8.8, 'David Fincher', 'USA', '2026-05-15 06:31:00'),
(10, 'Inception', 'Action, Sci-Fi', '2010', 148, 8.8, 'Christopher Nolan', 'USA/UK', '2026-05-15 06:31:00'),
(11, 'The Matrix', 'Action, Sci-Fi', '1999', 136, 8.7, 'The Wachowskis', 'USA', '2026-05-15 06:31:00'),
(12, 'Goodfellas', 'Biography, Crime', '1990', 146, 8.7, 'Martin Scorsese', 'USA', '2026-05-15 06:31:00'),
(13, 'Star Wars: The Empire Strikes Back', 'Action, Adventure', '1980', 124, 8.7, 'Irvin Kershner', 'USA', '2026-05-15 06:31:00'),
(14, 'The Silence of the Lambs', 'Crime, Thriller', '1991', 118, 8.6, 'Jonathan Demme', 'USA', '2026-05-15 06:31:00'),
(15, 'Interstellar', 'Adventure, Drama', '2014', 169, 8.7, 'Christopher Nolan', 'USA/UK', '2026-05-15 06:31:00'),
(16, 'The Pianist', 'Biography, Drama', '2002', 150, 8.5, 'Roman Polanski', 'France/Poland', '2026-05-15 06:31:00'),
(17, 'The Departed', 'Crime, Drama', '2006', 151, 8.5, 'Martin Scorsese', 'USA', '2026-05-15 06:31:00'),
(18, 'Gladiator', 'Action, Adventure', '2000', 155, 8.5, 'Ridley Scott', 'USA/UK', '2026-05-15 06:31:00'),
(19, 'The Lion King', 'Animation, Adventure', '1994', 88, 8.5, 'Roger Allers', 'USA', '2026-05-15 06:31:00'),
(20, 'Whiplash', 'Drama, Music', '2014', 107, 8.5, 'Damien Chazelle', 'USA', '2026-05-15 06:31:00'),
(21, 'The Prestige', 'Drama, Mystery', '2006', 130, 8.5, 'Christopher Nolan', 'USA/UK', '2026-05-15 06:31:00'),
(22, 'The Green Mile', 'Crime, Drama', '1999', 189, 8.6, 'Frank Darabont', 'USA', '2026-05-15 06:31:00'),
(23, 'Avengers: Infinity War', 'Action, Adventure', '2018', 149, 8.4, 'Russo Brothers', 'USA', '2026-05-15 06:31:00'),
(24, 'Joker', 'Crime, Drama', '2019', 122, 8.4, 'Todd Phillips', 'USA/Canada', '2026-05-15 06:31:00'),
(25, 'The Wolf of Wall Street', 'Biography, Crime', '2013', 180, 8.2, 'Martin Scorsese', 'USA', '2026-05-15 06:31:00'),
(26, 'Django Unchained', 'Drama, Western', '2012', 165, 8.5, 'Quentin Tarantino', 'USA', '2026-05-15 06:31:00'),
(27, 'The Truman Show', 'Comedy, Drama', '1998', 103, 8.2, 'Peter Weir', 'USA', '2026-05-15 06:31:00'),
(28, 'Parasite', 'Comedy, Drama', '2019', 132, 8.5, 'Bong Joon-ho', 'South Korea', '2026-05-15 06:31:00'),
(29, 'Toy Story', 'Animation, Comedy', '1995', 81, 8.3, 'John Lasseter', 'USA', '2026-05-15 06:31:00'),
(30, 'The Grand Budapest Hotel', 'Adventure, Comedy', '2014', 99, 8.1, 'Wes Anderson', 'Germany/USA', '2026-05-15 06:31:00'),
(31, 'Drama', 'Drama', '2026', 140, 7.5, 'Nikola Iljazovic', 'USA', '2026-05-15 06:38:35');

-- --------------------------------------------------------

--
-- Table structure for table `korisnici`
--

CREATE TABLE `korisnici` (
  `id` int(11) NOT NULL,
  `korisnicko_ime` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `lozinka` varchar(255) NOT NULL,
  `uloga` enum('korisnik','admin') NOT NULL DEFAULT 'korisnik',
  `datum_registracije` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `korisnici`
--

INSERT INTO `korisnici` (`id`, `korisnicko_ime`, `email`, `lozinka`, `uloga`, `datum_registracije`) VALUES
(1, 'admin', 'admin@netflix.hr', '$2y$12$lIjtr7/4J2gad9qTcU2JNeim2T1mw4xLGxFSB3IwULDoTs9S9LzO.', 'admin', '2026-05-15 06:31:00'),
(2, 'nikola', 'nikola@nikola.hr', '$2y$12$Hh51CB8eoEFx4/7KOJtWWu7SKtnJAqIwT5DyW6MOqFgMrZ.hgtlHO', 'korisnik', '2026-05-15 06:44:05');

-- --------------------------------------------------------

--
-- Table structure for table `zeljeni_filmovi`
--

CREATE TABLE `zeljeni_filmovi` (
  `id` int(11) NOT NULL,
  `id_korisnik` int(11) NOT NULL,
  `id_film` int(11) NOT NULL,
  `datum_dodavanja` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `zeljeni_filmovi`
--

INSERT INTO `zeljeni_filmovi` (`id`, `id_korisnik`, `id_film`, `datum_dodavanja`) VALUES
(1, 1, 23, '2026-05-15 06:36:57'),
(2, 1, 9, '2026-05-15 06:37:04'),
(3, 1, 26, '2026-05-15 06:37:06'),
(4, 1, 31, '2026-05-15 06:38:48'),
(5, 2, 23, '2026-05-15 06:44:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `filmovi`
--
ALTER TABLE `filmovi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `korisnici`
--
ALTER TABLE `korisnici`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `korisnicko_ime` (`korisnicko_ime`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `zeljeni_filmovi`
--
ALTER TABLE `zeljeni_filmovi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_kosarica` (`id_korisnik`,`id_film`),
  ADD KEY `id_film` (`id_film`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `filmovi`
--
ALTER TABLE `filmovi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `korisnici`
--
ALTER TABLE `korisnici`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `zeljeni_filmovi`
--
ALTER TABLE `zeljeni_filmovi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `zeljeni_filmovi`
--
ALTER TABLE `zeljeni_filmovi`
  ADD CONSTRAINT `zeljeni_filmovi_ibfk_1` FOREIGN KEY (`id_korisnik`) REFERENCES `korisnici` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `zeljeni_filmovi_ibfk_2` FOREIGN KEY (`id_film`) REFERENCES `filmovi` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
