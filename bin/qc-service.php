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



while (true) {
$qc_calls=qc_getcalls($db);

//var_export($qc_calls);

foreach ($qc_calls as $qc_call) {
    $call_id=$qc_call['call_id'];
    $qc_settings=get_qc_settings($db,$qc_call['qc_id']);
    $qc_retry= (int) $qc_settings['qc_retry'];
    $qc_minagents= (int) $qc_settings['qc_minagents'];
    $call=(int) $qc_call['qc_call'];
    $qc_status=$qc_call['status'];
    $time=qc_checkIntervals(get_timegroup_data($qc_settings['qc_timegroup']));
    if ($call<$qc_retry AND $qc_status!='ANSWER' AND $time==true)
    {
        $qc_agents= (int) qc_check_agents($db,$qc_call['qc_id']);
        if ($qc_agents<$qc_minagents)
        {
           // echo "No Dial Agent\n";
        }
        else
        {
           // echo "Dial\n";
            if ($qc_settings['qc_agentfirst'] != 0) {
                $first = $qc_settings['qc_queue'];
                $second = $qc_call['number'];
            } else {
                $second = $qc_settings['qc_queue'];
                $first = $qc_call['number'];
            }
            //echo $qc_settings['qc_agentfirst']."\n";
            $call=qc_call($amp_conf,$first,$second,$call_id);
            //print_r($call);
            usleep(2000000);
        }
    }
    else
    {
       // echo "No Dial\n";

    }

    //print '<pre>';
    //echo("Agents $qc_agents\n");
    //echo("Retry $qc_retry\n");
    //echo("Call $call\n");
    //echo("time \n");
    //var_export($time);
    //echo("\n");

    //print '</pre>';
    //ждать 2 сек



}


    usleep(5000000);
  //  echo ("service is running");

}


function qc_check_agents ($db,$qc_id)
{
    $qc_exten=get_qc_settings($db,$qc_id);
    $qc_exten=$qc_exten['qc_queue'];
    $command = `/usr/sbin/asterisk -rx "queue show $qc_exten" | grep -E "SIP|local" | grep "Not in use" | grep -v "paused" | cut -d"(" -f1 | wc -l`;
    //$command = '/usr/sbin/asterisk -rx "queue show '.$qc_exten['qc_queue'].'" | grep -E "SIP|local" | grep "Not in use" | cut -d"(" -f1 | wc -l';
    $output = explode("m", $command);
    return $output[0];
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
    return $res;
}


function qc_getcalls($db,$qc_id='',$qc_retry='')
{
    if ($qc_id!='')
    {
        $whereand='where `qc_call`<'.$qc_retry.' and `qc_id`='.$qc_id.' ';
    }
    else
    {
        $whereand='';
    }

    $sql="SELECT * FROM `qc_calls` $whereand";
    $res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
    return $res;
}



function qc_call($amp_conf,$first,$second,$call_id)
{
    $strHost = $amp_conf['ASTMANAGERHOST'];
    $strPort = $amp_conf['ASTMANAGERPORT'];
    $strUser = $amp_conf['AMPMGRUSER'];
    $strSecret = $amp_conf['AMPMGRPASS'];
    $strPriority = "1";
    $strChannel = "local/$first@from-internal";
    $strCallerId = "<$second>";
    $strContext = 'qc-callback-dial';
    $strWaitTime = 60;


    $oSocket = fsockopen($strHost, $strPort, $errnum, $errdesc) or die("Connection to host failed");
    fputs($oSocket, "Action: login\r\n");
    fputs($oSocket, "Events: on\r\n");
    fputs($oSocket, "Username: $strUser\r\n");
    fputs($oSocket, "Secret: $strSecret\r\n\r\n");
    fputs($oSocket, "Action: originate\r\n");
    fputs($oSocket, "MaxRetries: 0\r\n");
    fputs($oSocket, "Channel: $strChannel\r\n");
    fputs($oSocket, "WaitTime: $strWaitTime\r\n");
    fputs($oSocket, "CallerId: Callback $strCallerId\r\n");
    fputs($oSocket, "Exten: $second\r\n");
    fputs($oSocket, "Context: $strContext\r\n");
    fputs($oSocket, "Priority: $strPriority\r\n");
    fputs($oSocket, "Async: no\r\n");
    fputs($oSocket, "Variable: QCALL=$call_id\r\n");
    fputs($oSocket, "Action: logoff \r\n\r\n");
    sleep (1);
    fclose($oSocket);
}

function qc_checkIntervals($intervals)
{
    foreach ($intervals as $int){
        //var_export($this->qc_checkIntervalDate($int['time']));
        if (qc_checkIntervalDate($int['time'])){
            return true;
        }
    }
    return false;
}
function qc_checkIntervalDate($interval, $timestamp = null)
{


    //echo $interval . PHP_EOL;
//    echo date('H:i | N (D) | m (F) | d', $timestamp);

    if (empty($timestamp)) {
        $timestamp = time();
    }

    list($timeInterval, $dayOfWeekInterval, $monthDayInterval, $monthInterval) = explode('|', $interval);

    // Check Time interval
    if ($timeInterval != '*') {
        list($from, $to) = explode('-', $timeInterval);
        if (date('Hi', $timestamp) < str_replace(':', '', $from)) {
            return false;
        }
        if (date('Hi', $timestamp) > str_replace(':', '', $to)) {
            return false;
        }
    }

    // Check day of week interval
    if ($dayOfWeekInterval != '*') {
        $mapping = array('mon' => 1, 'tue' => 2, 'wed' => 3, 'thu' => 4, 'fri' => 5, 'sat' => 6, 'sun' => 7);
        list($from, $to) = explode('-', $dayOfWeekInterval);
        if (date('N', $timestamp) < $mapping[$from]) {
            return false;
        }
        if (date('N', $timestamp) > $mapping[$to]) {
            return false;
        }
    }
    // Check day of month interval
    if ($monthDayInterval != '*') {

        list($from, $to) = explode('-', $monthDayInterval);
        if (date('d', $timestamp) < $from) {
            return false;
        }
        if (date('d', $timestamp) > $to) {
            return false;
        }
    }

    // Check month interval
    if ($monthInterval != '*') {
        list($from, $to) = explode('-', $monthInterval);
        if (date('m', $timestamp) < date('m', strtotime($from))) {
            return false;
        }
        if (date('m', $timestamp) > date('m', strtotime($to))) {
            return false;
        }
    }

    return true;

}
function get_timegroup_data($qc_timegroup)
{
    global $db;
    $sql="SELECT * FROM `timegroups_details` WHERE `timegroupid` ='$qc_timegroup'";
    $res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
    return $res;
}