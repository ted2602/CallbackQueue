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
$license = FreePBX::Callbackqueue()->checklicense();

if ($license['valid'] == 1) {
    FreePBX::PM2()->start("callbackqueue", "/var/www/html/admin/modules/callbackqueue/bin/qc-service.php");
}