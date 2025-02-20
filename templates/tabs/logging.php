<!-- logging tab -->
<div class="tab-pane fade" id="ddclientlogging">
  <h4 class="mt-3 mb-3"><?php echo _("Logging") ?></h4>
  <p><?php echo _("The current <code>ddclient daemon</code> verbose status is provided below.") ?></p>
  <div class="row">
    <div class="mb-3 col-md-8 mt-2">
    <textarea class="logoutput text-secondary" readonly id="ddclient-log"><?php echo htmlspecialchars($__template_data['veboseLog'], ENT_QUOTES); ?></textarea>
    </div>
  </div>
</div><!-- /.tab-pane -->

