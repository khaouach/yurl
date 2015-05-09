-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Machine: localhost
-- Genereertijd: 10 mrt 2015 om 17:05
-- Serverversie: 5.5.41-0ubuntu0.14.04.1
-- PHP-versie: 5.5.9-1ubuntu4.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Databank: `healthcheck_dev`
--
CREATE DATABASE IF NOT EXISTS `healthcheck_dev` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `healthcheck_dev`;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `incidents`
--

DROP TABLE IF EXISTS `incidents`;
CREATE TABLE IF NOT EXISTS `incidents` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `site_id` int(6) NOT NULL,
  `call_im` varchar(16) NOT NULL,
  `timestamp` datetime NOT NULL,
  `by` varchar(60) NOT NULL,
  `remarks` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `runs`
--

DROP TABLE IF EXISTS `runs`;
CREATE TABLE IF NOT EXISTS `runs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=160363 ;

-- --------------------------------------------------------

--
-- Stand-in structuur voor view `run_statuses`
--
DROP VIEW IF EXISTS `run_statuses`;
CREATE TABLE IF NOT EXISTS `run_statuses` (
`timestamp` datetime
,`id` int(6)
,`run_id` int(6)
,`site_id` int(6)
,`raw` text
,`status` varchar(16)
);
-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `sites`
--

DROP TABLE IF EXISTS `sites`;
CREATE TABLE IF NOT EXISTS `sites` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `application` varchar(30) NOT NULL,
  `type` varchar(16) NOT NULL,
  `environment` varchar(16) NOT NULL,
  `url` text NOT NULL,
  `url_site` text NOT NULL,
  `remarks` text NOT NULL,
  `javascript` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=92 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `statuses`
--

DROP TABLE IF EXISTS `statuses`;
CREATE TABLE IF NOT EXISTS `statuses` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `http_code` varchar(12) NOT NULL,
  `run_id` int(6) NOT NULL,
  `site_id` int(6) NOT NULL,
  `raw` text NOT NULL,
  `status` varchar(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8274 ;

-- --------------------------------------------------------

--
-- Structuur voor de view `run_statuses`
--
DROP TABLE IF EXISTS `run_statuses`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `run_statuses` AS (select `r`.`timestamp` AS `timestamp`,`s`.`id` AS `id`,`s`.`run_id` AS `run_id`,`s`.`site_id` AS `site_id`,`s`.`raw` AS `raw`,`s`.`status` AS `status` from (`runs` `r` join `statuses` `s` on((`r`.`id` = `s`.`run_id`))));


-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Machine: localhost
-- Genereertijd: 10 mrt 2015 om 17:06
-- Serverversie: 5.5.41-0ubuntu0.14.04.1
-- PHP-versie: 5.5.9-1ubuntu4.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Databank: `healthcheck_dev`
--

--
-- Gegevens worden uitgevoerd voor tabel `sites`
--

INSERT INTO `sites` (`id`, `application`, `type`, `environment`, `url`, `url_site`, `remarks`, `javascript`) VALUES
(2, 'Storyboard', 'GUI', 'ST', 'http://10.49.23.197/st_test/index.php?option=com_healthcheck', 'http://10.49.23.197/st_test/', '', 0),
(3, 'Storyboard', 'GUI', 'PROD', 'https://storyboard.abnamro.nl/index.php?option=com_healthcheck', 'https://storyboard.abnamro.nl/index.php', '', 1),
(4, 'Alerting', 'GUI', 'UT', 'http://n05ast0029-c06.nl.eu.abnamro.com:14300/alerting/healthCheck?extra=1', 'http://n05ast0029-c06.nl.eu.abnamro.com:14300/alerting/index.htm', '', 0),
(5, 'Alerting', 'Engine', 'UT', 'http://n05ast0029-c06.nl.eu.abnamro.com:14300/alerting-engine/healthCheck?extra=1', '', '', 0),
(6, 'Alerting', 'Relation', 'UT', 'http://n05ast0029-c06.nl.eu.abnamro.com:14300/alerting-relation/healthCheck?extra=1', '', '', 0),
(7, 'Alerting', 'Media', 'UT', 'http://n05ast0029-c06.nl.eu.abnamro.com:14300/alerting-media/healthCheck?extra=1', '', '', 0),
(8, 'Alerting', 'Archive', 'UT', 'http://n05ast0029-c06.nl.eu.abnamro.com:14300/alerting-archive/healthCheck', '', 'alex: extra=1 is not working for this this project', 0),
(9, 'Alerting', 'GUI', 'ST', 'http://s05ast0026-c06.nl.eu.abnamro.com:14300/alerting/healthCheck', 'http://s05ast0026-c06.nl.eu.abnamro.com:14300/alerting/index.htm', '', 0),
(10, 'Alerting', 'Engine', 'ST', 'http://s05ast0026-c06.nl.eu.abnamro.com:14300/alerting-engine/healthCheck', '', '', 0),
(11, 'Alerting', 'Relation', 'ST', 'http://s05ast0026-c06.nl.eu.abnamro.com:14300/alerting-relation/healthCheck', '', '', 0),
(12, 'Alerting', 'Media', 'ST', 'http://s05ast0026-c06.nl.eu.abnamro.com:14300/alerting-media/healthCheck', '', '', 0),
(13, 'Alerting', 'Archive', 'ST', 'http://s05ast0026-c06.nl.eu.abnamro.com:14300/alerting-archive/healthCheck', '', '', 0),
(14, 'Alerting', 'GUI', 'ETs', 'http://s05aet0300-c01.nl.eu.abnamro.com:14300/alerting/healthCheck', 'http://s05aet0300-c01.nl.eu.abnamro.com:14300/alerting/index.htm', '', 0),
(15, 'Alerting', 'Engine', 'ETs', 'http://s05aet0300-c01.nl.eu.abnamro.com:14300/alerting-engine/healthCheck', '', '', 0),
(16, 'Alerting', 'Relation', 'ETs', 'http://s05aet0300-c01.nl.eu.abnamro.com:14300/alerting-relation/healthCheck', '', '', 0),
(17, 'Alerting', 'Media', 'ETs', 'http://s05aet0300-c01.nl.eu.abnamro.com:14300/alerting-media/healthCheck', '', '', 0),
(18, 'Alerting', 'Archive', 'ETs', 'http://s05aet0300-c01.nl.eu.abnamro.com:14300/alerting-archive/healthCheck', '', '', 0),
(19, 'Alerting', 'GUI', 'ETn', 'http://n05aet0300-c01.nl.eu.abnamro.com:14300/alerting/healthCheck', 'http://n05aet0300-c01.nl.eu.abnamro.com:14300/alerting/index.htm', '', 0),
(20, 'Alerting', 'Engine', 'ETn', 'http://n05aet0300-c01.nl.eu.abnamro.com:14300/alerting-engine/healthCheck', '', '', 0),
(21, 'Alerting', 'Relation', 'ETn', 'http://n05aet0300-c01.nl.eu.abnamro.com:14300/alerting-relation/healthCheck', '', '', 0),
(22, 'Alerting', 'Media', 'ETn', 'http://n05aet0300-c01.nl.eu.abnamro.com:14300/alerting-media/healthCheck', '', '', 0),
(23, 'Alerting', 'Archive', 'ETn', 'http://n05aet0300-c01.nl.eu.abnamro.com:14300/alerting-archive/healthCheck', '', '', 0),
(24, 'Alerting', 'GUI', 'PRODs', 'http://s05apr0300-c01.nl.eu.abnamro.com:14300/alerting/healthCheck', 'http://s05apr0300-c01.nl.eu.abnamro.com:14300/alerting/index.htm', '', 0),
(25, 'Alerting', 'Engine', 'PRODs', 'http://s05apr0300-c01.nl.eu.abnamro.com:14300/alerting-engine/healthCheck', '', '', 0),
(26, 'Alerting', 'Relation', 'PRODs', 'http://s05apr0300-c01.nl.eu.abnamro.com:14300/alerting-relation/healthCheck', '', '', 0),
(27, 'Alerting', 'Media', 'PRODs', 'http://s05apr0300-c01.nl.eu.abnamro.com:14300/alerting-media/healthCheck', '', '', 0),
(28, 'Alerting', 'Archive', 'PRODs', 'http://s05apr0300-c01.nl.eu.abnamro.com:14300/alerting-archive/healthCheck', '', '', 0),
(29, 'Alerting', 'GUI', 'PRODn', 'http://n05apr0300-c01.nl.eu.abnamro.com:14300/alerting/healthCheck', 'http://n05apr0300-c01.nl.eu.abnamro.com:14300/alerting/index.htm', '', 0),
(30, 'Alerting', 'Engine', 'PRODn', 'http://n05apr0300-c01.nl.eu.abnamro.com:14300/alerting-engine/healthCheck', '', '', 0),
(31, 'Alerting', 'Relation', 'PRODn', 'http://n05apr0300-c01.nl.eu.abnamro.com:14300/alerting-relation/healthCheck', '', '', 0),
(32, 'Alerting', 'Media', 'PRODn', 'http://n05apr0300-c01.nl.eu.abnamro.com:14300/alerting-media/healthCheck', '', '', 0),
(33, 'Alerting', 'Archive', 'PRODn', 'http://n05apr0300-c01.nl.eu.abnamro.com:14300/alerting-archive/healthCheck', '', '', 0),
(34, 'OWS', 'Client', 'UT', 'http://n05ast0026-c08.nl.eu.abnamro.com:12117/OW-WebServiceClient/healthCheck', '', '', 0),
(35, 'OWS', 'Server', 'UT', 'http://n05ast0026-c08.nl.eu.abnamro.com:12116/OW-WebService/healthcheck', '', '', 0),
(36, 'OWS', 'Client', 'ST', 'http://n05ast0027-c08.nl.eu.abnamro.com:14117/OW-WebServiceClient/healthCheck', '', '', 0),
(37, 'OWS', 'Server', 'ST', 'http://n05ast0027-c08.nl.eu.abnamro.com:12116/OW-WebService/healthcheck', '', '', 0),
(38, 'OWS', 'Client BE', 'ET', 'http://10.103.52.94:9080/OW-WebServiceClient/healthCheck', '', '', 0),
(39, 'OWS', 'Server BE', 'ET', 'http://10.103.52.94:9080/OW-WebService/healthcheck', '', '', 0),
(40, 'OWS', 'Client SG', 'ET', 'https://10.103.52.96:9443/OW-WebServiceClient/healthCheck', '', '', 0),
(41, 'OWS', 'Server SG', 'ET', 'https://10.103.52.96:9443/OW-WebService/healthcheck', '', '', 0),
(42, 'OWS', 'Client NL', 'ETn', 'http://n05aet0024-c01.nl.eu.abnamro.com:14117/OW-WebServiceClient/healthCheck', '', '', 0),
(43, 'OWS', 'Server NL', 'ETn', 'http://n05aet0024-c01.nl.eu.abnamro.com:12116/OW-WebService/healthcheck', '', '', 0),
(44, 'OWS', 'Client NL', 'ETs', 'http://s05aet0024-c01.nl.eu.abnamro.com:14117/OW-WebServiceClient/healthCheck', '', '', 0),
(45, 'OWS', 'Server NL', 'ETs', 'http://s05aet0024-c01.nl.eu.abnamro.com:12116/OW-WebService/healthcheck', '', '', 0),
(46, 'OWS', 'Client BE', 'PROD', 'http://10.103.45.87:9080/OW-WebServiceClient/healthCheck', '', '', 0),
(47, 'OWS', 'Server BE', 'PROD', 'http://10.103.45.87:9080/OW-WebService/healthcheck', '', '', 0),
(48, 'OWS', 'Server SG', 'PROD', 'https://10.103.45.78:9443/OW-WebService/healthcheck ', '', '', 0),
(49, 'OWS', 'Client NL', 'PRODn', 'http://n05apr0027-c01.nl.eu.abnamro.com:14117/OW-WebServiceClient/healthCheck', '', '', 0),
(50, 'OWS', 'Server NL', 'PRODn', 'http://n05apr0027-c01.nl.eu.abnamro.com:12116/OW-WebService/healthcheck', '', '', 0),
(51, 'OWS', 'Client NL', 'PRODs', 'http://s05apr0027-c01.nl.eu.abnamro.com:14117/OW-WebServiceClient/healthCheck', '', '', 0),
(52, 'OWS', 'Server NL', 'PRODs', 'http://s05apr0027-c01.nl.eu.abnamro.com:12116/OW-WebService/healthcheck', '', '', 0),
(63, 'Backbase', 'GUI', 'UT', 'https://n05ast0070.nl.eu.abnamro.com:5443/intlogon/healthcheck.html', '', '', 0),
(64, 'Backbase', 'GUI', 'ST', 'https://n05ast0075.nl.eu.abnamro.com:5443/intlogon/healthcheck.html', '', '', 0),
(65, 'Backbase', 'GUI', 'ETn', 'https://n05aet0316.nl.eu.abnamro.com:5443/intlogon/healthcheck.html', '', '', 0),
(66, 'Backbase', 'GUI', 'ETs', 'https://s05aet0316.nl.eu.abnamro.com:5443/intlogon/healthcheck.html', '', '', 0),
(68, 'Backbase', 'GUI', 'PRn', 'https://n05apr0316.nl.eu.abnamro.com:5443/intlogon/healthcheck.html', '', '', 0),
(69, 'Backbase', 'GUI', 'PRs', 'https://s05apr0316.nl.eu.abnamro.com:5443/intlogon/healthcheck.html', '', '', 0),
(71, 'Backbase last successful', 'REST', 'UT', 'https://n05ast0070.nl.eu.abnamro.com:5443/lastsuccessfullogon/healthcheck.html', '', '', 0),
(72, 'Backbase last successful', 'REST', 'ST', 'https://n05ast0075.nl.eu.abnamro.com:5443/lastsuccessfullogon/healthcheck.html ', '', '', 0),
(73, 'Backbase last successful', 'REST', 'ETn', 'https://n05aet0316.nl.eu.abnamro.com:5443/lastsuccessfullogon/healthcheck.html', '', '', 0),
(74, 'Backbase last successful', 'REST', 'ETs', 'https://s05aet0316.nl.eu.abnamro.com:5443/lastsuccessfullogon/healthcheck.html', '', '', 0),
(76, 'Backbase last successful', 'REST', 'PRn', 'https://n05apr0316.nl.eu.abnamro.com:5443/lastsuccessfullogon/healthcheck.html', '', '', 0),
(77, 'Backbase last successful', 'REST', 'PRs', 'https://s05apr0316.nl.eu.abnamro.com:5443/lastsuccessfullogon/healthcheck.html', '', '', 0),
(79, 'Backbase unread messages', 'REST', 'UT', 'https://n05ast0070.nl.eu.abnamro.com:5443/unreadmessages/healthcheck.html', '', '', 0),
(80, 'Backbase unread messages', 'REST', 'ST', 'https://n05ast0075.nl.eu.abnamro.com:5443/unreadmessages/healthcheck.html \r\n', '', '', 0),
(81, 'Backbase unread messages', 'REST', 'ETn', 'https://n05aet0316.nl.eu.abnamro.com:5443/unreadmessages/healthcheck.html', '', '', 0),
(82, 'Backbase unread messages', 'REST', 'ETs', 'https://s05aet0316.nl.eu.abnamro.com:5443/unreadmessages/healthcheck.html', '', '', 0),
(84, 'Backbase unread messages', 'REST', 'PRn', 'https://n05apr0316.nl.eu.abnamro.com:5443/unreadmessages/healthcheck.html', '', '', 0),
(85, 'Backbase unread messages', 'REST', 'PRs', 'https://s05apr0316.nl.eu.abnamro.com:5443/unreadmessages/healthcheck.html', '', '', 0),
(86, 'Alerting', 'CRM', 'UT', 'http://n05ast0029-c06.nl.eu.abnamro.com:14300/alerting-crm/healthCheck', 'http://n05ast0029-c06.nl.eu.abnamro.com:14300/alerting-crm/notifications/list.htm', '', 0),
(87, 'Alerting', 'CRM', 'ST', 'http://s05ast0026-c06.nl.eu.abnamro.com:14300/alerting-crm/healthCheck', 'http://s05ast0026-c06.nl.eu.abnamro.com:14300/alerting-crm/notifications/list.htm', '', 0),
(88, 'Alerting', 'CRM', 'ETs', 'http://s05aet0300-c01.nl.eu.abnamro.com:14300/alerting-crm/healthCheck', 'http://s05aet0300-c01.nl.eu.abnamro.com:14300/alerting-crm/notifications/list.htm', '', 0),
(89, 'Alerting', 'CRM', 'ETn', 'http://n05aet0300-c01.nl.eu.abnamro.com:14300/alerting-crm/healthCheck', 'http://n05aet0300-c01.nl.eu.abnamro.com:14300/alerting-crm/notifications/list.htm', '', 0),
(90, 'Alerting', 'CRM', 'PRODs', 'http://s05apr0300-c01.nl.eu.abnamro.com:14300/alerting-crm/healthCheck', 'http://s05apr0300-c01.nl.eu.abnamro.com:14300/alerting-crm/notifications/list.htm', '', 0),
(91, 'Alerting', 'CRM', 'PRODn', 'http://n05apr0300-c01.nl.eu.abnamro.com:14300/alerting-crm/healthCheck', 'http://n05apr0300-c01.nl.eu.abnamro.com:14300/alerting-crm/notifications/list.htm', '', 0);
