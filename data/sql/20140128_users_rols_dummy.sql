CREATE DATABASE  IF NOT EXISTS `ad-service-web` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `ad-service-web`;
-- MySQL dump 10.13  Distrib 5.5.34, for debian-linux-gnu (i686)
--
-- Host: 127.0.0.1    Database: ad-service-web
-- ------------------------------------------------------
-- Server version	5.5.34-0ubuntu0.12.10.1

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
-- Table structure for table `user_role`
--

DROP TABLE IF EXISTS `user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_role` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `IDX_2DE8C6A3A76ED395` (`user_id`),
  KEY `IDX_2DE8C6A3D60322AC` (`role_id`),
  CONSTRAINT `FK_2DE8C6A3A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_2DE8C6A3D60322AC` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_role`
--

LOCK TABLES `user_role` WRITE;
/*!40000 ALTER TABLE `user_role` DISABLE KEYS */;
INSERT INTO `user_role` VALUES (1,1),(2,2),(3,2),(4,2);
/*!40000 ALTER TABLE `user_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` VALUES (1,'ROLE_ADMIN'),(2,'ROLE_USER');
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(255) NOT NULL,
  `email_2` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `phone_number_1` varchar(9) NOT NULL,
  `phone_number_2` varchar(9) NOT NULL,
  `movile_number_1` varchar(9) NOT NULL,
  `movile_number_2` varchar(9) NOT NULL,
  `fax` varchar(9) NOT NULL,
  `dni` varchar(9) NOT NULL,
  `sessionID` varchar(50) NOT NULL,
  `language` varchar(2) NOT NULL,
  `name` varchar(255) NOT NULL,
  `surname` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `email_1` varchar(255) NOT NULL,
  `region_id` int(11) DEFAULT NULL,
  `province_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8D93D64998260155` (`region_id`),
  KEY `IDX_8D93D649E946114A` (`province_id`),
  CONSTRAINT `FK_8D93D64998260155` FOREIGN KEY (`region_id`) REFERENCES `region` (`id`),
  CONSTRAINT `FK_8D93D649E946114A` FOREIGN KEY (`province_id`) REFERENCES `province` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'admin','nGMp2kn4S0AG2EEYBCFpjSfyLO+9wMg2n0kZHXZNo2IXCnL+ucUn3zJVuLZTaBQrpO+bV6VQzjBMe8ayFSObNg==','f0e63518ef9894780f821dc2723cfa00','admin2@grupeina.com',1,'111111111','222222222','333333333','444444444','555555555','111111111','','ca','admin','admin','Calafell','admin1@grupeina.com',9,34),(2,'user','QeDeEveLRGfrw94I1iCOzs37BWE+xnmQFjkT5EeUTwDjXWRIjQsSYghkc2kAefhuFMTPvnIaplq7xbPOesN22Q==','84a2646fadd65616d73199e9f1fae1e1','user2@grupeina.com',1,'111111111','222222222','333333333','444444444','555555555','111111111','','ca','user','user','Calafell','user1@grupeina.com',9,34),(3,'user1','nGMp2kn4S0AG2EEYBCFpjSfyLO+9wMg2n0kZHXZNo2IXCnL+ucUn3zJVuLZTaBQrpO+bV6VQzjBMe8ayFSObNg==','f0e63518ef9894780f821dc2723cfa00','user2@grupeina.com',1,'111111111','222222222','333333333','444444444','555555555','111111111','','ca','user','user','Calafell','user1@grupeina.com',9,34),(4,'user2','nGMp2kn4S0AG2EEYBCFpjSfyLO+9wMg2n0kZHXZNo2IXCnL+ucUn3zJVuLZTaBQrpO+bV6VQzjBMe8ayFSObNg==','f0e63518ef9894780f821dc2723cfa00','user2@grupeina.com',1,'111111111','222222222','333333333','444444444','555555555','111111111','','ca','user','user','Calafell','user1@grupeina.com',9,34);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-01-28  9:07:13
