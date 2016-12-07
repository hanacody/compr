--
-- 테이블 구조 `rankup_analytics_config`
--

DROP TABLE IF EXISTS `rankup_analytics_config`;
CREATE TABLE `rankup_analytics_config` (
  `google_id` varchar(50) NOT NULL default '',
  `google_pass` varchar(50) NOT NULL default '',
  `google_profile_id` varchar(50) NOT NULL default '',
  `google_scripts` text NOT NULL
) TYPE=MyISAM;

--
-- 테이블의 덤프 데이터 `rankup_analytics_config`
--

INSERT INTO `rankup_analytics_config` (`google_id`, `google_pass`, `google_profile_id`, `google_scripts`) VALUES
('gorankup@gmail.com', 'rankup2011', '51296083', '<script type="text/javascript">\r\n\r\n  var _gaq = _gaq || [];\r\n  _gaq.push([''_setAccount'', ''UA-26155224-1'']);\r\n  _gaq.push([''_setDomainName'', ''.'']);\r\n  _gaq.push([''_trackPageview'']);\r\n\r\n  (function() {\r\n    var ga = document.createElement(''script''); ga.type = ''text/javascript''; ga.async = true;\r\n    ga.src = (''https:'' == document.location.protocol ? ''https://ssl'' : ''http://www'') + ''.google-analytics.com/ga.js'';\r\n    var s = document.getElementsByTagName(''script'')[0]; s.parentNode.insertBefore(ga, s);\r\n  })();\r\n\r\n</script>');


--
-- 테이블 구조 `rankup_analytics`
--

DROP TABLE IF EXISTS `rankup_analytics`;
CREATE TABLE `rankup_analytics` (
  `no` int(11) NOT NULL auto_increment,
  `kind` varchar(50) NOT NULL default '',
  `sdate` date NOT NULL default '0000-00-00',
  `edate` date NOT NULL default '0000-00-00',
  `titles` mediumtext NOT NULL,
  `datas` mediumtext NOT NULL,
  `max` int(11) NOT NULL default '0',
  `regist_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `update_time` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`no`),
  KEY `kind` (`kind`,`sdate`,`edate`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;
