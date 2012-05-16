-- phpMyAdmin SQL Dump
-- version 3.4.5deb1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le : Mer 09 Mai 2012 à 10:42
-- Version du serveur: 5.1.62
-- Version de PHP: 5.3.6-13ubuntu3.7

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT=0;
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Structure de la table `seo_meta`
--

DROP TABLE IF EXISTS `seo_meta`;
CREATE TABLE IF NOT EXISTS `seo_meta` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `website_id` int(11) unsigned DEFAULT NULL,
  `language_id` int(11) unsigned DEFAULT NULL,
  `model_id` int(11) unsigned DEFAULT NULL,
  `record_id` varchar(200) NOT NULL,
  `type` char(20) NOT NULL,
  `content` text,
  PRIMARY KEY (`id`),
  KEY `website_id` (`website_id`),
  KEY `language_id` (`language_id`),
  KEY `model_id` (`model_id`),
  KEY `record_id` (`record_id`),
  KEY `type` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=214 ;

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `seo_meta`
--
ALTER TABLE `seo_meta`
  ADD CONSTRAINT `seo_meta_ibfk_2` FOREIGN KEY (`model_id`) REFERENCES `centurion_content_type` (`id`),
  ADD CONSTRAINT `seo_meta_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `translation_language` (`id`);
SET FOREIGN_KEY_CHECKS=1;
COMMIT;
