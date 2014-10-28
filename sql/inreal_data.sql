-- phpMyAdmin SQL Dump
-- version 4.1.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2014 年 10 月 28 日 07:20
-- サーバのバージョン： 5.6.19
-- PHP Version: 5.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `inreal_data`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `motion_histories`
--

CREATE TABLE IF NOT EXISTS `motion_histories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `dir_name` varchar(255) NOT NULL COMMENT 'ディレクトリ名',
  `file_name` varchar(255) NOT NULL COMMENT 'ファイル名',
  `subject` varchar(255) NOT NULL COMMENT 'ねらい',
  `grade_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学部ID',
  `person_name` varchar(255) NOT NULL COMMENT '名前',
  `pose_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '姿勢ID',
  `record_date` date NOT NULL COMMENT '記録日',
  `exposure_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '撮影時間',
  `speak_remark` text NOT NULL COMMENT '教師の言葉かけ',
  `part_name_1` varchar(255) NOT NULL COMMENT '体の部分1',
  `motion_level_1` int(11) NOT NULL DEFAULT '0' COMMENT '動きの程度1',
  `part_name_2` varchar(255) NOT NULL COMMENT '体の部分2',
  `motion_level_2` int(11) NOT NULL DEFAULT '0' COMMENT '動きの程度2',
  `part_name_3` varchar(255) NOT NULL COMMENT '体の部分3',
  `motion_level_3` int(11) NOT NULL DEFAULT '0' COMMENT '動きの程度3',
  `part_name_4` varchar(255) NOT NULL COMMENT '体の部分4',
  `motion_level_4` int(11) NOT NULL DEFAULT '0' COMMENT '動きの程度4',
  `motion_remark` text NOT NULL COMMENT '身体の動きの様子',
  `motion_type_id` int(11) NOT NULL COMMENT '1.無意識行動;2.意図的行動',
  `created` datetime NOT NULL COMMENT '登録日時',
  `modified` datetime NOT NULL COMMENT '更新日時',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
