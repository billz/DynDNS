<!-- basic settings tab -->
<div class="tab-pane active" id="ddclientbasic">
  <div class="row">
    <div class="col-lg-12">
      <h4 class="mt-3"><?php echo _("Basic settings"); ?></h4>

        <div class="row">
          <div class="mb-3 col-md-6">
            <h5 class="mt-1">Service provider</h5>
            <p id="service-description">
              <small><?php echo _("Select a Dynamic DNS service supported by <strong>ddclient</strong> from the list below. Selecting a known service provider will populate the <code>protocol</code> and <code>server</code> fields. You may also configure the service manually.") ?></small>
            </p>
            <?php SelectorOptions('ddclient-provider', $__template_data['services'], $__template_data['provider'], 'cbxddns-provider', 'setddnsProviderData', null, 'custom-select custom-select-sm', 'Select a provider...'); ?>
          </div>
        </div>

        <div class="row">
          <div class="mb-3 col-md-6">
            <label for="protocol"><?php echo _("Protocol"); ?></label>
              <?php SelectorOptions('ddclient-protocol', $__template_data['protocols'], $protocol, 'cbxddns-protocol', 'setddnsProtocols', null, 'custom-select custom-select-sm', 'Select a protocol...'); ?>
          </div>
        </div>

        <div class="row">
          <div class="mb-3 col-md-6">
            <label for="server"><?php echo _("Server"); ?></label>
            <input type="text" class="form-control" id="ddclient-server" name="ddclient-server" value="<?php safeOutputValue('server',$arrConfig); ?>" />
          </div>
        </div>

        <div class="row">
          <div class="mb-3 col-md-6">
            <label for="cbxdddns-method"><?php echo _("Method to obtain IP") ;?></label>
            <p id="service-description">
              <small><?php echo _("Select the method used by <strong>ddclient</strong> to obtain an IP address. This value is specified in the <code>-use</code> option.") ?></small>
            </p>
            <?php SelectorOptions('ddclient-method', $__template_data['methods'], $__template_data['use'], 'cbxddns-methods', null, null, 'custom-select custom-select-sm', 'Select a method...'); ?>
          </div>
        </div>

        <div id="web" class="row ddclient-opt d-none">
          <div class="mb-3 col-md-6">
            <label for="web"><?php echo _("Web address") ;?></label>
            <input type="text" class="form-control" name="ddclient-web" value="<?php safeOutputValue('web', $arrConfig); ?>" />
          </div>
        </div>

        <div id="if" class="row ddclient-opt d-none">
          <div class="mb-3 col-md-6">
            <label for="cbxinterface"><?php echo _("Interface") ;?></label>
            <?php SelectorOptions('ddclient-if', $interfaces, $iface, 'cbxinterface'); ?>
          </div>
        </div>

        <div id="ip" class="row ddclient-opt d-none">
          <div class="mb-3 col-md-6">
            <label for="ip"><?php echo _("IP address") ;?></label>
            <input type="text" class="form-control" name="ddclient-ip" value="<?php safeOutputValue('ip', $arrConfig); ?>" />
          </div>
        </div>

        <div id="fw" class="row ddclient-opt d-none">
          <div class="mb-3 col-md-6">
            <label for="fw"><?php echo _("Firewall") ;?></label>
            <input type="text" class="form-control" name="ddclient-fw" value="<?php safeOutputValue('fw', $arrConfig); ?>" />
            <small><?php echo _("Example: <code>192.168.1.254/status.htm</code>."); ?></small>
          </div>
        </div>

        <div id="cmd" class="row ddclient-opt d-none">
          <div class="mb-3 col-md-6">
            <label for="cmd"><?php echo _("Command") ;?></label>
            <input type="text" class="form-control" name="ddclient-cmd" value="<?php safeOutputValue('cmd', $arrConfig); ?>" />
            <small><?php echo _("Example: <code>/usr/local/bin/get-ip</code>."); ?></small>
          </div>
        </div>

        <div class="row">
          <div class="mb-3 col-md-6">
            <label for="code"><?php echo _("Username"); ?></label>
            <input type="text" class="form-control" id="txtddclient-username" name="ddclient-username" value="<?php safeOutputValue('login',$arrConfig); ?>" />
          </div>
        </div>

        <div class="row">
          <div class="mb-3 col-md-6">
            <label for="code"><?php echo _("Password"); ?></label>
            <div class="input-group">
              <input type="password" class="form-control" id="txtddclient-password" name="ddclient-password" value="<?php safeOutputValue('password', $arrConfig); ?>" />
              <div class="input-group-text js-toggle-password" data-bs-target="[name=ddclient-password]" data-toggle-with="fas fa-eye-slash"><i class="fas fa-eye mx-2"></i></div> 
            </div>
          </div>
        </div>

        <div class="row">
          <div class="mb-3 col-md-6">
            <label for="code"><?php echo _("Domain"); ?></label>
            <input type="text" class="form-control" id="txtddclient-domain" name="ddclient-domain" value="<?php echo htmlspecialchars(array_key_last($arrConfig), ENT_QUOTES); ?>" />
          </div>
        </div>

      </div>
    </div><!-- /.row -->
  </div><!-- /.tab-pane | basic tab -->

