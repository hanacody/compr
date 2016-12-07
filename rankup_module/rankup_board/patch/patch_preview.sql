--
-- @ 2009.08.31 patch
--
/* 미리보기 용 게시판 샘플 */
INSERT INTO `rankup_board_config` VALUES (0,'_sample_','미리보기 샘플게시판입니다.',0,0,'basic/gray','normal',1,0,'yes','no','no','a:5:{s:14:\"subject_length\";i:40;s:12:\"article_rows\";i:5;s:11:\"image_width\";s:0:\"\";s:12:\"image_height\";s:0:\"\";s:11:\"print_style\";s:4:\"text\";}','a:5:{s:14:\"subject_length\";i:40;s:12:\"article_rows\";i:5;s:11:\"image_width\";s:0:\"\";s:12:\"image_height\";s:0:\"\";s:11:\"print_style\";s:4:\"text\";}','a:4:{s:11:\"board_width\";s:3:\"725\";s:14:\"subject_length\";s:2:\"40\";s:12:\"use_condense\";s:2:\"on\";s:12:\"article_rows\";s:2:\"15\";}','a:8:{s:12:\"use_category\";N;s:17:\"use_duplicate_hit\";N;s:11:\"use_comment\";s:2:\"on\";s:9:\"use_reply\";N;s:8:\"use_vote\";N;s:13:\"use_only_good\";N;s:10:\"use_report\";s:2:\"on\";s:10:\"use_secret\";N;}','a:7:{s:12:\"use_hit_best\";N;s:12:\"use_new_icon\";s:2:\"on\";s:11:\"recent_time\";s:2:\"24\";s:15:\"use_attach_icon\";N;s:14:\"use_reply_icon\";N;s:16:\"use_near_article\";s:2:\"on\";s:15:\"use_detail_list\";s:2:\"on\";}','a:0:{}','a:8:{s:10:\"list_level\";i:7;s:10:\"read_level\";i:7;s:11:\"write_level\";i:6;s:13:\"comment_level\";i:6;s:11:\"reply_level\";i:6;s:12:\"delete_level\";i:1;s:12:\"notice_level\";i:1;s:12:\"secret_level\";i:1;}','a:0:{}','a:5:{s:10:\"use_attach\";s:2:\"on\";s:17:\"use_detail_attach\";N;s:11:\"attach_nums\";s:1:\"4\";s:11:\"attach_size\";s:4:\"2048\";s:16:\"attach_extension\";s:51:\"gif,png,jpg,bmp,swf,hwp,doc,pdf,ppt,xsl,gul,zip,alz\";}','a:4:{s:11:\"thumb_width\";s:3:\"120\";s:12:\"thumb_height\";s:3:\"120\";s:13:\"picture_width\";s:3:\"685\";s:10:\"thumb_nums\";s:1:\"5\";}','','','');
UPDATE `rankup_board_config` SET `no` = '0' WHERE `id` ='_sample_';
INSERT `rankup_board_division` SET `bid`='_sample_', `division`='1', `banum` = '1';

--
-- Table structure for table `rankup_board__sample_`
--

DROP TABLE IF EXISTS `rankup_board__sample_`;
CREATE TABLE `rankup_board__sample_` (
  `no` int(20) unsigned NOT NULL auto_increment,
  `dno` int(10) NOT NULL default '1',
  `cno` int(10) default NULL,
  `sno` int(20) NOT NULL default '0',
  `gno` int(10) NOT NULL default '0',
  `pno` int(10) unsigned NOT NULL default '0',
  `nano` int(20) NOT NULL default '0',
  `pano` int(20) NOT NULL default '0',
  `uip` varchar(15) default NULL,
  `uid` varchar(20) default NULL,
  `unick` varchar(40) NOT NULL default '',
  `upass` varchar(20) default NULL,
  `subject` varchar(250) NOT NULL default '',
  `content` text,
  `attach` text,
  `voter` text,
  `wdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `mdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `sval` enum('yes','no') NOT NULL default 'no',
  `nval` enum('yes','no') NOT NULL default 'no',
  `dval` enum('yes','no') NOT NULL default 'no',
  `cnum` int(11) unsigned NOT NULL default '0',
  `dnum` int(11) unsigned NOT NULL default '0',
  `gnum` int(11) unsigned NOT NULL default '0',
  `bnum` int(11) unsigned NOT NULL default '0',
  `hnum` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`no`),
  KEY `dno` (`dno`,`sno`,`gno`),
  KEY `pno` (`pno`),
  KEY `cno` (`cno`),
  KEY `nano` (`nano`),
  KEY `pano` (`pano`),
  KEY `unick` (`unick`),
  KEY `wdate` (`wdate`),
  KEY `cnum` (`cnum`),
  KEY `hnum` (`hnum`),
  KEY `gnum` (`gnum`),
  KEY `bnum` (`bnum`),
  KEY `dnum` (`dnum`)
) TYPE=MyISAM;

--
-- Dumping data for table `rankup_board__sample_`
--

LOCK TABLES `rankup_board__sample_` WRITE;
/*!40000 ALTER TABLE `rankup_board__sample_` DISABLE KEYS */;
INSERT INTO `rankup_board__sample_` VALUES (1,1,NULL,-1,0,0,4,0,'58.72.123.42','rankup','랭크업',NULL,'미리보기 샘플 게시판입니다. 스킨형태만 참조하세요.','미리보기 샘플 게시판입니다. 스킨형태만 참조하세요.<BR><BR><BR><IMG src=\"/rankup_module/rankup_board/attach/_sample_/12532552112646.gif\" width=100 height=100>','a:1:{i:0;a:3:{s:5:\"oname\";s:6:\"12.gif\";s:5:\"sname\";s:18:\"12532552112646.gif\";s:4:\"dnum\";i:0;}}',NULL,'2009-09-18 15:23:57','2009-09-18 15:26:56','no','no','no',0,0,0,0,0);
/*!40000 ALTER TABLE `rankup_board__sample_` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rankup_board_comment__sample_`
--

DROP TABLE IF EXISTS `rankup_board_comment__sample_`;
CREATE TABLE `rankup_board_comment__sample_` (
  `no` int(20) unsigned NOT NULL auto_increment,
  `ano` int(20) NOT NULL default '0',
  `uip` varchar(15) default NULL,
  `uid` varchar(20) default NULL,
  `unick` varchar(40) default NULL,
  `upasswd` varchar(20) default NULL,
  `icon` tinyint(3) unsigned default NULL,
  `content` text,
  `wdate` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`no`),
  KEY `ano` (`ano`)
) TYPE=MyISAM;
