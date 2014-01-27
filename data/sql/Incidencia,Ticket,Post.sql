CREATE DATABASE  IF NOT EXISTS `ad-service-web` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `ad-service-web`;
-- MySQL dump 10.13  Distrib 5.5.32, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: ad-service-web
-- ------------------------------------------------------
-- Server version	5.5.32-0ubuntu0.12.10.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `incidence`
--

DROP TABLE IF EXISTS `incidence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `incidence` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL,
  `importance` int(11) NOT NULL,
  `solution` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_17060417700047D2` (`ticket_id`),
  KEY `IDX_170604176BF700BD` (`status_id`),
  CONSTRAINT `FK_170604176BF700BD` FOREIGN KEY (`status_id`) REFERENCES `status` (`id`),
  CONSTRAINT `FK_17060417700047D2` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `incidence`
--

LOCK TABLES `incidence` WRITE;
/*!40000 ALTER TABLE `incidence` DISABLE KEYS */;
INSERT INTO `incidence` VALUES (1,2,0,3,'mensaje4','DESCRIPCION'),(3,2,0,2,'mensaje4','asd'),(4,2,0,2,'mensaje4','asd'),(5,2,0,2,'mensaje4','asd'),(6,2,0,2,'mensaje4','asd'),(7,2,0,3,'mensaje4','gsdhiebgduisav');
/*!40000 ALTER TABLE `incidence` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket`
--

DROP TABLE IF EXISTS `ticket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `userModified_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `importance` int(11) NOT NULL,
  `date_created` date NOT NULL,
  `date_modified` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_97A0ADA3A76ED395` (`user_id`),
  KEY `IDX_97A0ADA3E7BB4453` (`userModified_id`),
  CONSTRAINT `FK_97A0ADA3A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_97A0ADA3E7BB4453` FOREIGN KEY (`userModified_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket`
--

LOCK TABLES `ticket` WRITE;
/*!40000 ALTER TABLE `ticket` DISABLE KEYS */;
INSERT INTO `ticket` VALUES (1,1,NULL,'titulo1',0,1,'2014-01-23','0000-00-00'),(2,1,NULL,'titulo2',0,2,'2014-01-23','0000-00-00'),(8,1,NULL,'titulo3',0,3,'2014-01-23','0000-00-00'),(9,1,NULL,'titulo3',0,3,'2014-01-23','0000-00-00'),(10,1,NULL,'titulo4',1,4,'2014-01-23','0000-00-00'),(11,1,NULL,'titulo5',0,5,'2014-01-23','0000-00-00'),(12,1,NULL,'titulo5',0,5,'2014-01-23','0000-00-00'),(13,1,NULL,'titulo5',0,5,'2014-01-23','0000-00-00'),(14,1,NULL,'titulo5',0,5,'2014-01-23','0000-00-00');
/*!40000 ALTER TABLE `ticket` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post`
--

DROP TABLE IF EXISTS `post`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5A8A6C8D700047D2` (`ticket_id`),
  KEY `IDX_5A8A6C8DA76ED395` (`user_id`),
  CONSTRAINT `FK_5A8A6C8D700047D2` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`id`),
  CONSTRAINT `FK_5A8A6C8DA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post`
--

LOCK TABLES `post` WRITE;
/*!40000 ALTER TABLE `post` DISABLE KEYS */;
INSERT INTO `post` VALUES (1,1,1,'t1111111'),(2,2,1,'mensaje1'),(3,8,1,'t33333333333333333333333'),(4,9,1,'t33333333333333333333333'),(5,10,1,'44444444444444444'),(6,11,1,'555555555555'),(7,12,1,'555555555555'),(8,13,1,'555555555555'),(9,14,1,'555555555555'),(12,2,2,'mensaje2'),(13,2,1,'mensaje3'),(14,2,2,'mensaje4');
/*!40000 ALTER TABLE `post` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-01-27 16:06:34
