<?php
/**
 * Copyright (c) 2018.
 * Itach-soft
 * www.itach.by
 * Minsk. Belarus
 */

namespace FreePBX\modules;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Callbackqueue implements \BMO


{

    //функции инициализации класса модуля для Freepbx
    public function __construct($freepbx = null)
    {
        if ($freepbx == null) {
            //throw new Exceptio    n("Not given a FreePBX Object");
        }
        $this->FreePBX = $freepbx;
        $this->db = $freepbx->Database;
    }

    public function install()
    {
    }

    public function uninstall()
    {
    }

    public function backup()
    {
    }

    public function restore($backup)
    {
    }
    public function dashboardService()
    {

        $services = array(
            array("title" => "Itach Callback", "type" => "unknown", "tooltip" => _("Unknown"), "order" => 999, "command" => __DIR__ . "/check-queuecallback.sh")
        );

        foreach ($services as &$service ) {
            $output = "";
            exec($service["command"] . " 2>&1", $output, $ret);

            if ($ret === 0) {
                $service = array_merge($service, $this->genAlertGlyphicon("ok", $output[0]));
            }
            else {
                $service = array_merge($service, $this->genAlertGlyphicon("warning", $output[0]));
            }
        }

        return $services;
    }
    private function genAlertGlyphicon($res, $tt = NULL)
    {
        return \FreePBX::Dashboard()->genStatusIcon($res, $tt);
    }
    public function startFreepbx($output)
    {
        $script = __DIR__ . "/start-queuecallback.sh";

        if (!file_exists($script)) {
            return true;
        }

        $output->writeln(_("Running Itach Callback Hooks"));
        $process = new Process("$script &> /dev/null");

        try {
            $output->writeln(_("Starting Itach Callback Daemon"));
            $process->mustRun();
            $output->writeln(_("Queue Itach Callback Started"));
        }
        catch (ProcessFailedException $e) {
            $output->writeln(sprintf(_("Itach Callback Daemon Start Failed: %s"), $e->getMessage()));
        }

        return true;
    }

    public function stopFreepbx($output)
    {
        $script = __DIR__ . "/start-queuecallback.sh";

        if (!file_exists($script)) {
            return true;
        }

        $output->writeln(_("Running Itach Callback Hooks"));
        $output->writeln(_("Stopping Itach Callback Daemon"));
        $pids = shell_exec("pgrep -f qc-service.php");

        if ($pids) {
            $allpids = explode(" ", $pids);

            foreach ($allpids as $p ) {
                posix_kill($p, 9);
            }
        }

        $output->writeln(_("Queue Itach Callback Stopped"));
        return true;
    }


    /**
     * Start a process
     * @method start
     * @param  string $name    The name of the application
     * @param  string $process The process to run
     * @return mixed           Output of getStatus
     */
    public function start()
    {
        $license = $this->checklicense();
        if ($license['valid'] == 1) {

        \FreePBX::PM2()->start("callbackqueue", "/var/www/html/admin/modules/callbackqueue/bin/qc-service.php");
    }
    }
    /**
     * Stop process
     * @method stop
     * @param  string  $name The application name
     */
    public function stop()
    {
        $license = $this->checklicense();
        if ($license['valid'] == 1) {


            \FreePBX::PM2()->stop("callbackqueue");
        }
    }
    /**
     * Restart process
     * @method restart
     * @param  string  $name The application name
     */
    public function restart()
    {
        $license = $this->checklicense();
        if ($license['valid'] == 1) {

            \FreePBX::PM2()->restart("callbackqueue");
        }
    }

    /**
     * Get status of a process
     * @method getStatus
     * @param  string    $name The process name
     * @return mixed          Return array of data if known or false if unknown
     */
    public function getStatus($name) {
        $out = \FreePBX::PM2()->getStatus("$name");
        if ($out!=NULL){
            return $out;
        }
        else{
            $out='';
            return $out;
        }
        }
    public function chownFreepbx()
    {
        $moduledir = \FreePBX::Config()->get("AMPWEBROOT") . "/admin/modules/callbackqueue";
        $files = array(
            array("type" => "file", "path" => $moduledir . "/check-queuecallback.sh", "perms" => 493),
            array("type" => "file", "path" => $moduledir . "/start-queuecallback.sh", "perms" => 493),
            array("type" => "file", "path" => $moduledir . "/bin/qc-service.php", "perms" => 493),
            array("type" => "file", "path" => $moduledir . "/bin/qc_agi.php", "perms" => 493)

        );
        return $files;
    }





