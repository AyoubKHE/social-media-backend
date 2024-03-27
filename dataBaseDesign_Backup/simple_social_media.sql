-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 25 mars 2024 à 11:59
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `simple_social_media`
--

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `tweet_id` int(11) NOT NULL,
  `body` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `comments`
--

INSERT INTO `comments` (`id`, `author_id`, `tweet_id`, `body`, `created_at`) VALUES
(2, 1, 19, 'first comment', '2024-03-13 11:43:10'),
(3, 1, 5, 'comment', '2024-03-13 15:35:44'),
(4, 3, 12, 'c', '2024-03-15 11:14:19'),
(5, 4, 18, 'c', '2024-03-15 11:14:19'),
(6, 8, 22, 'c', '2024-03-15 11:14:19'),
(7, 6, 11, 'c', '2024-03-15 11:14:19'),
(8, 3, 19, 'c', '2024-03-15 11:14:19'),
(9, 8, 23, 'c', '2024-03-15 11:14:19'),
(10, 6, 2, 'c', '2024-03-15 11:14:19'),
(11, 4, 14, 'c', '2024-03-15 11:14:19'),
(12, 6, 19, 'c', '2024-03-15 11:14:19'),
(13, 7, 5, 'c', '2024-03-15 11:14:19'),
(14, 1, 10, 'c', '2024-03-15 11:14:19'),
(15, 4, 20, 'c', '2024-03-15 11:14:19'),
(16, 4, 9, 'c', '2024-03-15 11:14:19'),
(17, 7, 9, 'c', '2024-03-15 11:14:19'),
(18, 6, 24, 'c', '2024-03-15 11:14:19'),
(19, 8, 19, 'c', '2024-03-15 11:14:19'),
(20, 6, 12, 'c', '2024-03-15 11:14:19'),
(21, 3, 24, 'c', '2024-03-15 11:14:19'),
(22, 6, 22, 'c', '2024-03-15 11:14:19'),
(23, 7, 7, 'c', '2024-03-16 12:04:20'),
(24, 1, 2, 'comment 1', '2024-03-17 13:06:00'),
(25, 1, 37, 'c', '2024-03-17 13:22:55'),
(26, 1, 37, 'hhh', '2024-03-17 13:23:40'),
(27, 1, 36, 'hhh', '2024-03-17 13:23:58'),
(28, 1, 36, 'hh', '2024-03-17 13:24:36'),
(29, 1, 35, 'hh', '2024-03-17 13:25:05'),
(30, 1, 37, 'hhh', '2024-03-19 12:01:43'),
(31, 1, 37, 'aaa', '2024-03-19 12:53:08'),
(32, 1, 38, 'hh', '2024-03-19 12:53:30'),
(33, 1, 38, 'dd', '2024-03-19 12:53:34'),
(34, 1, 23, 'hhh', '2024-03-19 12:54:48');

-- --------------------------------------------------------

--
-- Structure de la table `tweets`
--

