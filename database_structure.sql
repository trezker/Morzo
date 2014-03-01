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
  `Inventory_ID` bigint(20) NOT NULL,
  `Hunt_ID` bigint(20) DEFAULT NULL,
  `Hunger` double NOT NULL DEFAULT '0',
  `Health` double NOT NULL DEFAULT '128',
  `Inside_object_ID` bigint(20) DEFAULT NULL,
  `Species_ID` bigint(20) NOT NULL,
  `Corpse_object_ID` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Inventory_ID_UNIQUE` (`Inventory_ID`),
  KEY `Actor_fk_Project` (`Project_ID`),
  KEY `Actor_fk_Inventory` (`Inventory_ID`),
  KEY `Actor_fk_Hunt_idx` (`Hunt_ID`),
  KEY `Actor_fk_Species_idx` (`Species_ID`),
  KEY `Actor_fk_User` (`User_ID`),
  KEY `Actor_fk_Location` (`Location_ID`),
  KEY `Actor_fk_Inside_object_idx` (`Inside_object_ID`),
  KEY `Actor_fk_Corpse_object_idx` (`Corpse_object_ID`),
  CONSTRAINT `Actor_fk_Corpse_object` FOREIGN KEY (`Corpse_object_ID`) REFERENCES `Object` (`ID`) ON DELETE SET NULL,
  CONSTRAINT `Actor_fk_Hunt` FOREIGN KEY (`Hunt_ID`) REFERENCES `Hunt` (`ID`) ON DELETE SET NULL,
  CONSTRAINT `Actor_fk_Inside_object` FOREIGN KEY (`Inside_object_ID`) REFERENCES `Object` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `Actor_fk_Inventory` FOREIGN KEY (`Inventory_ID`) REFERENCES `Inventory` (`ID`),
  CONSTRAINT `Actor_fk_Location` FOREIGN KEY (`Location_ID`) REFERENCES `Location` (`ID`),
  CONSTRAINT `Actor_fk_Project` FOREIGN KEY (`Project_ID`) REFERENCES `Project` (`ID`),
  CONSTRAINT `Actor_fk_Species` FOREIGN KEY (`Species_ID`) REFERENCES `Species` (`ID`),
  CONSTRAINT `Actor_fk_User` FOREIGN KEY (`User_ID`) REFERENCES `User` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;
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
  CONSTRAINT `Actor_event_fk_Event` FOREIGN KEY (`Event_ID`) REFERENCES `Event` (`ID`) ON DELETE CASCADE,
  CONSTRAINT `Actor_event_fk_Actor` FOREIGN KEY (`Actor_ID`) REFERENCES `Actor` (`ID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=511 DEFAULT CHARSET=utf8;
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
  CONSTRAINT `Actor_name_fk_Actor` FOREIGN KEY (`Actor_ID`) REFERENCES `Actor` (`ID`) ON DELETE CASCADE,
  CONSTRAINT `Actor_name_fk_Named_actor` FOREIGN KEY (`Named_actor_ID`) REFERENCES `Actor` (`ID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8;
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
-- Table structure for table `Blog`
--

DROP TABLE IF EXISTS `Blog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Blog` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `User_ID` bigint(20) NOT NULL,
  `Name` varchar(45) NOT NULL,
  `Description` varchar(500) NOT NULL,
  `Created_date` datetime NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_UNIQUE` (`ID`),
  UNIQUE KEY `Name_UNIQUE` (`Name`),
  KEY `Blog_fk_User` (`User_ID`),
  CONSTRAINT `Blog_fk_User` FOREIGN KEY (`User_ID`) REFERENCES `User` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Blogpost`
--

DROP TABLE IF EXISTS `Blogpost`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Blogpost` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Blog_ID` bigint(20) NOT NULL,
  `Created_date` datetime NOT NULL,
  `Content` text NOT NULL,
  `Title` varchar(45) NOT NULL,
  `Hidden` tinyint(1) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_UNIQUE` (`ID`),
  KEY `Blogpost_fk_Blog` (`Blog_ID`),
  CONSTRAINT `Blogpost_fk_Blog` FOREIGN KEY (`Blog_ID`) REFERENCES `Blog` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Category`
--

DROP TABLE IF EXISTS `Category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Category` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Name` varchar(45) NOT NULL,
  `Is_tool` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_UNIQUE` (`ID`),
  UNIQUE KEY `Name_UNIQUE` (`Name`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Count`
--

DROP TABLE IF EXISTS `Count`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Count` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Name` varchar(32) NOT NULL,
  `Value` bigint(20) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Event`
--

DROP TABLE IF EXISTS `Event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Event` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Translation_handle` varchar(64) NOT NULL DEFAULT '',
  `Message` varchar(256) DEFAULT NULL,
  `Ingame_time` bigint(20) NOT NULL,
  `Real_time` datetime NOT NULL,
  `From_actor_ID` bigint(20) DEFAULT NULL,
  `To_actor_ID` bigint(20) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Hunt`
--

DROP TABLE IF EXISTS `Hunt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Hunt` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Stage_ID` bigint(20) NOT NULL DEFAULT '1',
  `Prey_ID` bigint(20) DEFAULT NULL,
  `Duration` int(11) NOT NULL,
  `Hours_left` int(11) NOT NULL,
  `Location_ID` bigint(20) NOT NULL,
  `UpdateTick` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_UNIQUE` (`ID`),
  KEY `fk_Hunt_huntstage_idx` (`Stage_ID`),
  KEY `Hunt_fk_species_idx` (`Prey_ID`),
  KEY `Hunt_fk_prey_idx` (`Prey_ID`),
  KEY `Hunt_fk_location_idx` (`Location_ID`),
  CONSTRAINT `Hunt_fk_huntstage` FOREIGN KEY (`Stage_ID`) REFERENCES `Huntstage` (`ID`),
  CONSTRAINT `Hunt_fk_location` FOREIGN KEY (`Location_ID`) REFERENCES `Location` (`ID`),
  CONSTRAINT `Hunt_fk_prey` FOREIGN KEY (`Prey_ID`) REFERENCES `Species` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Hunt_species`
--

DROP TABLE IF EXISTS `Hunt_species`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Hunt_species` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Hunt_ID` bigint(20) NOT NULL,
  `Species_ID` bigint(20) NOT NULL,
  `Amount` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_UNIQUE` (`ID`),
  UNIQUE KEY `Hunt_species_unique` (`Hunt_ID`,`Species_ID`),
  KEY `Hunt_species_fk_hunt_idx` (`Hunt_ID`),
  KEY `Hunt_species_fk_species_idx` (`Species_ID`),
  CONSTRAINT `Hunt_species_fk_hunt` FOREIGN KEY (`Hunt_ID`) REFERENCES `Hunt` (`ID`) ON DELETE CASCADE,
  CONSTRAINT `Hunt_species_fk_species` FOREIGN KEY (`Species_ID`) REFERENCES `Species` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Huntstage`
--

DROP TABLE IF EXISTS `Huntstage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Huntstage` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Name` varchar(45) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_UNIQUE` (`ID`),
  UNIQUE KEY `Name_UNIQUE` (`Name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Inventory`
--

DROP TABLE IF EXISTS `Inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Inventory` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Mass_limit` double DEFAULT NULL,
  `Volume_limit` double DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_UNIQUE` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=152 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Inventory_resource`
--

DROP TABLE IF EXISTS `Inventory_resource`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Inventory_resource` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Inventory_ID` bigint(20) NOT NULL,
  `Resource_ID` bigint(20) NOT NULL,
  `Amount` double NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_UNIQUE` (`ID`),
  UNIQUE KEY `IR_UNIQUE` (`Inventory_ID`,`Resource_ID`),
  KEY `Inventory_resource_fk_Inventory` (`Inventory_ID`),
  KEY `Inventory_resource_fk_Resource` (`Resource_ID`),
  CONSTRAINT `Inventory_resource_fk_Inventory` FOREIGN KEY (`Inventory_ID`) REFERENCES `Inventory` (`ID`),
  CONSTRAINT `Inventory_resource_fk_Resource` FOREIGN KEY (`Resource_ID`) REFERENCES `Resource` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=346 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Key_form`
--

DROP TABLE IF EXISTS `Key_form`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Key_form` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_UNIQUE` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Language`
--

DROP TABLE IF EXISTS `Language`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Language` (
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
  `Inventory_ID` bigint(20) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `XY` (`X`,`Y`) USING BTREE,
  UNIQUE KEY `Inventory_ID_UNIQUE` (`Inventory_ID`),
  KEY `Location_fk_Biome` (`Biome_ID`),
  KEY `Location_fk_Inventory` (`Inventory_ID`),
  CONSTRAINT `Location_fk_Biome` FOREIGN KEY (`Biome_ID`) REFERENCES `Biome` (`ID`),
  CONSTRAINT `Location_fk_Inventory` FOREIGN KEY (`Inventory_ID`) REFERENCES `Inventory` (`ID`)
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
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Location_species`
--

DROP TABLE IF EXISTS `Location_species`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Location_species` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Location_ID` bigint(20) NOT NULL,
  `Species_ID` bigint(20) NOT NULL,
  `Population` int(11) NOT NULL DEFAULT '0',
  `Actor_spawn` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_UNIQUE` (`ID`),
  UNIQUE KEY `Location_species_UNIQUE` (`Location_ID`,`Species_ID`),
  KEY `Location_species_fk_Location` (`Location_ID`),
  KEY `Location_species_fk_Species` (`Species_ID`),
  CONSTRAINT `Location_species_fk_Location` FOREIGN KEY (`Location_ID`) REFERENCES `Location` (`ID`),
  CONSTRAINT `Location_species_fk_Species` FOREIGN KEY (`Species_ID`) REFERENCES `Species` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Measure`
--

DROP TABLE IF EXISTS `Measure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Measure` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `name_UNIQUE` (`name`),
  UNIQUE KEY `id_UNIQUE` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Object`
--

DROP TABLE IF EXISTS `Object`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Object` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Product_ID` bigint(20) NOT NULL,
  `Inventory_ID` bigint(20) DEFAULT NULL,
  `Quality` double NOT NULL,
  `Rot` double NOT NULL,
  `Label` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_UNIQUE` (`ID`),
  KEY `Object_fk_Product` (`Product_ID`),
  KEY `Object_fk_Inventory` (`Inventory_ID`),
  CONSTRAINT `Object_fk_Inventory` FOREIGN KEY (`Inventory_ID`) REFERENCES `Inventory` (`ID`),
  CONSTRAINT `Object_fk_Product` FOREIGN KEY (`Product_ID`) REFERENCES `Product` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=474 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Object_inventory`
--

DROP TABLE IF EXISTS `Object_inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Object_inventory` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Object_ID` bigint(20) NOT NULL,
  `Inventory_ID` bigint(20) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_UNIQUE` (`ID`),
  KEY `Object_inventory_fk_Object` (`Object_ID`),
  KEY `Object_inventory_fk_Inventory` (`Inventory_ID`),
  CONSTRAINT `Object_inventory_fk_Inventory` FOREIGN KEY (`Inventory_ID`) REFERENCES `Inventory` (`ID`),
  CONSTRAINT `Object_inventory_fk_Object` FOREIGN KEY (`Object_ID`) REFERENCES `Object` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='This is for objects that are containers.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Object_key`
--

DROP TABLE IF EXISTS `Object_key`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Object_key` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Object_ID` bigint(20) NOT NULL,
  `Key_form_ID` bigint(20) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_UNIQUE` (`ID`),
  UNIQUE KEY `Object_ID_UNIQUE` (`Object_ID`),
  KEY `Key_fk_object` (`Object_ID`),
  KEY `Key_fk_key_form` (`Key_form_ID`),
  CONSTRAINT `Key_fk_key_form` FOREIGN KEY (`Key_form_ID`) REFERENCES `Key_form` (`ID`),
  CONSTRAINT `Key_fk_object` FOREIGN KEY (`Object_ID`) REFERENCES `Object` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Object_lock`
--

DROP TABLE IF EXISTS `Object_lock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Object_lock` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Object_ID` bigint(20) NOT NULL,
  `Attached_object_ID` bigint(20) DEFAULT NULL,
  `Key_form_ID` bigint(20) DEFAULT NULL,
  `Is_locked` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_UNIQUE` (`ID`),
  UNIQUE KEY `Object_ID_UNIQUE` (`Object_ID`),
  KEY `Lock_fk_object` (`Object_ID`),
  KEY `Lock_fk_attached` (`Attached_object_ID`),
  KEY `Lock_fk_key_form` (`Key_form_ID`),
  CONSTRAINT `Lock_fk_attached` FOREIGN KEY (`Attached_object_ID`) REFERENCES `Object` (`ID`),
  CONSTRAINT `Lock_fk_key_form` FOREIGN KEY (`Key_form_ID`) REFERENCES `Key_form` (`ID`),
  CONSTRAINT `Lock_fk_object` FOREIGN KEY (`Object_ID`) REFERENCES `Object` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Product`
--

DROP TABLE IF EXISTS `Product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Product` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Name` varchar(45) NOT NULL,
  `Mass` double NOT NULL,
  `Volume` double NOT NULL,
  `Rot_rate` double NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_UNIQUE` (`ID`),
  UNIQUE KEY `Name_UNIQUE` (`Name`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Product_category`
--

DROP TABLE IF EXISTS `Product_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Product_category` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Product_ID` bigint(20) NOT NULL,
  `Category_ID` bigint(20) NOT NULL,
  `Food_nutrition` double DEFAULT NULL,
  `Container_mass_limit` double DEFAULT NULL,
  `Container_volume_limit` double DEFAULT NULL,
  `Tool_efficiency` double DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_UNIQUE` (`ID`),
  UNIQUE KEY `Product_category_unique` (`Product_ID`,`Category_ID`),
  KEY `Product_category_fk_Product` (`Product_ID`),
  KEY `Product_category_fk_Category` (`Category_ID`),
  CONSTRAINT `Product_category_fk_Category` FOREIGN KEY (`Category_ID`) REFERENCES `Category` (`ID`),
  CONSTRAINT `Product_category_fk_Product` FOREIGN KEY (`Product_ID`) REFERENCES `Product` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8;
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
  `Progress` int(11) NOT NULL DEFAULT '0',
  `Active` tinyint(1) NOT NULL DEFAULT '0',
  `UpdateTick` bigint(20) NOT NULL,
  `Inventory_ID` bigint(20) NOT NULL,
  `Location_inventory_ID` bigint(20) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Inventory_ID_UNIQUE` (`Inventory_ID`),
  KEY `Project_fk_Creator` (`Creator_actor_ID`),
  KEY `Project_fk_Recipe` (`Recipe_ID`),
  KEY `Project_fk_Inventory` (`Inventory_ID`),
  KEY `Project_fk_Location_inventory` (`Location_inventory_ID`),
  CONSTRAINT `Project_fk_Creator` FOREIGN KEY (`Creator_actor_ID`) REFERENCES `Actor` (`ID`),
  CONSTRAINT `Project_fk_Inventory` FOREIGN KEY (`Inventory_ID`) REFERENCES `Inventory` (`ID`),
  CONSTRAINT `Project_fk_Location_inventory` FOREIGN KEY (`Location_inventory_ID`) REFERENCES `Inventory` (`ID`),
  CONSTRAINT `Project_fk_Recipe` FOREIGN KEY (`Recipe_ID`) REFERENCES `Recipe` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
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
  `Amount` double NOT NULL,
  `From_nature` tinyint(1) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `Recipe_input_fk_Recipe` (`Recipe_ID`),
  KEY `Recipe_input_fk_Resource` (`Resource_ID`),
  CONSTRAINT `Recipe_input_fk_Recipe` FOREIGN KEY (`Recipe_ID`) REFERENCES `Recipe` (`ID`),
  CONSTRAINT `Recipe_input_fk_Resource` FOREIGN KEY (`Resource_ID`) REFERENCES `Resource` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
  `Amount` double NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `Recipe_output_fk_Recipe` (`Recipe_ID`),
  KEY `Recipe_output_fk_Resource` (`Resource_ID`),
  CONSTRAINT `Recipe_output_fk_Recipe` FOREIGN KEY (`Recipe_ID`) REFERENCES `Recipe` (`ID`),
  CONSTRAINT `Recipe_output_fk_Resource` FOREIGN KEY (`Resource_ID`) REFERENCES `Resource` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Recipe_product_input`
--

DROP TABLE IF EXISTS `Recipe_product_input`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Recipe_product_input` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Recipe_ID` bigint(20) NOT NULL,
  `Product_ID` bigint(20) NOT NULL,
  `Amount` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_UNIQUE` (`ID`),
  UNIQUE KEY `Recipe_product_input_UNIQUE` (`Recipe_ID`,`Product_ID`),
  KEY `Recipe_product_input_fk_Product` (`Product_ID`),
  KEY `Recipe_product_input_fk_Recipe` (`Recipe_ID`),
  CONSTRAINT `Recipe_product_input_fk_Product` FOREIGN KEY (`Product_ID`) REFERENCES `Product` (`ID`),
  CONSTRAINT `Recipe_product_input_fk_Recipe` FOREIGN KEY (`Recipe_ID`) REFERENCES `Recipe` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Recipe_product_output`
--

DROP TABLE IF EXISTS `Recipe_product_output`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Recipe_product_output` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Recipe_ID` bigint(20) NOT NULL,
  `Product_ID` bigint(20) NOT NULL,
  `Amount` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_UNIQUE` (`ID`),
  UNIQUE KEY `Recipe_product_output_UNIQUE` (`Recipe_ID`,`Product_ID`),
  KEY `Recipe_product_output_fk_Product` (`Product_ID`),
  KEY `Recipe_product_output_fk_Recipe` (`Recipe_ID`),
  CONSTRAINT `Recipe_product_output_fk_Product` FOREIGN KEY (`Product_ID`) REFERENCES `Product` (`ID`),
  CONSTRAINT `Recipe_product_output_fk_Recipe` FOREIGN KEY (`Recipe_ID`) REFERENCES `Recipe` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Recipe_tool`
--

DROP TABLE IF EXISTS `Recipe_tool`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Recipe_tool` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Recipe_ID` bigint(20) NOT NULL,
  `Category_ID` bigint(20) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_UNIQUE` (`ID`),
  KEY `Recipe_tool_fk_recipe` (`Recipe_ID`),
  KEY `Recipe_tool_fk_category` (`Category_ID`),
  CONSTRAINT `Recipe_tool_fk_category` FOREIGN KEY (`Category_ID`) REFERENCES `Category` (`ID`),
  CONSTRAINT `Recipe_tool_fk_recipe` FOREIGN KEY (`Recipe_ID`) REFERENCES `Recipe` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
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
  `Measure` bigint(20) NOT NULL DEFAULT '1',
  `Mass` double NOT NULL,
  `Volume` double NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Name_UNIQUE` (`Name`),
  KEY `Resource_fk_Measure` (`Measure`),
  CONSTRAINT `Resource_fk_Measure` FOREIGN KEY (`Measure`) REFERENCES `Measure` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Resource_category`
--

DROP TABLE IF EXISTS `Resource_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Resource_category` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Category_ID` bigint(20) NOT NULL,
  `Resource_ID` bigint(20) NOT NULL,
  `Food_nutrition` float DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_UNIQUE` (`ID`),
  UNIQUE KEY `Category_resource_unique` (`Category_ID`,`Resource_ID`) USING BTREE,
  KEY `Resource_category_fk_Resource` (`Resource_ID`),
  KEY `Resource_category_fk_Category` (`Category_ID`),
  CONSTRAINT `Resource_category_fk_Category` FOREIGN KEY (`Category_ID`) REFERENCES `Category` (`ID`),
  CONSTRAINT `Resource_category_fk_Resource` FOREIGN KEY (`Resource_ID`) REFERENCES `Resource` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Species`
--

DROP TABLE IF EXISTS `Species`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Species` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Name` varchar(45) NOT NULL,
  `Max_population` int(11) NOT NULL,
  `Corpse_product_ID` bigint(20) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_UNIQUE` (`ID`),
  UNIQUE KEY `Name_UNIQUE` (`Name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Translation`
--

DROP TABLE IF EXISTS `Translation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Translation` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Language_ID` bigint(20) NOT NULL,
  `Handle` varchar(64) NOT NULL,
  `Text` text NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Translation_unique` (`Language_ID`,`Handle`),
  KEY `Translation_fk_Language` (`Language_ID`),
  CONSTRAINT `Translation_fk_Language` FOREIGN KEY (`Language_ID`) REFERENCES `Language` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8;
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
  `Has_moved` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ActorID_unique` (`ActorID`) USING BTREE,
  KEY `Travel_fk_Destination` (`DestinationID`),
  KEY `Travel_fk_Origin` (`OriginID`),
  CONSTRAINT `Travel_fk_Actor` FOREIGN KEY (`ActorID`) REFERENCES `Actor` (`ID`),
  CONSTRAINT `Travel_fk_Destination` FOREIGN KEY (`DestinationID`) REFERENCES `Location` (`ID`),
  CONSTRAINT `Travel_fk_Origin` FOREIGN KEY (`OriginID`) REFERENCES `Location` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
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
  `Banned_from` date DEFAULT NULL,
  `Banned_to` date DEFAULT NULL,
  `Max_actors` bigint(20) NOT NULL,
  `Last_active` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Username` (`Username`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
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
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping events for database 'morzo'
--
/*!50106 SET @save_time_zone= @@TIME_ZONE */ ;
/*!50106 DROP EVENT IF EXISTS `e_update` */;
DELIMITER ;;
ALTER DATABASE morzo CHARACTER SET latin1 COLLATE latin1_swedish_ci ;;
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
ALTER DATABASE morzo CHARACTER SET utf8 COLLATE utf8_general_ci ;;
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

-- Dump completed on 2014-03-01 18:32:38
