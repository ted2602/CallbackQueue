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
  `qc_id` int(11) NOT NULL,
  `qc_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `qc_queue` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `qc_agentfirst` int(11) NOT NULL DEFAULT '1',
  `qc_maxcalls` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `qc_minagents` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `qc_callbackdest` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `qc_timegroup` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `qc_retry` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `qc_calls` (
  `call_id` int(11) NOT NULL,
  `uniqueid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `qc_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `number` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `datetime_in` datetime DEFAULT NULL,
  `datetime_out` datetime DEFAULT NULL,
  `qc_call` int(11) NOT NULL DEFAULT '0',
  `status` varchar(255) COLLATE utf8_unicode_ci DEFAULT ''
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `qc_settings` (
  `licensekey` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `qc_settings` (`licensekey`) VALUES ('');
";
$check = sql($sql);
if (DB::IsError($check)) {
    die_freepbx( "Can not create table: " . $check->getMessage() .  "\n");
}