CREATE TABLE `tweets` (
  `id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `title` text DEFAULT NULL,
  `body` text DEFAULT NULL,
  `tweet_image_path` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `tweets`
--

INSERT INTO `tweets` (`id`, `author_id`, `title`, `body`, `tweet_image_path`, `created_at`) VALUES
(2, 1, 'title 1', 'body 1', NULL, '2024-03-05 14:32:22'),
(5, 1, 'title 2', 'body 2', '/images/tweets_image/tweet_5.png', '2024-03-07 18:10:57'),
(7, 1, 'title 2', 'body 2', '/images/tweets_image/tweet_7.png', '2024-03-07 18:18:43'),
(8, 1, 'title', 'body', '/images/tweets_image/tweet_8.png', '2024-03-10 14:53:14'),
(9, 1, 'title', 'body', '/images/tweets_image/tweet_9.png', '2024-03-10 14:54:00'),
(10, 1, 'az', 'aza', NULL, '2024-03-10 15:31:20'),
(11, 1, '', 'az', NULL, '2024-03-10 16:07:08'),
(12, 1, 'azaz', '', NULL, '2024-03-10 16:07:25'),
(13, 1, NULL, NULL, '/images/tweets_image/tweet_13.png', '2024-03-10 16:11:18'),
(14, 1, 'title 2', 'body 2', '/images/tweets_image/tweet_14.png', '2024-03-12 13:22:37'),
(15, 1, 'title 2', 'body 2', '/images/tweets_image/tweet_15.png', '2024-03-12 13:30:12'),
(16, 1, 'title 2', 'body 2', '/images/tweets_image/tweet_16.png', '2024-03-12 13:32:07'),
(17, 1, 'title 2', 'body 2', '/images/tweets_image/tweet_17.png', '2024-03-12 13:32:57'),
(18, 1, NULL, NULL, '/images/tweets_image/tweet_18.png', '2024-03-12 14:00:20'),
(19, 1, 'title', 'body', '/images/tweets_image/tweet_19.png', '2024-03-12 20:38:28'),
(20, 3, 'title', 'body', NULL, '2024-03-15 11:09:23'),
(21, 4, 'tiltle', NULL, NULL, '2024-03-15 11:10:49'),
(22, 7, NULL, 'body', NULL, '2024-03-15 11:10:49'),
(23, 6, 'title', '', NULL, '2024-03-15 11:10:49'),
(24, 8, 'title', '', NULL, '2024-03-15 11:10:49'),
(25, 3, 'title', '', NULL, '2024-03-15 11:10:49'),
(26, 7, 't', NULL, NULL, '2024-03-15 11:15:44'),
(27, 6, 't', NULL, NULL, '2024-03-15 11:15:44'),
(28, 3, 't', NULL, NULL, '2024-03-15 11:15:44'),
(29, 6, 't', NULL, NULL, '2024-03-15 11:15:44'),
(30, 3, 't', NULL, NULL, '2024-03-15 11:15:44'),
(31, 4, 'title', 'body', NULL, '2024-03-16 11:40:21'),
(32, 6, 't1', NULL, NULL, '2024-03-16 11:58:57'),
(33, 1, 'ze', 'ze', NULL, '2024-03-17 11:34:26'),
(34, 1, 'er', NULL, NULL, '2024-03-17 11:35:00'),
(35, 1, 'dfdf', NULL, NULL, '2024-03-17 11:35:41'),
(36, 1, 'dfdfghh', NULL, NULL, '2024-03-17 11:35:54'),
(37, 1, 'zesdq', NULL, NULL, '2024-03-17 11:36:32'),
(38, 1, 'aa', 'aa', '/images/tweets_image/tweet_38.png', '2024-03-19 12:02:07'),
(39, 1, 'c', 'c', '/images/tweets_image/tweet_39.png', '2024-03-21 11:48:09');

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `tweets_view`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `tweets_view` (
`tweet_id` int(11)
,`author_id` int(11)
,`title` text
,`body` text
,`tweet_image_path` text
,`tweet_created_at` datetime
,`tweet_comments_count` bigint(21)
);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_image_path` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `full_name`, `username`, `password`, `profile_image_path`, `created_at`) VALUES
(1, 'Ayoub Kheyar', 'AyKHE', '$2y$10$lbBIqEltYnybMMZA1RDpoOYMP13U3zVXJBrYBKRbh0yyPzl.IbJhW', '/images/Users_profile_image/AyKHE.png', '2024-03-04 11:21:07'),
(3, 'Salim Kheyar', 'saKHE', '$2y$10$HE0hqyD4VDcSO1tKLwtWmOdeMfp5xK4Wj/GxOyY4CJZpV9cPQ0ajC', NULL, '2024-03-04 17:11:16'),
(4, 'Ahmed Kheyar', 'ahKHE', '123456', NULL, '2024-03-06 11:45:13'),
(6, 'Djahid Kheyar', 'djKHE', '$2y$10$6fDfS/N6GsF65pU1k.YdWOC5AHFCtfvSx7YoLUX02/0GYHEcL/xoy', NULL, '2024-03-12 12:33:11'),
(7, 'Fateh Kheyar', 'faKHE', '$2y$10$t1Xndcuj/ErxEafNcPEgDOQW2qKJtZv9fgAbR8Sn3uRD24PbsSoa.', '/images/Users_profile_image/faKHE.png', '2024-03-12 12:40:24'),
(8, 'Bahman Kheyar', 'baKHE', '$2y$10$nXwrCPkM3ffR.SBReQssiekkjAMuKXKZMDhInjRO6E478opkc6Joa', '/images/Users_profile_image/baKHE.png', '2024-03-12 13:14:33'),
(9, 'Smail Kheyar', 'smKHE', '$2y$10$mPyn23fQzDLhmayU5lhec.jGUmRpEBzSYVDoKmC58n7ludUMq7TMy', NULL, '2024-03-15 12:31:54'),
(10, 'Walid Kheyar', 'waKHE', '123456789', NULL, '2024-03-15 19:31:25');

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `users_view`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `users_view` (
`user_id` int(11)
,`full_name` varchar(50)
,`username` varchar(50)
,`password` varchar(255)
,`profile_image_path` text
,`user_created_at` datetime
,`user_tweets_count` bigint(21)
,`user_comments_count` bigint(21)
);

-- --------------------------------------------------------

--
-- Structure de la vue `tweets_view`
--
DROP TABLE IF EXISTS `tweets_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `tweets_view`  AS SELECT `t`.`id` AS `tweet_id`, `t`.`author_id` AS `author_id`, `t`.`title` AS `title`, `t`.`body` AS `body`, `t`.`tweet_image_path` AS `tweet_image_path`, `t`.`created_at` AS `tweet_created_at`, CASE WHEN `c`.`id` is null THEN 0 ELSE count(`t`.`id`) END AS `tweet_comments_count` FROM (`tweets` `t` left join `comments` `c` on(`t`.`id` = `c`.`tweet_id`)) GROUP BY `t`.`id` ;

-- --------------------------------------------------------

--
-- Structure de la vue `users_view`
--
DROP TABLE IF EXISTS `users_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `users_view`  AS WITH users_tweets AS (SELECT `u`.`id` AS `user_id`, `u`.`full_name` AS `full_name`, `u`.`username` AS `username`, `u`.`password` AS `password`, `u`.`profile_image_path` AS `profile_image_path`, `u`.`created_at` AS `user_created_at`, CASE WHEN `t`.`id` is null THEN 0 ELSE count(`t`.`id`) END AS `user_tweets_count` FROM (`users` `u` left join `tweets` `t` on(`u`.`id` = `t`.`author_id`)) GROUP BY `u`.`id`), users_comments AS (SELECT `u`.`id` AS `user_id`, CASE WHEN `c`.`id` is null THEN 0 ELSE count(`u`.`id`) END AS `user_comments_count` FROM (`users` `u` left join `comments` `c` on(`u`.`id` = `c`.`author_id`)) GROUP BY `u`.`id`)  SELECT `ut`.`user_id` AS `user_id`, `ut`.`full_name` AS `full_name`, `ut`.`username` AS `username`, `ut`.`password` AS `password`, `ut`.`profile_image_path` AS `profile_image_path`, `ut`.`user_created_at` AS `user_created_at`, `ut`.`user_tweets_count` AS `user_tweets_count`, `uc`.`user_comments_count` AS `user_comments_count` FROM (`users_tweets` `ut` join `users_comments` `uc` on(`ut`.`user_id` = `uc`.`user_id`)))  ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
