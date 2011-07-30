-- MySQL dump 10.13  Distrib 5.1.54, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: morzo
-- ------------------------------------------------------
-- Server version	5.1.54-1ubuntu4

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
-- Table structure for table `Access`
--

DROP TABLE IF EXISTS `Access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Access` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Accessname` varchar(32) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Name` (`Accessname`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Actor`
--

DROP TABLE IF EXISTS `Actor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Actor` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `User_ID` bigint(20) DEFAULT NULL,
  `Location_ID` bigint(20) NOT NULL,
  `Inhabitable` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ID`),
  KEY `Character_fk_User` (`User_ID`),
  KEY `Character_fk_Location` (`Location_ID`),
  CONSTRAINT `Character_fk_Location` FOREIGN KEY (`Location_ID`) REFERENCES `Location` (`ID`),
  CONSTRAINT `Character_fk_User` FOREIGN KEY (`User_ID`) REFERENCES `User` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Actor_name`
--

DROP TABLE IF EXISTS `Actor_name`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Actor_name` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Named_actor_ID` bigint(20) NOT NULL,
  `Actor_ID` bigint(20) NOT NULL,
  `Name` varchar(64) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Unique` (`Actor_ID`,`Named_actor_ID`) USING BTREE,
  KEY `Actor_name_fk_Named_actor` (`Named_actor_ID`),
  CONSTRAINT `Actor_name_fk_Actor` FOREIGN KEY (`Actor_ID`) REFERENCES `Actor` (`ID`),
  CONSTRAINT `Actor_name_fk_Named_actor` FOREIGN KEY (`Named_actor_ID`) REFERENCES `Actor` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Location`
--

DROP TABLE IF EXISTS `Location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Location` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `X` bigint(20) NOT NULL,
  `Y` bigint(20) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `XY` (`X`,`Y`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Location_name`
--

DROP TABLE IF EXISTS `Location_name`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Location_name` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Location_ID` bigint(20) NOT NULL,
  `Actor_ID` bigint(20) NOT NULL,
  `Name` varchar(64) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Unique` (`Actor_ID`,`Location_ID`) USING BTREE,
  KEY `Location_name_fk_Location` (`Location_ID`),
  CONSTRAINT `Location_name_fk_Actor` FOREIGN KEY (`Actor_ID`) REFERENCES `Actor` (`ID`),
  CONSTRAINT `Location_name_fk_Location` FOREIGN KEY (`Location_ID`) REFERENCES `Location` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User`
--

DROP TABLE IF EXISTS `User`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `User` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Username` varchar(32) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Username` (`Username`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_access`
--

DROP TABLE IF EXISTS `User_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `User_access` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `User_ID` bigint(20) NOT NULL,
  `Access_ID` bigint(20) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `User_fk_constraint` (`User_ID`),
  KEY `Access_fk_constraint` (`Access_ID`),
  CONSTRAINT `Access_fk_constraint` FOREIGN KEY (`Access_ID`) REFERENCES `Access` (`ID`),
  CONSTRAINT `User_fk_constraint` FOREIGN KEY (`User_ID`) REFERENCES `User` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_openID`
--

DROP TABLE IF EXISTS `User_openID`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `User_openID` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `OpenID` varchar(255) NOT NULL,
  `UserID` bigint(20) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `OpenID_UNIQUE` (`OpenID`),
  KEY `User_openID_fk_User` (`UserID`),
  CONSTRAINT `User_openID_fk_User` FOREIGN KEY (`UserID`) REFERENCES `User` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2011-07-30 18:05:11
