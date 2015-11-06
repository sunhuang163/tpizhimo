/*
Navicat MySQL Data Transfer

Source Server         : localost
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : izhimo

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2015-11-06 13:32:17
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ih_att
-- ----------------------------
DROP TABLE IF EXISTS `ih_att`;
CREATE TABLE `ih_att` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `ext` varchar(10) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `atype` varchar(10) DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=117 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ih_content
-- ----------------------------
DROP TABLE IF EXISTS `ih_content`;
CREATE TABLE `ih_content` (
  `ncntid` int(10) NOT NULL AUTO_INCREMENT,
  `cpid` int(11) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  `content` text,
  `ord` int(11) DEFAULT '0',
  `ncid` int(11) DEFAULT NULL,
  `nid` int(11) DEFAULT NULL,
  `utime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `caijiurl` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`ncntid`)
) ENGINE=MyISAM AUTO_INCREMENT=6940 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ih_nchapter
-- ----------------------------
DROP TABLE IF EXISTS `ih_nchapter`;
CREATE TABLE `ih_nchapter` (
  `cpid` int(11) NOT NULL AUTO_INCREMENT,
  `nid` int(10) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `cdesc` varchar(100) DEFAULT NULL,
  `ord` int(5) DEFAULT NULL,
  `ncid` int(11) DEFAULT NULL,
  PRIMARY KEY (`cpid`)
) ENGINE=MyISAM AUTO_INCREMENT=1811 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ih_nclass
-- ----------------------------
DROP TABLE IF EXISTS `ih_nclass`;
CREATE TABLE `ih_nclass` (
  `ncid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `ncdesc` varchar(50) DEFAULT NULL,
  `logo` varchar(100) DEFAULT NULL,
  `cn` int(11) DEFAULT '0',
  `state` tinyint(1) DEFAULT '1',
  `url` varchar(50) DEFAULT NULL,
  `ord` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`ncid`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ih_ndata
-- ----------------------------
DROP TABLE IF EXISTS `ih_ndata`;
CREATE TABLE `ih_ndata` (
  `nid` int(10) NOT NULL,
  `cnday` int(11) DEFAULT NULL,
  `cnweek` int(11) DEFAULT NULL,
  `cnmonth` int(11) DEFAULT NULL,
  `cnall` int(11) DEFAULT NULL,
  `upday` int(11) DEFAULT NULL,
  `upweek` int(11) DEFAULT NULL,
  `upmonth` int(11) DEFAULT NULL,
  `upall` int(11) DEFAULT NULL,
  `dday` int(11) DEFAULT NULL,
  `dweek` int(11) DEFAULT NULL,
  `dmonth` int(11) DEFAULT NULL,
  `dall` int(11) DEFAULT NULL,
  PRIMARY KEY (`nid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ih_novel
-- ----------------------------
DROP TABLE IF EXISTS `ih_novel`;
CREATE TABLE `ih_novel` (
  `nid` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `author` varchar(30) DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  `utime` int(11) DEFAULT NULL,
  `ncid` int(11) DEFAULT NULL,
  `nstate` tinyint(1) DEFAULT '0',
  `uptxt` varchar(50) DEFAULT NULL,
  `ndesc` varchar(300) DEFAULT NULL,
  `ncomm` int(11) DEFAULT '0',
  `zimu` varchar(1) DEFAULT NULL,
  `url` varchar(50) DEFAULT NULL,
  `newurl` varchar(100) DEFAULT NULL,
  `pic` varchar(100) DEFAULT NULL,
  `caijiurl` varchar(200) DEFAULT NULL,
  `tag` enum('A','B','C','D','E','F','G','H') DEFAULT NULL,
  PRIMARY KEY (`nid`),
  UNIQUE KEY `index_url` (`url`),
  KEY `index_ncid` (`ncid`)
) ENGINE=MyISAM AUTO_INCREMENT=255 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ih_recommend
-- ----------------------------
DROP TABLE IF EXISTS `ih_recommend`;
CREATE TABLE `ih_recommend` (
  `recommend_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nid` int(11) NOT NULL,
  `rtype` varchar(10) DEFAULT NULL,
  `ncid` int(11) NOT NULL,
  `ord` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`recommend_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ih_syslog
-- ----------------------------
DROP TABLE IF EXISTS `ih_syslog`;
CREATE TABLE `ih_syslog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `said` int(10) DEFAULT NULL,
  `name` varchar(30) DEFAULT NULL,
  `ctype` varchar(10) DEFAULT NULL,
  `msg` varchar(200) DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  `ip` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_logc` (`ctype`)
) ENGINE=MyISAM AUTO_INCREMENT=154 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ih_sysnotice
-- ----------------------------
DROP TABLE IF EXISTS `ih_sysnotice`;
CREATE TABLE `ih_sysnotice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) DEFAULT NULL,
  `ntxt` varchar(1000) DEFAULT NULL,
  `etime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ih_sysuser
-- ----------------------------
DROP TABLE IF EXISTS `ih_sysuser`;
CREATE TABLE `ih_sysuser` (
  `said` int(10) NOT NULL AUTO_INCREMENT,
  `psw` varchar(100) DEFAULT NULL,
  `salt` varchar(10) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `name` varchar(30) DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  `utime` int(11) DEFAULT NULL,
  `mtime` int(11) DEFAULT NULL,
  `ip` varchar(128) DEFAULT NULL,
  `state` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`said`),
  UNIQUE KEY `index_uname` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ih_tag
-- ----------------------------
DROP TABLE IF EXISTS `ih_tag`;
CREATE TABLE `ih_tag` (
  `tid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  `tc` int(11) DEFAULT '0',
  PRIMARY KEY (`tid`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ih_tagindex
-- ----------------------------
DROP TABLE IF EXISTS `ih_tagindex`;
CREATE TABLE `ih_tagindex` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) DEFAULT NULL,
  `nid` int(11) DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=255 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ih_ubook
-- ----------------------------
DROP TABLE IF EXISTS `ih_ubook`;
CREATE TABLE `ih_ubook` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `nid` int(11) DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ih_ucomment
-- ----------------------------
DROP TABLE IF EXISTS `ih_ucomment`;
CREATE TABLE `ih_ucomment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(10) DEFAULT NULL,
  `nid` int(11) DEFAULT NULL,
  `rid` int(11) DEFAULT '0',
  `msg` varchar(100) DEFAULT NULL,
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `isok` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ih_unotify
-- ----------------------------
DROP TABLE IF EXISTS `ih_unotify`;
CREATE TABLE `ih_unotify` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(10) DEFAULT NULL,
  `ctype` varchar(10) DEFAULT NULL,
  `title` varchar(20) DEFAULT NULL,
  `txt` varchar(200) DEFAULT NULL,
  `fview` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `index_ctype` (`ctype`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ih_user
-- ----------------------------
DROP TABLE IF EXISTS `ih_user`;
CREATE TABLE `ih_user` (
  `uid` int(10) NOT NULL AUTO_INCREMENT,
  `psw` varchar(100) DEFAULT NULL,
  `salt` varchar(10) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `sign` varchar(50) DEFAULT NULL,
  `state` tinyint(2) DEFAULT '1',
  `ctime` int(11) DEFAULT NULL,
  `utime` int(11) DEFAULT NULL,
  `ip` int(11) DEFAULT NULL,
  `logo` varchar(100) DEFAULT NULL,
  `udesc` varchar(100) DEFAULT NULL,
  `cnotify` int(11) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ih_validate
-- ----------------------------
DROP TABLE IF EXISTS `ih_validate`;
CREATE TABLE `ih_validate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(10) DEFAULT NULL,
  `type` varchar(5) DEFAULT NULL,
  `salt` varchar(10) DEFAULT NULL,
  `data` varchar(20) DEFAULT NULL,
  `txt` varchar(500) DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ih_webgb
-- ----------------------------
DROP TABLE IF EXISTS `ih_webgb`;
CREATE TABLE `ih_webgb` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rid` int(11) DEFAULT '0',
  `name` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `msg` varchar(500) DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  `ip` varchar(128) DEFAULT NULL,
  `isok` tinyint(1) DEFAULT '0',
  `rpmsg` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ih_webinfo
-- ----------------------------
DROP TABLE IF EXISTS `ih_webinfo`;
CREATE TABLE `ih_webinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seotitle` varchar(50) DEFAULT NULL,
  `seokey` varchar(100) DEFAULT NULL,
  `seodesc` varchar(100) DEFAULT NULL,
  `contact` varchar(200) DEFAULT NULL,
  `copyright` varchar(200) DEFAULT NULL,
  `extdata` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `ih_sysuser` VALUES ('1', '22c304aa5058f9fb116b70d7e33f3d8a', '8732', 'admin@bizz2.info', 'sh001', '1416711720', '1446513491', '1416711720', '::1', '1');

INSERT INTO `ih_nclass` VALUES ('1', '默认', '默认分类			   ', '', '0', '1', 'none', '10');
INSERT INTO `ih_nclass` VALUES ('3', '玄幻', '玄幻奇幻小说', '', '0', '1', 'xuanhuanqihuan', '0');
INSERT INTO `ih_nclass` VALUES ('4', '都市', '都市小说', '', '0', '1', 'dushi', '1');
INSERT INTO `ih_nclass` VALUES ('5', '言情', '女生言情', '', '0', '1', 'yanqing', '3');
INSERT INTO `ih_nclass` VALUES ('6', '武侠', '武侠修真', '', '0', '1', 'wuxia', '2');
INSERT INTO `ih_nclass` VALUES ('7', '文学', '文学名著', '', '0', '1', 'wenxue', '8');
INSERT INTO `ih_nclass` VALUES ('8', '科幻', '科幻小说', '', '0', '1', 'kehuan', '6');
INSERT INTO `ih_nclass` VALUES ('9', '恐怖', '恐怖小说', '', '0', '1', 'kongbu', '5');
INSERT INTO `ih_nclass` VALUES ('10', '历史', '历史军事', '', '0', '1', 'lishi', '4');
INSERT INTO `ih_nclass` VALUES ('12', '经管', '经管励志', '', '0', '1', 'lizhi', '7');
INSERT INTO `ih_nclass` VALUES ('13', '法律', '法律教育', '', '0', '1', 'falv', '9');