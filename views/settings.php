<?php
/**
 * Copyright (c) 2018.
 * Itach-soft
 * www.itach.by
 * Minsk. Belarus
 */
$heading = _("Settings");
$licensekey=$callbackqueue->getlicense();
$license = $callbackqueue->checklicense();
//var_export($license);



?>
<br>
<h2><?php echo $heading; ?></h2>

<div class="display">
    <div class="row">
        <div class="col-sm-12">
            <div class="fpbx-container">
                <div class="display full-border">
                    <?php echo "<h6>" . _('Service settings') . "</h6>"; ?>
                    <div class="container-fluid">
                        <?php
                        $status = $callbackqueue->getStatus('callbackqueue');
                        print '<pre>';
                        print_r(_('Callback PID') . ': ');
                        print_r($status['pid']);
                        print_r('    ' . ('Start time') . ': ');
                        print_r(date('d M Y H:i:s', $status['pm2_env']['pm_uptime'] / 1000));
                        print '</pre>';
                        ?>
                        <a href=?display=callbackqueue&view=settings&action=start class="btn btn-default">Start</a>
                        <a href=?display=callbackqueue&view=settings&action=stop class="btn btn-default">Stop</a>
                        <a href=?display=callbackqueue&view=settings&action=restart
                           class="btn btn-default">Restart</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
    <form autocomplete="off" class="fpbx-submit" name="licensekey">
        <input type="hidden" name="display" value="callbackqueue">
        <input type="hidden" name="view" value="settings">
        <input type="hidden" name="action" value="licensekey">
        <div class="row">
            <div class="col-sm-12">
                <div class="fpbx-container">
                    <div class="display full-border">
                        <?php echo "<h6>" . _('License settings') . "</h6>";
                        echo("<h5>".$license['status']."</h5>");

                        ?>
                        <!--licensekey ID-->
                        <div class="element-container">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-md-3">
                                                <label class="control-label"
                                                       for="client_id"><?php echo(_("Your Itach-soft licensekey")); ?></label>
                                                <i class="fa fa-question-circle fpbx-help-icon"
                                                   data-for="licensekey"></i>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="licensekey"
                                                       name="licensekey"
                                                       value="<?php echo(isset($licensekey) ? $licensekey : ''); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <span id="licensekey-help"
                                          class="help-block fpbx-help-block"><?php echo(_("Enter licensekey from Itach-soft")); ?></span>
                                </div>
                            </div>
                        </div>
                        <br> <input type="submit" name="submit" value="<?php echo(_("Submit")); ?>">
    </form>
    <!--END licensekey-->
</div>
</div>
</div>
</div>
</div>





