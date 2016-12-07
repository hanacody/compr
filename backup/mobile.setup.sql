-- MySQL dump 10.13  Distrib 5.1.60, for unknown-linux-gnu (x86_64)
--
-- Host: localhost    Database: new_compr
-- ------------------------------------------------------
-- Server version	5.1.60

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES euckr */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `rankup_mobile_frame`
--

DROP TABLE IF EXISTS `rankup_mobile_frame`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rankup_mobile_frame` (
  `no` int(11) NOT NULL AUTO_INCREMENT,
  `base_name` varchar(30) NOT NULL DEFAULT '',
  `bundle` int(11) NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL DEFAULT '0',
  `depth` int(11) NOT NULL DEFAULT '0',
  `parents` varchar(255) NOT NULL DEFAULT '',
  `has_child` enum('yes','no') NOT NULL DEFAULT 'no',
  `target` enum('_self','_blank') NOT NULL DEFAULT '_self',
  `access_level` tinyint(4) NOT NULL DEFAULT '7',
  `page_type` enum('ready','html','module','link') NOT NULL DEFAULT 'ready',
  `module` varchar(20) DEFAULT NULL,
  `component` varchar(20) DEFAULT NULL,
  `options` text NOT NULL,
  `link` varchar(20) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `page_body_content` mediumtext,
  `used` enum('yes','no') NOT NULL DEFAULT 'yes',
  `use_gnb` enum('yes','no') DEFAULT NULL,
  PRIMARY KEY (`no`),
  KEY `bundle` (`bundle`,`position`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=euckr;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rankup_mobile_frame`
--

