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
$qc_settings=get_qc_settings($db);
var_export($qc_calls);

foreach ($qc_calls as $qc_call) {
    $call_id=$qc_call['call_id'];
    $qc_settings=  get_qc_settings($db,$qc_call['qc_id']);
    $qc_agents= (int) qc_check_agents($db,$qc_call['qc_id']);
    $qc_minagents= (int) $qc_settings['qc_minagents'];
    if ($qc_agents<$qc_minagents)
    {
        //echo "No Dial\n";
    }
    else
    {

        //echo "Dial\n";
        if ($qc_settings['qc_agentfirst']!=0)
        {
            $first=$qc_settings['qc_queue'];
            $second=$qc_call['number'];
        }
        else
        {
            $second=$qc_settings['qc_queue'];
            $first=$qc_call['number'];
        }
        //echo $qc_settings['qc_agentfirst']."\n";
        $call=qc_call($amp_conf,$first,$second,$call_id);
        //print_r($call);
    }

    //print '<pre>';
    //print_r($qc_agents);
    //print_r($qc_minagents);
    //print '</pre>';
    //ждать 2 сек
    usleep(2000000);


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

    $sql="SELECT * FROM `qc_calls` where `call`=0 $whereand";
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
    fputs($oSocket, "CallerId: $strCallerId\r\n");
    fputs($oSocket, "Exten: $second\r\n");
    fputs($oSocket, "Context: $strContext\r\n");
    fputs($oSocket, "Priority: $strPriority\r\n");
    fputs($oSocket, "Async: no\r\n");
    fputs($oSocket, "Variable: QCALL=$call_id\r\n");
    fputs($oSocket, "Action: logoff \r\n\r\n");
    sleep (1);
    fclose($oSocket);
}