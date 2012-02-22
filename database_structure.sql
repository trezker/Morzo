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
  `Project_ID` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Character_fk_User` (`User_ID`),
  KEY `Character_fk_Location` (`Location_ID`),
  KEY `Actor_fk_Project` (`Project_ID`),
  CONSTRAINT `Actor_fk_Project` FOREIGN KEY (`Project_ID`) REFERENCES `Project` (`ID`),
  CONSTRAINT `Character_fk_Location` FOREIGN KEY (`Location_ID`) REFERENCES `Location` (`ID`),
  CONSTRAINT `Character_fk_User` FOREIGN KEY (`User_ID`) REFERENCES `User` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Actor_event`
--

DROP TABLE IF EXISTS `Actor_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Actor_event` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Actor_ID` bigint(20) NOT NULL,
  `Event_ID` bigint(20) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Actor_event_unique` (`Actor_ID`,`Event_ID`),
  KEY `Actor_event_fk_Actor` (`Actor_ID`),
  KEY `Actor_event_fk_Event` (`Event_ID`),
  CONSTRAINT `Actor_event_fk_Actor` FOREIGN KEY (`Actor_ID`) REFERENCES `Actor` (`ID`),
  CONSTRAINT `Actor_event_fk_Event` FOREIGN KEY (`Event_ID`) REFERENCES `Event` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=141 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Actor_inventory`
--

DROP TABLE IF EXISTS `Actor_inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Actor_inventory` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Actor_ID` bigint(20) NOT NULL,
  `Resource_ID` bigint(20) NOT NULL,
  `Amount` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Actor_inventory_unique` (`Actor_ID`,`Resource_ID`),
  KEY `Actor_inventory_fk_Actor` (`Actor_ID`),
  KEY `Actor_inventory_fk_Resource` (`Resource_ID`),
  CONSTRAINT `Actor_inventory_fk_Actor` FOREIGN KEY (`Actor_ID`) REFERENCES `Actor` (`ID`),
  CONSTRAINT `Actor_inventory_fk_Resource` FOREIGN KEY (`Resource_ID`) REFERENCES `Resource` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Biome`
--

DROP TABLE IF EXISTS `Biome`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Biome` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Name` varchar(45) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Name_UNIQUE` (`Name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Count`
--

DROP TABLE IF EXISTS `Count`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Count` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Name` varchar(16) NOT NULL,
  `Value` bigint(20) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Event`
--

DROP TABLE IF EXISTS `Event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Event` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `From_actor_ID` bigint(20) DEFAULT NULL,
  `To_actor_ID` bigint(20) DEFAULT NULL,
  `Message` varchar(256) NOT NULL,
  `Ingame_time` bigint(20) NOT NULL,
  `Real_time` datetime NOT NULL,
  `From_location_ID` bigint(20) DEFAULT NULL,
  `To_location_ID` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Event_fk_From_actor` (`From_actor_ID`),
  KEY `Event_fk_To_actor` (`To_actor_ID`),
  KEY `Event_fk_From_location` (`From_location_ID`),
  KEY `Event_fk_To_location` (`To_location_ID`),
  CONSTRAINT `Event_fk_From_actor` FOREIGN KEY (`From_actor_ID`) REFERENCES `Actor` (`ID`),
  CONSTRAINT `Event_fk_From_location` FOREIGN KEY (`From_location_ID`) REFERENCES `Location` (`ID`),
  CONSTRAINT `Event_fk_To_actor` FOREIGN KEY (`To_actor_ID`) REFERENCES `Actor` (`ID`),
  CONSTRAINT `Event_fk_To_location` FOREIGN KEY (`To_location_ID`) REFERENCES `Location` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Landscape`
--

DROP TABLE IF EXISTS `Landscape`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Landscape` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Name` varchar(45) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Name_UNIQUE` (`Name`)
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
  `Biome_ID` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `XY` (`X`,`Y`) USING BTREE,
  KEY `Location_fk_Biome` (`Biome_ID`),
  CONSTRAINT `Location_fk_Biome` FOREIGN KEY (`Biome_ID`) REFERENCES `Biome` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Location_resource`
--

DROP TABLE IF EXISTS `Location_resource`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Location_resource` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Location_ID` bigint(20) NOT NULL,
  `Resource_ID` bigint(20) NOT NULL,
  `Landscape_ID` bigint(20) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Location_resource_unique` (`Location_ID`,`Resource_ID`,`Landscape_ID`),
  KEY `Location_resource_fk_Location` (`Location_ID`),
  KEY `Location_resource_fk_Resource` (`Resource_ID`),
  KEY `Location_resource_fk_Landscape` (`Landscape_ID`),
  CONSTRAINT `Location_resource_fk_Landscape` FOREIGN KEY (`Landscape_ID`) REFERENCES `Landscape` (`ID`),
  CONSTRAINT `Location_resource_fk_Location` FOREIGN KEY (`Location_ID`) REFERENCES `Location` (`ID`),
  CONSTRAINT `Location_resource_fk_Resource` FOREIGN KEY (`Resource_ID`) REFERENCES `Resource` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Project`
--

