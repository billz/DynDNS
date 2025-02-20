<!-- advanced settings tab -->
<div class="tab-pane fade" id="ddclientadvanced">
  <div class="row">
    <div class="col-lg-12">
      <h4 class="mt-3"><?php echo _("Advanced settings"); ?></h4>

        <div class="row">
          <div class="mb-3 col-md-6 mb-0">
            <div class="form-check form-switch">
              <?php $checked = $__template_data['ssl'] == 'yes' ? 'checked="checked"' : '' ?>
              <input class="form-check-input" id="chxddclientssl" name="ddclient-usessl" type="checkbox" value="1" <?php echo $checked ?> />
              <label class="form-check-label" for="chxddclientssl"><?php echo _("Enable SSL"); ?></label>
            </div>
            <p id="service-description">
              <small><?php echo _("Use an encrypted SSL connection for updates. Not supported by all providers.") ?></small>
            </p>
          </div>
        </div>

        <div class="row">
          <div class="mb-3 col-md-6">
            <label for="server"><?php echo _("Daemon check interval"); ?></label>
            <input type="text" class="form-control" id="txtdds-daemon" name="ddclient-daemon" value="<?php echo $__template_data['daemon']; ?>" />
            <small><?php echo _("Value specified in milliseconds (ms). Default is 300."); ?></small>
          </div>
        </div>

      </div>
    </div><!-- /.row -->
  </div><!-- /.tab-pane | advanced tab -->

