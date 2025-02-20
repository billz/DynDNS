/*
 * Populates ddclient provider data
 */
function setddnsProviderData() {
    $.getJSON("config/ddns-services.json", function(json) {
        var provider = $('#cbxddns-provider').val();
        if(provider) {
            $('#cbxddns-protocol').val(json[provider].protocol);
            $('#ddclient-server').val(json[provider].server);
        }
    })
}
