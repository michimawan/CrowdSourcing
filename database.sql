-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 27, 2015 at 05:26 PM
-- Server version: 5.6.16
-- PHP Version: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `a`
--

-- --------------------------------------------------------

--
-- Table structure for table `komentar_statuses`
--

CREATE TABLE IF NOT EXISTS `komentar_statuses` (
  `id_status` varchar(255) NOT NULL,
  `id_komentar` varchar(255) NOT NULL,
  `id_user_komen` varchar(255) DEFAULT NULL,
  `nama_pembuat` varchar(255) DEFAULT NULL,
  `komentar` text,
  `waktu_komen` datetime DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `jml_label` int(11) NOT NULL DEFAULT '0',
  `status` enum('belum','lengkap') NOT NULL DEFAULT 'belum',
  PRIMARY KEY (`id_komentar`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `komentar_statuses`
--

INSERT INTO `komentar_statuses` (`id_status`, `id_komentar`, `id_user_komen`, `nama_pembuat`, `komentar`, `waktu_komen`, `label`, `jml_label`, `status`) VALUES
('23383061178_10152096383296179', '267046653502470_267048560168946', '723158587758586', 'Rahmad Art', 'Salam 2 jari...bismillah..mudah2an Jokowi JK menang 9 juli', '2014-07-02 02:30:08', '', 0, 'belum'),
('23383061178_10152096383296179', '270924516448017_270928699780932', '943017389044925', 'Nico Nyolee Jr Gong', '#BEJO4RI1', '2014-07-05 03:41:46', '', 0, 'belum'),
('23383061178_10152076252911179', '270924516448017_270930359780766', '10204951756212534', 'Alvira Vira', 'Cuutee', '2014-07-05 03:42:45', '', 0, 'belum'),
('23383061178_10152096383296179', '270924516448017_270937739780028', '695146633911011', 'Arbaizs Baiz', 'Salamm 2 jarii,,,!!', '2014-07-05 03:45:40', '', 0, 'belum');

-- --------------------------------------------------------

--
-- Table structure for table `statuses`
--

CREATE TABLE IF NOT EXISTS `statuses` (
  `id_status` varchar(255) NOT NULL,
  `id_pembuat` varchar(255) DEFAULT NULL,
  `nama_pembuat` varchar(200) DEFAULT NULL,
  `teks_status` text,
  `gambar_status` varchar(200) DEFAULT NULL,
  `url_komentar` varchar(200) DEFAULT NULL,
  `waktu_status` datetime DEFAULT NULL,
  PRIMARY KEY (`id_status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='FB status from Jokowi dan Prabowo';

--
-- Dumping data for table `statuses`
--

INSERT INTO `statuses` (`id_status`, `id_pembuat`, `nama_pembuat`, `teks_status`, `gambar_status`, `url_komentar`, `waktu_status`) VALUES
('23383061178_10152076252911179', '23383061178', 'Prabowo Subianto', 'Saya banyak belajar prinsip-prinsip kehidupan dari ayah saya, Prof. Sumitro Djojohadikusumo. Beliau pernah berkata:\n\nPrabowo, ???Smile in the face of adversity. Be contemptuous of danger. Undaunted in defeat. Magnanimous in victory.???\n\n"Tersenyumlah dalam menghadapi kemalangan. Beranilah menantang bahaya. Tegarlah dalam kekalahan. Selalu rendah hati akan kemenangan."', 'https://fbexternal-a.akamaihd.net/safe_image.php?d=AQCngyFIeiYXRmnz&url=https%3A%2F%2Fscontent-a.xx.fbcdn.net%2Fhphotos-xpa1%2Fv%2Ft1.0-9%2Fp130x130%2F10409212_10152076252516179_2069619468475747210_n.', 'https://www.facebook.com/23383061178/posts/10152076252911179', '2014-06-05 03:54:47'),
('23383061178_10152096383296179', '23383061178', 'Prabowo Subianto', 'Siapa yang mengaku orang Indonesia, yang hidup di Indonesia, yang bekerja di Indonesia, yang berkarya di Indonesia, membela Indonesia, adalah saudara saya. \n\nJadi wajib saya melindunginya, membantunya, mempertahankan hak-haknya. Ini adalah sumpah saya, Prabowo Subianto kepada diri saya sendiri dan kepada Allah SWT.', 'https://fbexternal-a.akamaihd.net/safe_image.php?d=AQAcntV0ZitCmmPG&url=https%3A%2F%2Ffbcdn-sphotos-g-a.akamaihd.net%2Fhphotos-ak-xpa1%2Fv%2Ft1.0-9%2Fs130x130%2F10421107_10152096382966179_452052936396', 'https://www.facebook.com/23383061178/posts/10152096383296179', '2014-06-13 10:30:08');

-- --------------------------------------------------------

--
-- Table structure for table `tabel_labels`
--

CREATE TABLE IF NOT EXISTS `tabel_labels` (
  `id_label` int(11) NOT NULL AUTO_INCREMENT,
  `id_status` varchar(255) DEFAULT NULL,
  `id_komen` varchar(255) DEFAULT NULL,
  `username_pelabel` varchar(50) DEFAULT NULL,
  `waktu_melabel` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `nama_label` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_label`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `tabel_labels`
--

INSERT INTO `tabel_labels` (`id_label`, `id_status`, `id_komen`, `username_pelabel`, `waktu_melabel`, `nama_label`) VALUES
(1, '23383061178_10152076252911179', '270924516448017_270930359780766', 'eternity.eternity.angel@gmail.com', '2015-05-27 05:22:32', 'positif'),
(2, '23383061178_10152096383296179', '267046653502470_267048560168946', 'eternity.eternity.angel@gmail.com', '2015-05-27 05:03:40', 'positif'),
(3, '23383061178_10152096383296179', '270924516448017_270928699780932', 'eternity.eternity.angel@gmail.com', '2015-05-27 05:03:43', 'negatif'),
(4, '23383061178_10152096383296179', '270924516448017_270937739780028', 'eternity.eternity.angel@gmail.com', '2015-05-27 05:03:45', 'netral');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `social_network_id` varchar(128) DEFAULT NULL,
  `email` varchar(128) NOT NULL,
  `display_name` varchar(128) NOT NULL,
  `link` varchar(512) NOT NULL,
  `picture` varchar(512) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `total_label` int(11) NOT NULL,
  `role` varchar(64) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `social_network_id`, `email`, `display_name`, `link`, `picture`, `created`, `modified`, `total_label`, `role`, `status`) VALUES
(1, '104583646065358950970', 'openpublick@gmail.com', 'Openopen ToPublic', 'https://profiles.google.com/104583646065358950970', 'https://lh3.googleusercontent.com/-xZNhvsi6BfE/AAAAAAAAAAI/AAAAAAAAAEA/9YOCjz9oDr0/photo.jpg', '2015-05-25 12:24:12', '2015-05-25 12:24:12', 0, 'admin', 1),
(5, '104860961513772835214', 'blog.1393@gmail.com', 'Michael Himawan', 'https://profiles.google.com/104860961513772835214', 'https://lh3.googleusercontent.com/-XdUIqdMkCWA/AAAAAAAAAAI/AAAAAAAAAAA/4252rscbv5M/photo.jpg', '2015-05-26 01:24:39', '2015-05-26 01:24:39', 0, 'user', 1),
(6, '113962260118921761990', 'eternity.eternity.angel@gmail.com', 'eternity angel', 'https://profiles.google.com/113962260118921761990', 'https://lh3.googleusercontent.com/-XdUIqdMkCWA/AAAAAAAAAAI/AAAAAAAAAAA/4252rscbv5M/photo.jpg', '2015-05-27 11:21:31', '2015-05-27 12:23:14', 4, 'user', 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
