--
-- Table structure for table `rankuplog_admin`
--

DROP TABLE IF EXISTS `rankuplog_admin`;
CREATE TABLE `rankuplog_admin` (
  `uid` varchar(30) default NULL,
  `upasswd` varchar(50) default NULL
) TYPE=MyISAM;

--
-- Table structure for table `rankuplog_date`
--

DROP TABLE IF EXISTS `rankuplog_date`;
CREATE TABLE `rankuplog_date` (
  `no` int(11) NOT NULL auto_increment,
  `wdate` date default NULL,
  `date1` int(11) default NULL,
  `date2` int(11) default NULL,
  `date3` int(11) default NULL,
  `date4` int(11) default NULL,
  `date5` int(11) default NULL,
  `date6` int(11) default NULL,
  `date7` int(11) default NULL,
  `date8` int(11) default NULL,
  `date9` int(11) default NULL,
  `date10` int(11) default NULL,
  `date11` int(11) default NULL,
  `date12` int(11) default NULL,
  `date13` int(11) default NULL,
  `date14` int(11) default NULL,
  `date15` int(11) default NULL,
  `date16` int(11) default NULL,
  `date17` int(11) default NULL,
  `date18` int(11) default NULL,
  `date19` int(11) default NULL,
  `date20` int(11) default NULL,
  `date21` int(11) default NULL,
  `date22` int(11) default NULL,
  `date23` int(11) default NULL,
  `date24` int(11) default NULL,
  `date25` int(11) default NULL,
  `date26` int(11) default NULL,
  `date27` int(11) default NULL,
  `date28` int(11) default NULL,
  `date29` int(11) default NULL,
  `date30` int(11) default NULL,
  `date31` int(11) default NULL,
  PRIMARY KEY  (`no`)
) TYPE=MyISAM;

--
-- Table structure for table `rankuplog_domain`
--

DROP TABLE IF EXISTS `rankuplog_domain`;
CREATE TABLE `rankuplog_domain` (
  `no` int(11) NOT NULL auto_increment,
  `wdate` date default NULL,
  `content` tinytext,
  `num` int(11) default NULL,
  PRIMARY KEY  (`no`)
) TYPE=MyISAM;

--
-- Table structure for table `rankuplog_ip`
--

DROP TABLE IF EXISTS `rankuplog_ip`;
CREATE TABLE `rankuplog_ip` (
  `no` int(11) NOT NULL auto_increment,
  `wdate` date default NULL,
  `content` varchar(50) default NULL,
  `num` int(11) default NULL,
  PRIMARY KEY  (`no`)
) TYPE=MyISAM;

--
-- Table structure for table `rankuplog_month`
--

DROP TABLE IF EXISTS `rankuplog_month`;
CREATE TABLE `rankuplog_month` (
  `no` int(11) NOT NULL auto_increment,
  `wdate` date default NULL,
  `month01` int(11) default NULL,
  `month02` int(11) default NULL,
  `month03` int(11) default NULL,
  `month04` int(11) default NULL,
  `month05` int(11) default NULL,
  `month06` int(11) default NULL,
  `month07` int(11) default NULL,
  `month08` int(11) default NULL,
  `month09` int(11) default NULL,
  `month10` int(11) default NULL,
  `month11` int(11) default NULL,
  `month12` int(11) default NULL,
  PRIMARY KEY  (`no`)
) TYPE=MyISAM;

--
-- Table structure for table `rankuplog_shortdomain`
--

DROP TABLE IF EXISTS `rankuplog_shortdomain`;
CREATE TABLE `rankuplog_shortdomain` (
  `no` int(11) NOT NULL auto_increment,
  `wdate` date default NULL,
  `content` tinytext,
  `num` int(11) default NULL,
  PRIMARY KEY  (`no`)
) TYPE=MyISAM;

--
-- Table structure for table `rankuplog_time`
--

DROP TABLE IF EXISTS `rankuplog_time`;
CREATE TABLE `rankuplog_time` (
  `no` int(11) NOT NULL auto_increment,
  `wdate` date default NULL,
  `time1` int(11) default NULL,
  `time2` int(11) default NULL,
  `time3` int(11) default NULL,
  `time4` int(11) default NULL,
  `time5` int(11) default NULL,
  `time6` int(11) default NULL,
  `time7` int(11) default NULL,
  `time8` int(11) default NULL,
  `time9` int(11) default NULL,
  `time10` int(11) default NULL,
  `time11` int(11) default NULL,
  `time12` int(11) default NULL,
  `time13` int(11) default NULL,
  `time14` int(11) default NULL,
  `time15` int(11) default NULL,
  `time16` int(11) default NULL,
  `time17` int(11) default NULL,
  `time18` int(11) default NULL,
  `time19` int(11) default NULL,
  `time20` int(11) default NULL,
  `time21` int(11) default NULL,
  `time22` int(11) default NULL,
  `time23` int(11) default NULL,
  `time0` int(11) default NULL,
  PRIMARY KEY  (`no`)
) TYPE=MyISAM;


--
-- Table structure for table `rankuplog_total`
--

DROP TABLE IF EXISTS `rankuplog_total`;
CREATE TABLE `rankuplog_total` (
  `no` int(11) NOT NULL auto_increment,
  `wdate` date default NULL,
  `num` int(11) default NULL,
  PRIMARY KEY  (`no`)
) TYPE=MyISAM;

--
-- Table structure for table `rankuplog_totaltoday`
--

DROP TABLE IF EXISTS `rankuplog_totaltoday`;
CREATE TABLE `rankuplog_totaltoday` (
  `no` int(11) NOT NULL auto_increment,
  `todaydate` date default NULL,
  PRIMARY KEY  (`no`)
) TYPE=MyISAM;

--
-- Table structure for table `rankuplog_week`
--

DROP TABLE IF EXISTS `rankuplog_week`;
CREATE TABLE `rankuplog_week` (
  `no` int(11) NOT NULL auto_increment,
  `wdate` date default NULL,
  `date0` int(11) default NULL,
  `date1` int(11) default NULL,
  `date2` int(11) default NULL,
  `date3` int(11) default NULL,
  `date4` int(11) default NULL,
  `date5` int(11) default NULL,
  `date6` int(11) default NULL,
  PRIMARY KEY  (`no`)
) TYPE=MyISAM;
