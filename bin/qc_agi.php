#!/usr/bin/php -q
<?php
/**
 * Copyright (c) 2018.
 * Itach-soft
 * www.itach.by
 * Minsk. Belarus
 */

include '/etc/freepbx.conf';

global $amp_conf;
global $db;
global $astman;

$qc_id=$argv[1];
$channel = $argv[2];
$calls=(int) qc_getcalls($db,$qc_id);
$maxcalls=  (int) get_qc_settings($db,$qc_id);

if ($calls<$maxcalls)
{
    $res='call';
}
else
{
    $res='nocall';
}
$astman->SetVar($channel,'QCMAX',$res);


function qc_getcalls($db,$qc_id='')
{
    if ($qc_id!='')
    {
        $whereand=' and `qc_id`='.$qc_id.' ';
    }
    else
    {
        $whereand='';
    }

    $sql="SELECT COUNT(*)  FROM `qc_calls` where `call`=0 $whereand";
    $res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
    return $res;
}

function get_qc_settings($db,$qc_id='')
{
    global $db;
    if ($qc_id!='')
    {
        $whereand=' where `qc_id`='.$qc_id.' ';
    }
    else
    {
        $whereand='';
    }
    $sql="SELECT  * FROM `qc_queues` $whereand";
    $res = $db->getrow($sql, DB_FETCHMODE_ASSOC);
    $res=$res['qc_maxcalls'];
    return $res;
}
