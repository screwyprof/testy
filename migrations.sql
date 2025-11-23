/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.3-MariaDB, for debian-linux-gnu (aarch64)
--
-- Host: db    Database: mariadb
-- ------------------------------------------------------
-- Server version	11.1.6-MariaDB-ubu2204

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `answers`
--

DROP TABLE IF EXISTS `answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `answers` (
  `ans_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ans_qst_id` int(11) unsigned NOT NULL DEFAULT 0,
  `ans_text` text NOT NULL,
  `ans_is_correct` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`ans_id`),
  KEY `ans_qst_id` (`ans_qst_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1011 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `answers`
--

LOCK TABLES `answers` WRITE;
/*!40000 ALTER TABLE `answers` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `answers` VALUES
(1,1,'Коала',1),
(2,1,'Панда',0),
(3,1,'Суслик',0),
(4,1,'Кенгуру',1),
(5,2,'Лев',0),
(6,2,'Пума',0),
(7,2,'Рысь',0),
(8,2,'Волк',1),
(9,3,'корова',1),
(10,4,'Четыре',4),
(11,4,'Пять',5),
(12,4,'Один',1),
(13,4,'Два',2),
(14,4,'Три',3),
(15,5,'жираф',1),
(16,6,'Немецкая овчарка',4),
(17,6,'Йоркширский терьер',1),
(18,6,'Шотландский терьер',2),
(19,6,'Дог',5),
(20,6,'Стаффордширский терьер',3),
(21,7,'Кобра',0),
(22,7,'Гюрза',0),
(23,7,'Питон',1),
(24,7,'Гадюка',0),
(25,7,'Медянка',0),
(26,8,'Корова',0),
(27,8,'Лошадь',0),
(28,8,'Зебра',0),
(29,8,'Коза',1),
(30,8,'Сайгак',1),
(31,8,'Бизон',0),
(32,8,'Олень',1);
/*!40000 ALTER TABLE `answers` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `questions`
--

DROP TABLE IF EXISTS `questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `questions` (
  `qst_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `qst_test_id` int(11) unsigned NOT NULL DEFAULT 0,
  `qst_is_enabled` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `qst_type` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `qst_text` text NOT NULL,
  PRIMARY KEY (`qst_id`),
  KEY `qst_test_id` (`qst_test_id`)
) ENGINE=MyISAM AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `questions`
--

LOCK TABLES `questions` WRITE;
/*!40000 ALTER TABLE `questions` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `questions` VALUES
(1,1,1,4,'Какие из перечисленных животных относяться к сумчатым?'),
(2,1,1,3,'Какому из перечисленных животных Джек Лондон дал прозвище &quot;белый клык&quot;?'),
(3,1,1,1,'Назовите животное, признанное священным в Индии...'),
(4,1,1,2,'Расположите числа в порядке их возрастания...'),
(5,1,1,1,'Самое высокое животное на земле сегодня'),
(6,1,1,5,'Расположите эти породы собак по их размеру начиная с самой маленькой'),
(7,1,1,3,'Отметьте лишнюю, по Вашему мнению, в этом списке змею'),
(8,1,1,4,'Какие из этих животных относятся к парнокопытным?');
/*!40000 ALTER TABLE `questions` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `results`
--

DROP TABLE IF EXISTS `results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `results` (
  `rst_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `rst_test_id` int(11) unsigned NOT NULL DEFAULT 0,
  `rst_usr_id` int(11) unsigned NOT NULL DEFAULT 0,
  `rst_start_time` int(11) unsigned NOT NULL DEFAULT 0,
  `rst_stop_time` int(11) unsigned NOT NULL DEFAULT 0,
  `rst_time_spent` int(11) unsigned NOT NULL DEFAULT 0,
  `rst_is_time_exceeded` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `rst_points` float unsigned NOT NULL DEFAULT 0,
  `rst_mark` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`rst_id`),
  KEY `rst_test_id` (`rst_test_id`),
  KEY `rst_usr_id` (`rst_usr_id`)
) ENGINE=MyISAM AUTO_INCREMENT=202 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `results`
--

LOCK TABLES `results` WRITE;
/*!40000 ALTER TABLE `results` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `results` VALUES
(152,1,1,1180284443,1180284486,43,0,0,1),
(157,1,1,1180813803,1180813870,67,0,88,4),
(161,1,1,1183717055,0,0,0,0,0),
(173,1,1,1189333665,1189333674,9,0,25,1),
(192,1,1,1192365867,1192365883,16,0,100,5);
/*!40000 ALTER TABLE `results` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `results_answers`
--

DROP TABLE IF EXISTS `results_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `results_answers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `rst_id` int(11) unsigned NOT NULL DEFAULT 0,
  `qst_id` int(11) unsigned NOT NULL DEFAULT 0,
  `ans_vr_order` text NOT NULL,
  `ans_correct` text NOT NULL,
  `ans_answer` text NOT NULL,
  `ans_percents` float unsigned NOT NULL DEFAULT 0,
  `ans_is_correct` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `ans_timespent` int(11) unsigned NOT NULL DEFAULT 0,
  `ans_is_time_exceeded` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `rst_id` (`rst_id`),
  KEY `qst_id` (`qst_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1195 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `results_answers`
--

LOCK TABLES `results_answers` WRITE;
/*!40000 ALTER TABLE `results_answers` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `results_answers` VALUES
(817,152,6,'5,2,3,4,1','2,3,1,5,4','4,0,0,0,0',0,0,43,0),
(829,157,8,'7,4,5,3,6,1,2','1,2,3','1,2,3',100,1,6,0),
(830,157,4,'5,2,3,4,1','3,5,1,2,4','3,5,1,2,4',100,1,11,0),
(831,157,7,'5,3,2,1,4','2','2',100,1,3,0),
(832,157,5,'1','жираф','жираф',100,1,10,0),
(833,157,1,'4,1,2,3','1,2','1,2',100,1,4,0),
(834,157,6,'3,2,1,5,4','2,1,4,3,5','2,2,4,3,5',0,0,26,0),
(835,157,3,'1','корова','корова\r\n',100,1,4,0),
(836,157,2,'4,1,2,3','1','1',100,1,2,0),
(957,173,4,'5,4,3,2,1','3,2,1,5,4','4,5,1,2,3',0,0,0,0),
(958,173,2,'1,2,4,3','3','8',0,0,0,0),
(959,173,3,'1','корова','корова',100,1,0,0),
(960,173,5,'1','жираф','жираф',100,1,0,0),
(961,173,8,'7,2,1,3,4,6,5','1,5,7','29,30,32',0,0,0,0),
(962,173,6,'5,2,3,1,4','2,3,1,4,5','2,3,5,1,4',0,0,0,0),
(963,173,1,'2,3,1,4','3,4','1,4',0,0,0,0),
(964,173,7,'5,2,3,4,1','3','23',0,0,0,0),
(1129,192,2,'4,2,1,3','1','1',100,1,2,0),
(1130,192,8,'5,3,7,1,6,4,2','1,3,6','1,3,6',100,1,1,0),
(1131,192,1,'4,2,3,1','1,4','1,4',100,1,1,0),
(1132,192,6,'1,3,2,4,5','3,2,5,1,4','3,2,5,1,4',100,1,2,0),
(1133,192,4,'2,3,5,1,4','5,1,3,4,2','5,1,3,4,2',100,1,1,0),
(1134,192,5,'1','жираф','жираф',100,1,2,0),
(1135,192,7,'2,5,4,3,1','4','4',100,1,1,0),
(1136,192,3,'1','корова','корова',100,1,1,0);
/*!40000 ALTER TABLE `results_answers` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `tests`
--

DROP TABLE IF EXISTS `tests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tests` (
  `test_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `test_is_enabled` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `test_start_time` int(11) unsigned DEFAULT 0,
  `test_stop_time` int(11) unsigned DEFAULT 0,
  `test_time` int(11) unsigned NOT NULL DEFAULT 0,
  `test_title` varchar(255) NOT NULL,
  `test_desc` text NOT NULL,
  `test_is_show_report` tinyint(1) unsigned DEFAULT 1,
  `test_qst_show_cnt` tinyint(3) unsigned NOT NULL DEFAULT 15,
  `test_is_mix_qst` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `test_is_mix_ans` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `test_is_show_answers` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `test_qst_per_page` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`test_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tests`
--

LOCK TABLES `tests` WRITE;
/*!40000 ALTER TABLE `tests` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `tests` VALUES
(1,1,1577826000,2524597200,600,'Животные','Тест с интересными вопросами о животных. В тесте поддерживаются разные типы вопросов',1,0,1,1,1,0);
/*!40000 ALTER TABLE `tests` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `usr_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `usr_login` varchar(32) NOT NULL,
  `usr_passwd` varchar(32) NOT NULL,
  `usr_firstname` varchar(64) NOT NULL,
  `usr_lastname` varchar(64) NOT NULL,
  `usr_thirdname` varchar(64) NOT NULL,
  `usr_email` varchar(255) NOT NULL,
  `usr_role` char(1) DEFAULT 'u',
  `usr_is_enabled` tinyint(1) unsigned DEFAULT 1,
  PRIMARY KEY (`usr_id`),
  UNIQUE KEY `usr_login` (`usr_login`)
) ENGINE=MyISAM AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `users` VALUES
(1,'admin','21232f297a57a5a743894a0e4a801fc3','admin','admin','admin','admin@example.com','a',1),
(2,'editor','5aee9dbd2a188839105073571bee1b1f','editor','editor','editor','editor@example.com','e',1),
(3,'student','098f6bcd4621d373cade4e832627b4f6','test','test','test','student@example.com','u',1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
commit;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2025-11-22  7:12:36
