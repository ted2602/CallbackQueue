<?php
/**
 * Copyright (c) 2018.
 * Itach-soft
 * www.itach.by
 * Minsk. Belarus
 */

global $db;
$sql = "
drop TABLE qc_queues;
drop TABLE qc_calls;
drop TABLE qc_settings;
";
$check = sql($sql);
if (DB::IsError($check)) {
    die_freepbx( "Can not create table: " . $check->getMessage() .  "\n");
}

out("Dropping all relevant tables");