DROP TABLE IF EXISTS `Project`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Project` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Creator_actor_ID` bigint(20) NOT NULL,
  `Recipe_ID` bigint(20) NOT NULL,
  `Cycles_left` int(11) NOT NULL,
  `Created_time` bigint(20) NOT NULL,
  `Progress` int(11) NOT NULL,
  `Active` tinyint(1) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `Project_fk_Creator` (`Creator_actor_ID`),
  KEY `Project_fk_Recipe` (`Recipe_ID`),
  CONSTRAINT `Project_fk_Creator` FOREIGN KEY (`Creator_actor_ID`) REFERENCES `Actor` (`ID`),
  CONSTRAINT `Project_fk_Recipe` FOREIGN KEY (`Recipe_ID`) REFERENCES `Recipe` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Project_input`
--

DROP TABLE IF EXISTS `Project_input`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Project_input` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Project_ID` bigint(20) NOT NULL,
  `Resource_ID` bigint(20) NOT NULL,
  `Amount` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `Project_input_fk_Project` (`Project_ID`),
  KEY `Project_input_fk_Resource` (`Resource_ID`),
  CONSTRAINT `Project_input_fk_Project` FOREIGN KEY (`Project_ID`) REFERENCES `Project` (`ID`),
  CONSTRAINT `Project_input_fk_Resource` FOREIGN KEY (`Resource_ID`) REFERENCES `Resource` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Recipe`
--

DROP TABLE IF EXISTS `Recipe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Recipe` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Name` varchar(45) NOT NULL,
  `Cycle_time` int(11) NOT NULL,
  `Allow_fraction_output` tinyint(1) NOT NULL,
  `Require_full_cycle` tinyint(1) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Name_UNIQUE` (`Name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Recipe_input`
--

DROP TABLE IF EXISTS `Recipe_input`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Recipe_input` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Recipe_ID` bigint(20) NOT NULL,
  `Resource_ID` bigint(20) NOT NULL,
  `Amount` int(11) NOT NULL,
  `From_nature` tinyint(1) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `Recipe_input_fk_Recipe` (`Recipe_ID`),
  KEY `Recipe_input_fk_Resource` (`Resource_ID`),
  CONSTRAINT `Recipe_input_fk_Recipe` FOREIGN KEY (`Recipe_ID`) REFERENCES `Recipe` (`ID`),
  CONSTRAINT `Recipe_input_fk_Resource` FOREIGN KEY (`Resource_ID`) REFERENCES `Resource` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Recipe_landscape`
--

DROP TABLE IF EXISTS `Recipe_landscape`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Recipe_landscape` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Recipe_ID` bigint(20) NOT NULL,
  `Landscape_ID` bigint(20) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Recipe_landscape_unique` (`Recipe_ID`,`Landscape_ID`),
  KEY `Recipe_landscape_fk_Recipe` (`Recipe_ID`),
  KEY `Recipe_landscape_fk_Landscape` (`Landscape_ID`),
  CONSTRAINT `Recipe_landscape_fk_Landscape` FOREIGN KEY (`Landscape_ID`) REFERENCES `Landscape` (`ID`),
  CONSTRAINT `Recipe_landscape_fk_Recipe` FOREIGN KEY (`Recipe_ID`) REFERENCES `Recipe` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Recipe_output`
--

DROP TABLE IF EXISTS `Recipe_output`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Recipe_output` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Recipe_ID` bigint(20) NOT NULL,
  `Resource_ID` bigint(20) NOT NULL,
  `Amount` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `Recipe_output_fk_Recipe` (`Recipe_ID`),
  KEY `Recipe_output_fk_Resource` (`Resource_ID`),
  CONSTRAINT `Recipe_output_fk_Recipe` FOREIGN KEY (`Recipe_ID`) REFERENCES `Recipe` (`ID`),
  CONSTRAINT `Recipe_output_fk_Resource` FOREIGN KEY (`Resource_ID`) REFERENCES `Resource` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Resource`
--

DROP TABLE IF EXISTS `Resource`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Resource` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Name` varchar(45) NOT NULL,
  `Is_natural` tinyint(1) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Name_UNIQUE` (`Name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Travel`
--

DROP TABLE IF EXISTS `Travel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Travel` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `ActorID` bigint(20) NOT NULL,
  `DestinationID` bigint(20) NOT NULL,
  `OriginID` bigint(20) NOT NULL,
  `X` double NOT NULL,
  `Y` double NOT NULL,
  `UpdateTick` bigint(20) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ActorID_unique` (`ActorID`) USING BTREE,
  KEY `Travel_fk_Destination` (`DestinationID`),
  KEY `Travel_fk_Origin` (`OriginID`),
  CONSTRAINT `Travel_fk_Actor` FOREIGN KEY (`ActorID`) REFERENCES `Actor` (`ID`),
  CONSTRAINT `Travel_fk_Destination` FOREIGN KEY (`DestinationID`) REFERENCES `Location` (`ID`),
  CONSTRAINT `Travel_fk_Origin` FOREIGN KEY (`OriginID`) REFERENCES `Location` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `Session_ID` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Username` (`Username`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping events for database 'morzo'
--
/*!50106 SET @save_time_zone= @@TIME_ZONE */ ;
/*!50106 DROP EVENT IF EXISTS `e_update` */;
DELIMITER ;;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;;
/*!50003 SET character_set_client  = latin1 */ ;;
/*!50003 SET character_set_results = latin1 */ ;;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;;
/*!50003 SET sql_mode              = '' */ ;;
/*!50003 SET @saved_time_zone      = @@time_zone */ ;;
/*!50003 SET time_zone             = 'SYSTEM' */ ;;
/*!50106 CREATE EVENT `e_update` ON SCHEDULE EVERY 3 HOUR STARTS '2011-08-10 00:00:00' ON COMPLETION NOT PRESERVE ENABLE DO update Count set Value=Value+1 where Name='Update' */ ;;
/*!50003 SET time_zone             = @saved_time_zone */ ;;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;;
/*!50003 SET character_set_client  = @saved_cs_client */ ;;
/*!50003 SET character_set_results = @saved_cs_results */ ;;
/*!50003 SET collation_connection  = @saved_col_connection */ ;;
DELIMITER ;
/*!50106 SET TIME_ZONE= @save_time_zone */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-02-22 20:19:17
