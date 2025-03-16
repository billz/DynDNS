<?php

/**
 * DynDNS Plugin
 *
 * @description A Dynamic DNS plugin that implements ddclient
 * @author      Bill Zimmerman <billzimmerman@gmail.com>
 * @license     https://github.com/RaspAP/DynDNS/blob/master/LICENSE
 * @see         src/RaspAP/Plugins/PluginInterface.php
 * @see         src/RaspAP/UI/Sidebar.php
 */

namespace RaspAP\Plugins\DynDNS;

use RaspAP\Plugins\PluginInterface;
use RaspAP\UI\Sidebar;

class DynDNS implements PluginInterface
{

    private string $pluginPath;
    private string $pluginName;
    private string $templateMain;
    private string $serviceStatus;
    private string $ddclientConfig;
    private int $daemonInterval;

    public function __construct(string $pluginPath, string $pluginName)
    {
        $this->pluginPath = $pluginPath;
        $this->pluginName = $pluginName;
        $this->templateMain = 'main';
        $this->ddclientConfig = '/etc/ddclient.conf';
        $this->daemonInterval = 300;
     }

    /**
     * Initializes DynDNS plugin and creates a custom sidebar item
     *
     * @param Sidebar $sidebar an instance of the Sidebar
     * @see src/RaspAP/UI/Sidebar.php
     * @see https://fontawesome.com/icons
     */
    public function initialize(Sidebar $sidebar): void
    {

        $label = _('Dynamic DNS');
        $icon = 'fas fa-globe';
        $action = 'plugin__'.$this->getName();
        $priority = 70;
        $sidebar->addItem($label, $icon, $action, $priority);
    }

    /**
     * Handles a page action by processing inputs and rendering a plugin template
     *
     * @param string $page the current page route
     */
    public function handlePageAction(string $page): bool
    {
        // Verify that this plugin should handle the page
        if (strpos($page, "/plugin__" . $this->getName()) === 0) {

            // Instantiate a StatusMessage object
            $status = new \RaspAP\Messages\StatusMessage;

            if (!RASPI_MONITOR_ENABLED) {
                if (isset($_POST['SaveDDClientSettings'])) {
                    $this->saveDDClientConfig($status);
                } elseif (isset($_POST['StartDDClient'])) {
                    $status->addMessage('Attempting to start Dynamic DNS Client', 'info');
                    exec('sudo /bin/systemctl start ddclient.service', $return);
                    foreach ($return as $line) {
                        $status->addMessage($line, 'info');
                    }
                } elseif (isset($_POST['StopDDClient'])) {
                    $status->addMessage('Attempting to stop Dynamic DNS Client', 'info');
                    exec('sudo /bin/systemctl stop ddclient.service', $return);
                    foreach ($return as $line) {
                        $status->addMessage($line, 'info');
                    }
                }
            }
            // get stored provider info
            if (file_exists(RASPI_CONFIG.'/ddclient.ini')) {
                $arrIni = parse_ini_file(RASPI_CONFIG.'/ddclient.ini');
                $provider = ($arrIni) ? $arrIni['Provider'] : null;
            } else {
                $provider = null;
            }

            // system calls
            exec("ip -o link show | awk -F': ' '{print $2}'", $interfaces);
            sort($interfaces);
            exec('sudo cat '. $this->ddclientConfig, $ddclientconfig);

            $arrConfig = [];
            foreach ($ddclientconfig as $line) {
                if (strlen($line) === 0) {
                    continue;
                }
                if ($line[0] != "#") {
                    $arrLine = explode("=", $line);
                    $arrConfig[$arrLine[0]] = $arrLine[1] ?? null;
                }
            };
            $ddclientstatus = $this->ddclientStatus();
            $serviceStatus = $ddclientstatus[0] == 0 ? "down" : "up";
            $iface = $arrConfig['interface'] ?? null;
            $protocol = $arrConfig['protocol'] ?? null;
            $use = $arrConfig['use'] ?? null;
            $ssl = $arrConfig['ssl'] ?? null;
            $daemon = $arrConfig['daemon'] ?: $this->daemonInterval;

            // Populate template data
            $__template_data = [
                'title' => _('Dynamic DNS Plugin'),
                'description' => _('A Dynamic DNS plugin that implements ddclient'),
                'author' => _('Bill Z'),
                'uri' => 'https://github.com/RaspAP/DynDNS',
                'icon' => 'fas fa-globe',
                'ddclientstatus' => $ddclientstatus,
                'serviceStatus' => $serviceStatus,
                'serviceName' => 'ddclient.service',
                'interfaces' => $interfaces,
                'arrConfig' => $arrConfig,
                'services' => $this->ddnsServices(),
                'protocols' => $this->ddnsProtocols(),
                'methods' => $this->ddnsUseMethods(),
                'iface' => $iface,
                'provider' => $provider,
                'protocol' => $protocol,
                'use' => $use,
                'ssl' => $ssl,
                'daemon' => $daemon,
                'action' => 'plugin__'.$this->getName(),
                'veboseLog' => $this->ddclientVerboseLog(),
                'pluginName' => $this->getName()
            ];
            echo $this->renderTemplate($this->templateMain, compact(
                "status",
                "__template_data"
            ));
            return true;
        }
        return false;
    }

    /**
     * Renders a template from inside a plugin directory
     * @param string $templateName
     * @param array $__data
     */
    public function renderTemplate(string $templateName, array $__data = []): string
    {
        $templateFile = "{$this->pluginPath}/{$this->getName()}/templates/{$templateName}.php";

        if (!file_exists($templateFile)) {
            return "Template file {$templateFile} not found.";
        }
        if (!empty($__data)) {
            extract($__data);
        }
        ob_start();
        include $templateFile;
        return ob_get_clean();
    }