    //конец функции инициализации класса модуля для Freepbx
    public function doConfigPageInit($page)
    {
        isset($_REQUEST['action']) ? $action = $_REQUEST['action'] : $action = '';
        isset($_REQUEST['itemid']) ? $itemid = $_REQUEST['itemid'] : $itemid = '';
        switch ($action) {
            case "start":
                $this->start();
                break;
            case "stop":
                $this->stop();
                break;
            case "restart":
                $this->restart();
                break;
            case "qc_delete":
                needreload();
                $this->qc_delete($_REQUEST);
                break;
            case "settings":
                $res = editsettings($_REQUEST);
                needreload();
                break;
            case "new_qc":
                needreload();
                $this->qc_insert($_REQUEST);
                break;
            case "edit_qc":
                needreload();
                $this->edit_qc($_REQUEST);
                break;
            case "licensekey":
                needreload();
                $this->updatelicense($_REQUEST['licensekey']);
                break;
        }
    }


//функция для построения boostrap таблиц
    public function ajaxHandler()
    {
        switch ($_REQUEST['command']) {
            case 'getJSON':
                switch ($_REQUEST['jdata']) {

                    case 'qc_queues':
                        return array_values($this->qc_getqueues());
                        break;
                    case 'agent_statistic':
                        return array_values($this->agent_statistic($_REQUEST));
                        break;
                    case 'queuestat':
                        return array_values($this->qc_getcalls());
                        break;
                    default:
                        return false;
                        break;
                }
                break;

            default:
                return false;
                break;
        }


    }

    public function ajaxRequest($req, &$setting)
    {
        switch ($req) {
            case 'getJSON':
                return true;
                break;
            default:
                return false;
                break;
        }
    }

    public function getqueus()
    {
        global $db;
        $sql="SELECT `extension`,`descr` FROM `queues_config`";
        $res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
        return $res;
    }

    public function qc_insert($request)
    {
        global $db;
        $qc_callbackdest=isset($_REQUEST[$_REQUEST['goto0'].'0']) ? $_REQUEST[$_REQUEST['goto0'].'0'] : "";
        $query = $this->db->prepare("INSERT INTO `qc_queues`(`qc_name`, `qc_queue`, `qc_agentfirst`, `qc_maxcalls`, `qc_minagents`,`qc_callbackdest`,`qc_retry`,`qc_timegroup`) VALUES (:qc_name,:qc_queue,:qc_agentfirst,:qc_maxcalls,:qc_minagents,:qc_callbackdest,:qc_retry,:qc_timegroup)");
        $query->bindParam(':qc_name', $request['qc_name']);
        $query->bindParam(':qc_queue', $request['qc_queue']);
        $query->bindParam(':qc_agentfirst', $request['qc_first']);
        $query->bindParam(':qc_maxcalls', $request['qc_maxcalls']);
        $query->bindParam(':qc_minagents', $request['qc_minagents']);
        $query->bindParam(':qc_callbackdest', $qc_callbackdest);
        $query->bindParam(':qc_retry', $request['qc_retry']);
        $query->bindParam(':qc_timegroup', $request['qc_timegroup']);
        $query->execute();
    }
    public function edit_qc($request)
    {
        global $db;
        $qc_callbackdest=isset($_REQUEST[$_REQUEST['goto0'].'0']) ? $_REQUEST[$_REQUEST['goto0'].'0'] : "";

        $query = $this->db->prepare("UPDATE `qc_queues` SET `qc_name`=:qc_name, `qc_queue`=:qc_queue, `qc_agentfirst`=:qc_agentfirst, `qc_maxcalls`=:qc_maxcalls, `qc_minagents`=:qc_minagents, `qc_callbackdest`=:qc_callbackdest, `qc_retry`=:qc_retry, `qc_timegroup`=:qc_timegroup WHERE `qc_id` = :qc_id");

        $query->bindParam(':qc_id', $request['qc_id']);
        $query->bindParam(':qc_name', $request['qc_name']);
        $query->bindParam(':qc_queue', $request['qc_queue']);
        $query->bindParam(':qc_agentfirst', $request['qc_first']);
        $query->bindParam(':qc_maxcalls', $request['qc_maxcalls']);
        $query->bindParam(':qc_minagents', $request['qc_minagents']);
        $query->bindParam(':qc_callbackdest', $qc_callbackdest);
        $query->bindParam(':qc_retry', $request['qc_retry']);
        $query->bindParam(':qc_timegroup', $request['qc_timegroup']);
        $query->execute();
    }
    public function qc_getqueues($qc_id='')
    {
        global $db;
        if ($qc_id!='')
        {
            $qc_where=" where `qc_id` = $qc_id";
        }
        else
        {
            $qc_where="";
        }
        $sql="SELECT * FROM `qc_queues` $qc_where";
        $res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
        $i=0;
        foreach ($res as $queue)
        {
            $res[$i]['qc_timegroup_name']=$this->get_timegroup_name($queue['qc_timegroup']);
            $res[$i]['qc_callwait']=$this->qc_callwait($queue['qc_id'],$queue['qc_retry']);
            $i++;
        }

        return $res;
    }

