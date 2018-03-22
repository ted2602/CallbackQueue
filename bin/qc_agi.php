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
$qc_settings=  get_qc_settings($db,$qc_id);
$maxcalls= (int) $qc_settings['qc_maxcalls'];
$qc_retry= (int) $qc_settings['qc_retry'];
$calls= (int) qc_callwait($qc_id,$qc_retry);

//var_export($calls);
if ($calls<$maxcalls)
{
    $res='call';
}
else
{
    $res='nocall';
}
$astman->SetVar($channel,'QCMAX',$res);
$astman->SetVar($channel,'QCMAXcount',qc_callwait($qc_id,$qc_retry));
$astman->SetVar($channel,'QCMAXcountmax',$maxcalls);


 function qc_callwait($qc_id,$qc_retry)
{
    global $db;
    $sql="SELECT COUNT(call_id) FROM `qc_calls` where `qc_call`<$qc_retry and qc_id=$qc_id and `status`!='ANSWER'";
   // var_export($sql);
    $res = $db->getrow($sql, DB_FETCHMODE_ASSOC);

    return $res['COUNT(call_id)'];
}
function get_qc_settings($db,$qc_id='')
{
    global $db;
    if ($qc_id!='')
    {
        $whereand=' where `qc_id`='.$qc_id.';';
    }
    else
    {
        $whereand='';
    }
    $sql="SELECT  * FROM `qc_queues` $whereand";
    $res = $db->getrow($sql, DB_FETCHMODE_ASSOC);
    $res=$res;
    return $res;
}
