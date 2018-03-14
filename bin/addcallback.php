<?php
/**
 * Copyright (c) 2018.
 * Itach-soft
 * www.itach.by
 * Minsk. Belarus
 */
include '/etc/freepbx.conf';

global $amp_conf;

// --qc_id "'.$qc_queue['qc_id'].'" --id="${CHANNEL(linkedid)}" --number="${QCnumber}" --datetime="${STRFTIME(${EPOCH},,%Y-%m-%d %H:%M:%S)}" >/dev/null 2>/dev/null &'));

$longopts = array(
    "id:",
    "number:",
    "datetime:",
    "qc_id:",
);

if ($_REQUEST == NULL) {
    $shortopts = "";

    $options = getopt($shortopts, $longopts);
} else {
    $options = $_REQUEST;
}
if (isset($options['id'])) {
    $id = $options['id'];
}
if (isset($options['number'])) {
    $number =   preg_replace('/\D*/', '', $options['number']);
    ;
}
if (isset($options['datetime'])) {
    $datetime = $options['datetime'];
}
if (isset($options['qc_id'])) {
    $qc_id = $options['qc_id'];
}

//var_export($options);

addcall($id,$qc_id,$number,$datetime);




function addcall ($id,$qc_id,$number,$datetime)
{
    global $db;
    $query = $db->prepare("INSERT INTO `qc_calls` (`uniqueid`, `qc_id`, `number`, `datetime_in`) VALUES (:uniqueid,:qc_id,:qc_number,:datetime_in)");
    $query->bindParam(':uniqueid', $id);
    $query->bindParam(':qc_id', $qc_id);
    $query->bindParam(':qc_number', $number);
    $query->bindParam(':datetime_in', $datetime);

    $check = $query->execute();
    if (DB::IsError($check)) {
        die_freepbx( "Can not create table: " . $check->getMessage() .  "\n");
    }
}
