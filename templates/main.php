<?php ob_start() ?>
  <?php if (!RASPI_MONITOR_ENABLED) : ?>
    <input type="submit" class="btn btn-outline btn-primary" name="SaveDDClientSettings" value="<?php echo _("Save settings"); ?>" />
    <?php if ($__template_data['ddclientstatus'] == 0) : ?>
      <input type="submit" class="btn btn-success" name="StartDDClient" value="<?php echo  _("Start Dynamic DNS"); $msg=_("Starting Dynamic DNS"); ?>" data-bs-toggle="modal" data-bs-target="#ddclientModal"/>
    <?php else : ?>
      <input type="submit" class="btn btn-warning" name="StopDDClient" value="<?php echo _("Stop Dynamic DNS") ?>"/>
      <input type ="submit" class="btn btn-warning" name="RestartDDClient" value="<?php echo _("Restart Dynamic DNS"); $msg=_("Restarting Dynamic DNS"); ?>" data-bs-toggle="modal" data-bs-target="#ddclientModal"/>
    <?php endif ?>
    <!-- Modal -->
    <div class="modal fade" id="ddclientModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <div class="modal-title" id="ModalLabel"><i class="fas fa-sync-alt fa-spin me-2"></i><?php echo $msg ?></div>
          </div>
          <div class="modal-body">
            <div class="col-md-12 mb-3 mt-1"><?php echo _("Executing Dynamic DNS service start") ?>...</div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline btn-primary" data-bs-dismiss="modal"><?php echo _("Close"); ?></button>
          </div>
        </div>
      </div>
    </div>
  <?php endif ?>
<?php $buttons = ob_get_clean(); ob_end_clean() ?>

<div class="row">
  <div class="col-lg-12">
    <div class="card">

      <div class="card-header">
        <div class="row">
          <div class="col">
            <i class="fas fa-globe me-2"></i><?php echo _("Dynamic DNS"); ?>
          </div>
          <div class="col">
            <button class="btn btn-light btn-icon-split btn-sm service-status float-end">
              <span class="icon text-gray-600"><i class="fas fa-circle service-status-<?php echo $__template_data['serviceStatus'] ?>"></i></span>
              <span class="text service-status"><?php echo $__template_data['serviceName'];?> <?php echo $__template_data['serviceStatus'] ?></span>
            </button>
          </div>
        </div><!-- /.row -->
      </div><!-- /.card-header -->

      <div class="card-body">
        <?php $status->showMessages(); ?>
        <form role="form" action="<?php echo $__template_data['action']; ?>" method="POST">
          <?php echo \RaspAP\Tokens\CSRF::hiddenField(); ?>

          <!-- Nav tabs -->
          <ul class="nav nav-tabs">
            <li class="nav-item"><a class="nav-link active" id="ddclientbasictab" href="#ddclientbasic" aria-controls="basic" data-bs-toggle="tab"><?php echo _("Basic"); ?></a></li>
            <li class="nav-item"><a class="nav-link" id="ddclientadvancedtab" href="#ddclientadvanced" data-bs-toggle="tab"><?php echo _("Advanced"); ?></a></li>
            <li class="nav-item"><a class="nav-link" id="ddclientloggingtab" href="#ddclientlogging" data-bs-toggle="tab"><?php echo _("Logging"); ?></a></li>
            <li class="nav-item"><a class="nav-link" id="ddclientabouttab" href="#ddclientabout" data-bs-toggle="tab"><?php echo _("About"); ?></a></li>
          </ul>

           <!-- Tab panes -->
            <div class="tab-content">
              <?php echo renderTemplate("tabs/basic", $__template_data, $__template_data['pluginName']) ?>
              <?php echo renderTemplate("tabs/advanced", $__template_data, $__template_data['pluginName']) ?>
              <?php echo renderTemplate("tabs/logging", $__template_data, $__template_data['pluginName']) ?>
              <?php echo renderTemplate("tabs/about", $__template_data, $__template_data['pluginName']) ?>
            </div><!-- /.tab-content -->

          <?php echo $buttons ?>
        </form>
      </div><!-- /.card-body -->
      <div class="card-footer"> <?php echo _("Information provided by ddclient"); ?></div>
    </div><!-- /.card -->
  </div><!-- /.col-lg-12 -->
</div><!-- /.row -->

<!-- Custom Plugin JS -->
<script src="/app/js/plugins/DynDNS.js"></script>