    public function qc_delete($request)
    {
        $query = $this->db->prepare("DELETE FROM `qc_queues` WHERE `qc_id` = :qc_id");
        $query->bindParam(':qc_id', $request['qc_id'], \PDO::PARAM_STR);
        $query->execute();

    }
    public function qc_callwait($qc_id,$qc_retry)
    {
        global $db;
        $sql="SELECT COUNT(call_id) FROM `qc_calls` where `finish` = '0' and `qc_id` = $qc_id";
        $res = $db->getrow($sql, DB_FETCHMODE_ASSOC);
        return $res['COUNT(call_id)'];
    }
    public function qc_getcalls($qc_id='')
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

        $sql="SELECT *  FROM `qc_calls` $whereand";
        $res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
        return $res;
    }
    public function getlicense()
    {
        global $db;

        $sql="SELECT `licensekey` FROM `qc_settings`";
        $res = $db->getrow($sql, DB_FETCHMODE_ASSOC);
        return $res['licensekey'];
    }
    public function get_timegroup_name($qc_timegroup)
    {
        global $db;
        $sql="SELECT `description` FROM `timegroups_groups` where id='$qc_timegroup'";
        $res = $db->getrow($sql, DB_FETCHMODE_ASSOC);
        return $res['description'];
    }
    public function get_timegroup_data($qc_timegroup)
    {
        global $db;
        $sql="SELECT * FROM `timegroups_details` WHERE `timegroupid` ='$qc_timegroup'";
        $res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
        return $res;
    }
    public function qc_checkIntervals($intervals)
    {
        foreach ($intervals as $int){
            //var_export($this->qc_checkIntervalDate($int['time']));
            if ($this->qc_checkIntervalDate($int['time'])){
                return true;
            }
        }
        return false;
    }
    public function qc_checkIntervalDate($interval, $timestamp = null)
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

    public function updatelicense($licensekey)
    {

        $query = $this->db->prepare("UPDATE `qc_settings` SET `licensekey`=:licensekey");
        $query->bindParam(':licensekey', $licensekey);
        $query->execute();
    }
    public function checklicense()
    {
        $licensekey=$this->getlicense();

        if ($curl = curl_init()) {
            curl_setopt($curl, CURLOPT_URL, 'https://keys.itach.by/?status=key&key=' . urlencode($licensekey));
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
            $out = curl_exec($curl);
            $out = json_decode($out, true);
            curl_close($curl);

        }
        $today=date_create()->format('Y-m-d');
        if ($out==NULL)
        {
            $answer=_("License server not answered");
            $valid=1;
        }
        elseif ($out['key']==0)
        {
            $answer=_("License not valid");
            $valid=0;

        }elseif ($out['key']==1)
        {
            if ($out['validdate']<$today)
            {
                $answer=_("License is expired");
                $valid=0;
            }
            else
            {
                $answer=_("License is valid");
                $valid=1;
            }
        }

        return array('status'=>$answer,'valid'=>$valid);
    }


}