    public function ddclientStatus()
    {
        exec('cat /run/ddclient.pid | wc -l', $status);
        return $status[0];
    }

    /**
     *  Saves a ddclient configuration
     *
     * @param obj status
     */
    protected function saveDDClientConfig($status)
    {
        $return = 1;
        $errors = $this->validateDDClientInput();
        if (empty($errors)) {
            $return = $this->updateDDClientConfig($status);
        } else {
            foreach ($errors as $error) {
                $status->addMessage($error, 'danger');
            }
        }
        if ($return == 1) {
            $status->addMessage('ddclient configuration failed to be updated.', 'danger');
            return false;
        }
        if ($return == 0) {
            $status->addMessage('Restart the ddclient service for your changes to take effect.', 'success');
        } else {
            $status->addMessage('ddclient configuration failed to be updated.', 'danger');
            return false;
        }
        // persist selected provider
        $ini = [];
        $ini['Provider'] = $_POST['ddclient-provider'];
        $result = write_php_ini($ini, RASPI_CONFIG.'/ddclient.ini');

        return true;
    }

    /**
     * Validates ddclient user input from the $_POST object
     *
     * @return array $errors
     */
    protected function validateDDClientInput()
    {
        $errors = [];
        if (!filter_var($_POST['ddclient-ip'], FILTER_VALIDATE_IP) && !empty($_POST['ddclient-ip'])) {
            $errors[] = _('Invalid interface name.');
        }
        if (!filter_var($_POST['ddclient-server'], FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) && !empty($_POST['ddclient-server'])) {
            $errors[] = _('Invalid server domain.');
        }
        if ($_POST['ddclient-method'] =='cmd' && empty($_POST['ddclient-cmd'])) {
            $errors[] = _('External command not specified.');
        }
        if ($_POST['ddclient-method'] =='fw' && empty($_POST['ddclient-fw'])) {
            $errors[] = _('Firewall status page not specified.');
        }
        if (empty($_POST['ddclient-domain'])) {
            $errors[] = _('Domain not specified.');
        }
        return $errors;
    }

    /**
     * Updates a ddclient configuration
     *
     * @param object $status
     * @return boolean $result
     */
    protected function updateDDClientConfig($status)
    {
        $config = [ '# Configuration file for ddclient generated by RaspAP' ];
        $config[] = '#';
        $config[] = '# '. $this->ddclientConfig;
        $daemon = $_POST['ddclient-daemon'];
        if (!empty($_POST['ddclient-daemon'])) {
            $config[] = 'daemon=' .$_POST['ddclient-daemon'];
        } else {
            $config[] = 'daemon=' . $this->daemonInterval;
        }
        if (!empty($_POST['ddclient-usessl'])) {
            if ($_POST['ddclient-usessl'] == 1) {
                $config[] = 'ssl=yes';
            } else {
                $config[] = 'nossl';
            }
        }
        $config[] = '';
        if (isset($_POST['ddclient-method'])) {
            $key = 'ddclient-'.$_POST['ddclient-method'];
            $config[] = 'use=' .$_POST['ddclient-method'];
            $config[] = $_POST['ddclient-method'] .'='. $_POST[$key];
        }
        if (isset($_POST['ddclient-protocol'])) {
            $config[] = 'protocol=' .$_POST['ddclient-protocol'];
        }
        if (isset($_POST['ddclient-server'])) {
            $config[] = 'server=' .$_POST['ddclient-server'];
        }
        if (isset($_POST['ddclient-username'])) {
            $config[] = 'login=' .$_POST['ddclient-username'];
        }
        if (isset($_POST['ddclient-password'])) {
            $config[] = 'password=' .$_POST['ddclient-password'];
        }
        if (isset($_POST['ddclient-domain'])) {
            $config[] = $_POST['ddclient-domain'];
        }
        $config = join(PHP_EOL, $config);
        $config .= PHP_EOL;

        file_put_contents("/tmp/ddclientddata", $config);
        system('sudo cp /tmp/ddclientddata '.$this->ddclientConfig, $return);

        if ($return == 0) {
            $status->addMessage('ddclient configuration updated successfully', 'success');
        } else {
            $status->addMessage('Unable to save ddclient settings', 'danger');
            return false;
        }
        return $return;
    }

    protected function ddnsServices()
    {
        $data = json_decode(file_get_contents("config/ddns-services.json"), true);
        foreach ($data as $key => $value) {
            $services[$key] = $key;
        }
        return (array) ($services);
    }

    protected function ddnsProtocols()
    {
        $protocols = [];
        $data = json_decode(file_get_contents("config/ddns-services.json"), true);
        foreach ($data as $key => $value) {
            $protocols[$value['protocol']] = $value['protocol'];
        }
        return (array) $protocols;
    }

    protected function ddnsUseMethods()
    {
        $methods = [
            "web" => _("Discovery page on the web"),
            "if" => _("Network interface"),
            "ip" => _("Network address"),
            "fw" => _("Firewall status page"),
            "cmd" => _("External command")
        ];
        return $methods;
    }

    protected function ddclientVerboseLog()
    {
        exec("sudo ddclient -daemon=0 -debug -verbose -noquiet", $output, $return);
        $serviceLog = implode("\n", $output);
        return $serviceLog;
    }

    // Static method to load persisted data
    public static function loadData(): ?self
    {
        $filePath = "/tmp/plugin__".self::getName() .".data";
        if (file_exists($filePath)) {
            $data = file_get_contents($filePath);
            return unserialize($data);
        }
        return null;
    }

    // Returns an abbreviated class name
    public static function getName(): string
    {
        return basename(str_replace('\\', '/', static::class));
    }

}

