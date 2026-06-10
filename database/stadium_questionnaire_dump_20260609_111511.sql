/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.14-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: Stadium_Questionnaire
-- ------------------------------------------------------
-- Server version	10.11.14-MariaDB-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `Stadium_Questionnaire`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `Stadium_Questionnaire` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `Stadium_Questionnaire`;

--
-- Table structure for table `Admin`
--

DROP TABLE IF EXISTS `Admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin` (
  `utilisateur_id` int(11) NOT NULL,
  `date_promotion` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`utilisateur_id`),
  CONSTRAINT `fk_admin_utilisateur` FOREIGN KEY (`utilisateur_id`) REFERENCES `Utilisateur` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;


LOCK TABLES `Admin` WRITE;
/*!40000 ALTER TABLE `Admin` DISABLE KEYS */;
INSERT INTO `Admin` VALUES
(5,'2026-03-16 14:04:03');
/*!40000 ALTER TABLE `Admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `LogAdmin`
--

CREATE TABLE `LogAdmin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `details` text NOT NULL,
  `date_log` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_log_admin_date` (`date_log`),
  KEY `idx_log_admin_user` (`utilisateur_id`),
  CONSTRAINT `fk_log_admin_utilisateur` FOREIGN KEY (`utilisateur_id`) REFERENCES `Utilisateur` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `LogAdmin`
--

LOCK TABLES `LogAdmin` WRITE;
/*!40000 ALTER TABLE `LogAdmin` DISABLE KEYS */;
INSERT INTO `LogAdmin` VALUES
(17,5,'CONNEXION_REUSSIE','Connexion reussie pour l\'utilisateur \'admin\'.','2026-03-16 14:07:18'),
(18,5,'CONNEXION_REUSSIE','Connexion reussie pour l\'utilisateur \'admin\'.','2026-03-16 14:09:33'),
(19,5,'CONNEXION_REUSSIE','Connexion reussie pour l\'utilisateur \'admin\'.','2026-03-16 14:24:00'),
(20,NULL,'CONNEXION_ECHOUEE','Tentative de connexion echouee pour l\'identifiant \'admin\'.','2026-03-27 10:05:23'),
(21,5,'CONNEXION_REUSSIE','Connexion reussie pour l\'utilisateur \'admin\'.','2026-03-27 10:05:30'),
(22,5,'CONNEXION_REUSSIE','Connexion reussie pour l\'utilisateur \'admin\'.','2026-04-20 14:19:54'),
(26,5,'CONNEXION_REUSSIE','Connexion reussie pour l\'utilisateur \'admin\'.','2026-04-20 15:01:10'),
(27,5,'CONNEXION_REUSSIE','Connexion reussie pour l\'utilisateur \'admin\'.','2026-04-20 15:14:37'),
(28,5,'CONNEXION_REUSSIE','Connexion reussie pour l\'utilisateur \'admin\'.','2026-05-26 00:17:54'),
(29,5,'QUESTIONNAIRE_TERMINE','Le questionnaire \'Les choux\' (theme : Culture générale) a ete termine par \'admin\' avec un score de 1/2 (50%).','2026-05-26 00:19:19'),
(30,5,'CONNEXION_REUSSIE','Connexion reussie pour l\'utilisateur \'admin\'.','2026-05-27 09:39:34'),
(31,5,'QUESTIONNAIRE_TERMINE','Le questionnaire \'initiation réseau\' (theme : Réseau) a ete termine par \'admin\' avec un score de 2/1 (200%).','2026-05-27 09:40:58');
/*!40000 ALTER TABLE `LogAdmin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Question`
--

DROP TABLE IF EXISTS `Question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(250) DEFAULT NULL,
  `id_type` int(11) DEFAULT NULL,
  `numero` int(11) NOT NULL DEFAULT 1,
  `questionnaire_id` int(11) DEFAULT NULL,
  `type_reponse` varchar(50) NOT NULL DEFAULT 'VraiFaux',
  `reponse_vrai_faux` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_type` (`id_type`),
  CONSTRAINT `Question_ibfk_1` FOREIGN KEY (`id_type`) REFERENCES `Type_question` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Question`
--

LOCK TABLES `Question` WRITE;
/*!40000 ALTER TABLE `Question` DISABLE KEYS */;
INSERT INTO `Question` VALUES
(3,'Quel est le meilleur chou ?',NULL,1,13,'ListeValeurs',NULL),
(4,'La voiture d\'axel est elle ouverte ?',NULL,2,13,'VraiFaux',1),
(5,'une adresse ip est constituée de 3 octets',NULL,1,14,'VraiFaux',0);
/*!40000 ALTER TABLE `Question` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Questionnaire`
--

DROP TABLE IF EXISTS `Questionnaire`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Questionnaire` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(200) NOT NULL,
  `theme` varchar(100) NOT NULL,
  `niveau` int(11) NOT NULL DEFAULT 1,
  `utilisateur_id` int(11) NOT NULL,
  `est_publie` tinyint(1) NOT NULL DEFAULT 0,
  `date_publication` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`),
  KEY `idx_questionnaire_niveau` (`niveau`),
  CONSTRAINT `Questionnaire_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `Utilisateur` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Questionnaire`
--

LOCK TABLES `Questionnaire` WRITE;
/*!40000 ALTER TABLE `Questionnaire` DISABLE KEYS */;
INSERT INTO `Questionnaire` VALUES
(13,'Les choux','Culture générale',1,5,1,'2026-04-20 14:44:58'),
(14,'initiation réseau','Réseau',2,5,1,'2026-04-20 14:45:01'),
(15,'MEGATRONUS PRIME','Réseau',3,5,0,NULL);
/*!40000 ALTER TABLE `Questionnaire` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `QuestionnaireConnexion`
--

DROP TABLE IF EXISTS `QuestionnaireConnexion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `QuestionnaireConnexion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int(11) NOT NULL,
  `questionnaire_id` int(11) NOT NULL,
  `date_connexion` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_qconn_user` (`utilisateur_id`),
  KEY `idx_qconn_questionnaire` (`questionnaire_id`),
  KEY `idx_qconn_date` (`date_connexion`),
  CONSTRAINT `fk_qconn_questionnaire` FOREIGN KEY (`questionnaire_id`) REFERENCES `Questionnaire` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_qconn_user` FOREIGN KEY (`utilisateur_id`) REFERENCES `Utilisateur` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `QuestionnaireConnexion`
--

LOCK TABLES `QuestionnaireConnexion` WRITE;
/*!40000 ALTER TABLE `QuestionnaireConnexion` DISABLE KEYS */;
INSERT INTO `QuestionnaireConnexion` VALUES
(1,5,13,'2026-04-13 16:57:26'),
(2,5,13,'2026-04-20 14:17:35'),
(3,5,13,'2026-04-20 14:45:26'),
(4,5,13,'2026-04-20 16:51:49'),
(5,5,13,'2026-04-20 16:52:15'),
(6,5,13,'2026-05-26 00:19:06'),
(7,5,14,'2026-05-26 00:22:21'),
(8,5,14,'2026-05-27 09:40:35');
/*!40000 ALTER TABLE `QuestionnaireConnexion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Reponse`
--

DROP TABLE IF EXISTS `Reponse`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Reponse` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contenu` varchar(50) DEFAULT NULL,
  `question_id` int(11) DEFAULT NULL,
  `valeur` varchar(500) DEFAULT NULL,
  `est_correcte` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Reponse`
--

LOCK TABLES `Reponse` WRITE;
/*!40000 ALTER TABLE `Reponse` DISABLE KEYS */;
INSERT INTO `Reponse` VALUES
(1,NULL,3,'Chou rouge',0),
(2,NULL,3,'Chou blanc',0),
(3,NULL,3,'Chou de bruxel',0),
(4,NULL,3,'Chou a la creme',0),
(6,NULL,3,'Chouquette',1);
/*!40000 ALTER TABLE `Reponse` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ReponseUtilisateur`
--

DROP TABLE IF EXISTS `ReponseUtilisateur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ReponseUtilisateur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `questionnaire_id` int(11) NOT NULL,
  `reponse_texte` varchar(500) DEFAULT NULL,
  `reponse_bool` tinyint(1) DEFAULT NULL,
  `est_correcte` tinyint(1) NOT NULL DEFAULT 0,
  `date_reponse` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`),
  KEY `question_id` (`question_id`),
  KEY `questionnaire_id` (`questionnaire_id`),
  CONSTRAINT `ReponseUtilisateur_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `Utilisateur` (`id`),
  CONSTRAINT `ReponseUtilisateur_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `Question` (`id`),
  CONSTRAINT `ReponseUtilisateur_ibfk_3` FOREIGN KEY (`questionnaire_id`) REFERENCES `Questionnaire` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ReponseUtilisateur`
--

LOCK TABLES `ReponseUtilisateur` WRITE;
/*!40000 ALTER TABLE `ReponseUtilisateur` DISABLE KEYS */;
INSERT INTO `ReponseUtilisateur` VALUES
(10,5,5,14,'false',0,1,'2026-05-26 00:22:28'),
(11,5,3,13,'Chou a la creme',NULL,0,'2026-05-26 00:19:14'),
(12,5,4,13,'Vrai',1,1,'2026-05-26 00:19:19'),
(13,5,5,14,'Faux',0,1,'2026-05-27 09:40:58');
/*!40000 ALTER TABLE `ReponseUtilisateur` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Signalement`
--

DROP TABLE IF EXISTS `Signalement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Signalement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `date_signalement` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `question_id` (`question_id`),
  KEY `utilisateur_id` (`utilisateur_id`),
  CONSTRAINT `Signalement_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `Question` (`id`) ON DELETE CASCADE,
  CONSTRAINT `Signalement_ibfk_2` FOREIGN KEY (`utilisateur_id`) REFERENCES `Utilisateur` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Signalement`
--

LOCK TABLES `Signalement` WRITE;
/*!40000 ALTER TABLE `Signalement` DISABLE KEYS */;
INSERT INTO `Signalement` VALUES
(2,3,5,'j\'aime pas les chouquettes on fais quoi mtn','2026-03-16 14:08:23'),
(3,3,6,'je prefere les choux a la creme','2026-03-16 14:22:55'),
(4,4,5,'bouton terminer ne fonctionne plus','2026-04-13 16:37:34');
/*!40000 ALTER TABLE `Signalement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Type_question`
--

DROP TABLE IF EXISTS `Type_question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Type_question` (
  `id` int(11) NOT NULL,
  `libelle` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Type_question`
--

LOCK TABLES `Type_question` WRITE;
/*!40000 ALTER TABLE `Type_question` DISABLE KEYS */;
/*!40000 ALTER TABLE `Type_question` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Role`
--

DROP TABLE IF EXISTS `Role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `desc` varchar(255) NOT NULL,
  `niveau` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_role_nom` (`nom`),
  KEY `idx_role_niveau` (`niveau`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Role`
--

LOCK TABLES `Role` WRITE;
/*!40000 ALTER TABLE `Role` DISABLE KEYS */;
INSERT INTO `Role` VALUES
(1,'Administratif','Personnel des services administratifs',1),
(2,'Technicien','Personnel technique',2),
(3,'Support','Personnel des services support ICT',3),
(4,'Gestion','Personnel comptable et financier, management',3),
(5,'Direction','Directeur de departements',4);
/*!40000 ALTER TABLE `Role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Utilisateur`
--

DROP TABLE IF EXISTS `Utilisateur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Utilisateur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pseudo` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mdp` varchar(255) NOT NULL,
  `idrole` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pseudo` (`pseudo`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_utilisateur_role` (`idrole`),
  CONSTRAINT `fk_utilisateur_role` FOREIGN KEY (`idrole`) REFERENCES `Role` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Utilisateur`
--

LOCK TABLES `Utilisateur` WRITE;
/*!40000 ALTER TABLE `Utilisateur` DISABLE KEYS */;
INSERT INTO `Utilisateur` VALUES
(5,'admin','admin@stadium.local','$2y$10$eFRJyJs/99bC1sN9bKcRw.qxbwCAnfsnkm.aOiJxDjnTgVeTofr.G',5),
(6,'joueur','joueur@stadium.local','$2y$10$ByTxUTzPte96GBXb8KP8COkSh6//z.S5qrupmrWIdupcfeJ97.hxm',1);
/*!40000 ALTER TABLE `Utilisateur` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `repondre`
--

DROP TABLE IF EXISTS `repondre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `repondre` (
  `id_question` int(11) NOT NULL,
  `id_reponse` int(11) NOT NULL,
  `bonne_reponse` tinyint(1) DEFAULT NULL,
  `num_reponse` int(11) DEFAULT NULL,
  `poids` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_question`,`id_reponse`),
  KEY `id_reponse` (`id_reponse`),
  CONSTRAINT `repondre_ibfk_1` FOREIGN KEY (`id_question`) REFERENCES `Question` (`id`),
  CONSTRAINT `repondre_ibfk_2` FOREIGN KEY (`id_reponse`) REFERENCES `Reponse` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `repondre`
--

LOCK TABLES `repondre` WRITE;
/*!40000 ALTER TABLE `repondre` DISABLE KEYS */;
/*!40000 ALTER TABLE `repondre` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'Stadium_Questionnaire'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-09 11:15:11
