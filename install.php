<?php
/**
 * Copyright (c) 2018.
 * Itach-soft
 * www.itach.by
 * Minsk. Belarus
 */
global $db;
$sql = "
CREATE TABLE IF NOT EXISTS `qc_queues` (
`qc_id` int(11) NOT NULL AUTO_INCREMENT,
  `qc_name` varchar(255) COLLATE utf8_unicode_ci,
  `qc_queue` varchar(255) COLLATE utf8_unicode_ci,
  `qc_agentfirst` int(11) NOT NULL DEFAULT '1',
  `qc_maxcalls` varchar(255) COLLATE utf8_unicode_ci,
  `qc_minagents` varchar(255) COLLATE utf8_unicode_ci,
  `qc_callbackdest` varchar(255) COLLATE utf8_unicode_ci,
  PRIMARY KEY (`qc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
CREATE TABLE IF NOT EXISTS `qc_calls` (
  `call_id` int(11) NOT NULL,
  `uniqueid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `qc_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `datetime_in` datetime DEFAULT NULL,
  `datetime_out` datetime DEFAULT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE IF NOT EXISTS `qc_settings` (
  `licensekey` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `qc_settings` (`licensekey`) VALUES ('');
";
$check = sql($sql);
if (DB::IsError($check)) {
    die_freepbx( "Can not create table: " . $check->getMessage() .  "\n");
}