LOCK TABLES `rankup_mobile_frame` WRITE;
/*!40000 ALTER TABLE `rankup_mobile_frame` DISABLE KEYS */;
INSERT INTO `rankup_mobile_frame` VALUES (1,'회사소개',1,0,1,'','yes','_self',7,'link','','','','13','','<P align=center>&nbsp;</P>','yes','yes'),(12,'전화걸기',9,0,1,'','no','_self',7,'link','etc','login','','','tel:1544-6862','','yes','no'),(2,'사업소개',2,0,1,'','yes','_self',7,'link','','','','19','','','yes','yes'),(3,'고객센터',5,0,1,'','yes','_self',7,'link','','','','27','','','yes','yes'),(4,'제품소개',3,0,1,'','yes','_self',7,'link','board','','','23','','','yes','yes'),(5,'커뮤니티',6,0,1,'','yes','_self',7,'link','gallery','','','31','','','yes','yes'),(7,'채용정보',4,0,1,'','yes','_self',7,'link','','','','9','','','yes','yes'),(9,'채용안내',4,1,2,'7','no','_self',7,'html','','','','',NULL,'<P align=center><IMG src=\"/wysiwyg/PEG/W_1331191270.png\"></P>','yes',NULL),(10,'채용공고',4,2,2,'7','no','_self',7,'module','board','jobopening','','',NULL,'','yes',NULL),(11,'온라인입사지원',4,3,2,'7','no','_self',7,'module','fbuilder','6','','',NULL,'','yes',NULL),(13,'CEO인사말',1,1,2,'1','no','_self',7,'html','','','','',NULL,'<P align=center><IMG src=\"/wysiwyg/PEG/W_1331187777.png\"></P>','yes',NULL),(14,'회사연혁',1,2,2,'1','no','_self',7,'html','','','','',NULL,'<P align=center><IMG src=\"/wysiwyg/PEG/W_1331188552.png\"></P>','yes',NULL),(15,'조직도',1,3,2,'1','no','_self',7,'html','','','','',NULL,'<P align=center><IMG src=\"/wysiwyg/PEG/W_1331189846.png\"></P>','yes',NULL),(16,'회사전경',1,4,2,'1','no','_self',7,'module','gallery','7','','',NULL,'','yes',NULL),(17,'주요일정안내',1,5,2,'1','no','_self',7,'module','schedule','2','','',NULL,'','yes',NULL),(18,'오시는길',1,6,2,'1','no','_self',7,'module','etc','map','','',NULL,'','yes',NULL),(19,'사업소개1',2,1,2,'2','no','_self',7,'module','gallery','5','','',NULL,'','yes',NULL),(20,'사업소개2',2,2,2,'2','no','_self',7,'module','gallery','4','','',NULL,'','yes',NULL),(21,'사업소개3',2,3,2,'2','no','_self',7,'module','gallery','3','','',NULL,'','yes',NULL),(22,'사업소개4',2,4,2,'2','no','_self',7,'module','gallery','2','','',NULL,'','yes',NULL),(23,'컴퓨터/전자',3,1,2,'4','no','_self',7,'module','product','list_normal','{\"cate1\": \"53\", \"cate2\": \"\"}','',NULL,'','yes',NULL),(24,'노트북',3,2,2,'4','no','_self',7,'module','product','list_normal','{\"cate1\": \"53\", \"cate2\": \"80\"}','',NULL,'','yes',NULL),(25,'키보드',3,3,2,'4','no','_self',7,'module','product','list_normal','{\"cate1\": \"53\", \"cate2\": \"81\"}','',NULL,'','yes',NULL),(26,'모니터',3,4,2,'4','no','_self',7,'module','product','list_normal','{\"cate1\": \"53\", \"cate2\": \"82\"}','',NULL,'','yes',NULL),(27,'공지사항',5,1,2,'3','no','_self',7,'module','board','notice','','',NULL,'','yes',NULL),(28,'견적문의',5,2,2,'3','no','_self',7,'ready','','','','','','','yes',NULL),(29,'자주하는질문',5,3,2,'3','no','_self',7,'module','board','faqboard','','',NULL,'','yes',NULL),(30,'온라인문의',5,4,2,'3','no','_self',7,'ready','','','','','','','yes',NULL),(31,'업계소식',6,1,2,'5','no','_self',7,'module','board','notice','','',NULL,'','yes',NULL),(32,'보도자료',6,2,2,'5','no','_self',7,'module','board','news','','',NULL,'','yes',NULL),(33,'시공사진',6,3,2,'5','no','_self',7,'module','board','gallery1','','',NULL,'','yes',NULL),(34,'자유게시판',6,4,2,'5','no','_self',7,'module','board','board1','','',NULL,'','yes',NULL);
/*!40000 ALTER TABLE `rankup_mobile_frame` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rankup_mobile_config`
--

DROP TABLE IF EXISTS `rankup_mobile_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rankup_mobile_config` (
  `item` varchar(20) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  PRIMARY KEY (`item`)
) ENGINE=MyISAM DEFAULT CHARSET=euckr;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rankup_mobile_config`
--

LOCK TABLES `rankup_mobile_config` WRITE;
/*!40000 ALTER TABLE `rankup_mobile_config` DISABLE KEYS */;
INSERT INTO `rankup_mobile_config` VALUES ('sitename','기업/제품홍보솔루션 - 모바일웹'),('copyright','<P align=center>Copyrightⓒ2001-2012 RANKUP Co. Ltd., All rights reserved.</P>'),('settings','a:19:{s:10:\"mobile_use\";s:3:\"yes\";s:14:\"membership_use\";s:3:\"yes\";s:11:\"domain_kind\";s:5:\"basic\";s:6:\"domain\";s:0:\"\";s:5:\"phone\";s:9:\"1544-6862\";s:4:\"logo\";s:23:\"logo.13315396130599.png\";s:9:\"intro_use\";s:3:\"yes\";s:8:\"intro_bg\";s:25:\"mintro.13189216237856.jpg\";s:4:\"skin\";s:1:\"5\";s:9:\"pc_domain\";s:26:\"http://compr.rankup.co.kr/\";s:6:\"layout\";s:1:\"a\";s:7:\"bg_type\";s:5:\"color\";s:12:\"site_bg_type\";s:5:\"solid\";s:14:\"site_bg_scolor\";s:7:\"#E7EEF3\";s:13:\"site_bg_gtype\";s:6:\"height\";s:15:\"site_bg_gcolor1\";s:7:\"#4E3737\";s:15:\"site_bg_gcolor2\";s:7:\"#D6D899\";s:12:\"site_bg_skin\";s:1:\"4\";s:7:\"site_bg\";s:22:\"sbg.13203079855899.gif\";}'),('ready_content','<P align=center><IMG src=\"/wysiwyg/PEG/W_1320661144.png\"></P>'),('main_content','<P align=center>&nbsp;</P>\r\n<P align=center>&nbsp;</P>\r\n<P align=center>&nbsp;</P>\r\n<P align=center>&nbsp;</P>\r\n<P align=center>&nbsp;</P>\r\n<P align=center>&nbsp;</P>\r\n<P align=center>직접 디자인한 내용을 입력해 주세요.</P>\r\n<P align=center>&nbsp;</P>\r\n<P align=center>&nbsp;</P>\r\n<P align=center>&nbsp;</P>\r\n<P align=center>&nbsp;</P>\r\n<P align=center>&nbsp;</P>'),('main_design','a:17:{s:8:\"main_use\";s:3:\"yes\";s:7:\"bg_type\";s:6:\"upload\";s:12:\"main_bg_type\";s:8:\"gradient\";s:14:\"main_bg_scolor\";s:7:\"#D4D4D4\";s:13:\"main_bg_gtype\";s:6:\"height\";s:15:\"main_bg_gcolor1\";s:7:\"#A2A9F0\";s:15:\"main_bg_gcolor2\";s:7:\"#F2C9C9\";s:11:\"design_type\";s:5:\"basic\";s:8:\"icon_qty\";s:1:\"3\";s:14:\"vertical_align\";s:3:\"top\";s:12:\"col_interval\";s:2:\"10\";s:12:\"row_interval\";s:2:\"18\";s:9:\"icon_type\";s:4:\"both\";s:10:\"quick_pids\";s:11:\"1,2,4,7,3,5\";s:11:\"quick_icons\";a:8:{i:1;s:24:\"quick.13311882425225.png\";i:2;s:24:\"quick.13311882618935.png\";i:4;s:24:\"quick.13311882720659.png\";i:7;s:24:\"quick.13311882895515.png\";i:3;s:24:\"quick.13311882895516.png\";i:5;s:24:\"quick.13311882895517.png\";i:6;s:24:\"quick.13207294239439.png\";i:12;s:24:\"quick.13207294239441.png\";}s:7:\"main_bg\";s:22:\"mbg.13311883065645.png\";s:12:\"main_bg_skin\";s:1:\"1\";}'),('site_design','a:18:{s:7:\"bg_type\";s:4:\"none\";s:12:\"site_bg_type\";s:5:\"solid\";s:14:\"site_bg_scolor\";s:7:\"#FFFFFF\";s:13:\"site_bg_gtype\";s:6:\"height\";s:15:\"site_bg_gcolor1\";s:7:\"#EFF6F9\";s:15:\"site_bg_gcolor2\";s:7:\"#D4E1E6\";s:11:\"nav_bgcolor\";s:7:\"#104A95\";s:11:\"frame_color\";s:7:\"#092952\";s:11:\"menu_height\";s:2:\"30\";s:8:\"menu_qty\";s:1:\"3\";s:9:\"menu_type\";s:4:\"text\";s:16:\"menu_off_bgcolor\";s:7:\"#296CCC\";s:15:\"menu_on_bgcolor\";s:7:\"#296CCC\";s:14:\"menu_off_color\";s:7:\"#FFFFFF\";s:13:\"menu_on_color\";s:7:\"#FFF100\";s:12:\"site_bg_skin\";s:1:\"2\";s:9:\"gnb_texts\";a:2:{i:1;s:22:\"gnb.13207299963488.png\";i:2;s:22:\"gnb.13207299963490.png\";}s:7:\"site_bg\";s:22:\"sbg.13208009970624.jpg\";}');
/*!40000 ALTER TABLE `rankup_mobile_config` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-03-13 13:54:35
