<?php

class Buttonable {
    private $version = '1.2.4';
    private $startPath;
    private $customDialogPath;
    private $customDialogBackupPath;

    public function __construct($startPath) {
        $this->startPath = $startPath;
        $this->customDialogPath = dirname($startPath) . '/display/custom-dialogs.html';
        $this->customDialogBackupPath = dirname(dirname($startPath)) . '/custom-dialogs.html';
    }

    public function getVersion() {
        return $this->version;
    }

    public function install() {
        try {
            $container = new ButtonableContainer();
            $installer = $container->getInstaller();
            $status = $installer->run();
        }

        catch (Exception $e) {
            return __('Activation of Extensible HTML Editor Buttons failed. Error Message: ', 'buttonable') . $e->getMessage();
        }

        return $status;
    }

    public function run() {
        add_action('admin_init', array($this, 'runtimeUpgrade'));
        add_filter('upgrader_pre_install', array($this, 'backupCustomDialogs'));
        add_filter('upgrader_post_install', array($this, 'restoreCustomDialogs'));
        add_action('admin_menu', array($this, 'initSettingsMenu'));
        add_action('admin_footer', array($this, 'initButtons'));
        add_action('admin_head', array($this, 'hideInactiveElements'));
    }

    public function runtimeUpgrade() {
        try {
            $container = new ButtonableContainer();
            $installer = $container->getInstaller();
            $status = $installer->runtimeUpgrade();
            return $status;
        }

        catch (Exception $e) {
            return $this->formatExceptionMessage($e);
        }
    }

    // inspired by http://hungred.com/how-to/prevent-wordpress-plugin-update-deleting-important-folder-plugin/
    public function backupCustomDialogs() {
        if (file_exists($this->customDialogPath)) {
            return copy($this->customDialogPath, $this->customDialogBackupPath);
        }

        return null;
    }

    public function restoreCustomDialogs() {
        if (file_exists($this->customDialogBackupPath)) {
            $status = copy($this->customDialogBackupPath, $this->customDialogPath);

            if ($status) {
                return unlink($this->customDialogBackupPath);
            }
        }

        return null;
    }

    public function initSettingsMenu() {
        add_options_page(
            __('Extensible HTML Editor Buttons', 'buttonable'),
            __('Extensible HTML Editor Buttons', 'buttonable'),
            'manage_options',
            'buttonable',
            array($this, 'displaySettingsMenu')
        );
    }

    public function displaySettingsMenu() {
        try {
            $container = new ButtonableContainer();
            $settingsMenuHandler = $container->getSettingsMenuHandler($this->startPath);
            echo $settingsMenuHandler->run();
        }

        catch (Exception $e) {
            echo $this->formatExceptionMessage($e);
        }
    }

    public function initButtons() {
        try {
            $container = new ButtonableContainer();
            $editorHandler = $container->getEditorHandler($this->startPath);
            echo $editorHandler->addButtons();
        }

        catch (Exception $e) {
            echo $this->formatExceptionMessage($e);
        }
    }

    public function hideInactiveElements() {
        try {
            $container = new ButtonableContainer();
            $editorHandler = $container->getEditorHandler($this->startPath);
            echo $editorHandler->hideInactiveElements();
        }

        catch (Exception $e) {
            echo $this->formatExceptionMessage($e);
        }

    }

    public function uninstall() {
        try {
            $container = new ButtonableContainer();
            $functionsFacade = $container->getFunctionsFacade();
            return $functionsFacade->callFunctionForNetworkSites(array($this, 'uninstallForNetworkSites'));
        }

        catch (Exception $e) {
            echo $this->formatExceptionMessage($e);
        }

        return false;
    }

    public function uninstallForNetworkSites() {
        $container = new ButtonableContainer();
        $settings = $container->getSettings();
        return $settings->delete();
    }

    public function formatExceptionMessage($e) {
        return '<p><strong>'
            . __('Extensible HTML Editor Buttons Error', 'buttonable')
            . ':<strong></p><pre>'
            . $e->getMessage()
            . '</pre>';
    }

    /**
     * For external plugins to register custom buttons. Registered
     * buttons are automatically set to active.
     *
     * @param string $handle the name of your custom button (eg: anchor)
     * @param string $tag the tag to insert, not including delimiters (eg: a)
     * @param string $title the title attribute for the button tag (eg: add anchor tag)
     * @param string $id the id attribute for the button tag; should start with ed_ (eg: ed_anchor)
     * @param string $selfClose 'y' if a self-closing tag (eg: an image tag) 'n' otherwise
     * @param string $shortcode 'y' if a WordPress shortcode tag, 'n' if an html tag
     * @param string $path optional path to the html file for the button's dialog, relative to the WP
     *     base dir (eg: /wp-content/plugins/your_plugin/anchor_dialog.html)
     * @static
     * @access public
     * @return true
     */
    public static function registerButton($handle, $tag, $title, $id, $selfClose, $shortcode, $path = null) {
        try {
            $container = new ButtonableContainer();
            $externalButtonHandler = $container->getExternalButtonHandler();
            $externalButtonHandler->registerButton($handle, $tag, $title, $id, $selfClose, $shortcode, $path);
        }

        catch (Exception $e) {
            return __('Registration of button failed. Error Message: ', 'buttonable') . $e->getMessage();
        }

        return true;
    }

    /**
     * To de-register a button. This should be called by your plugins deactivation hook
     *
     * @param string $handle the name of your custom button (eg: anchor)
     * @static
     * @access public
     * @return true
     */
    public static function deregisterButton($handle) {
        try {
            $container = new ButtonableContainer();
            $externalButtonHandler = $container->getExternalButtonHandler();
            $externalButtonHandler->deregisterButton($handle);
        }

        catch (Exception $e) {
            return __('Deregistration of button failed. Error Message: ', 'buttonable') . $e->getMessage();
        }

        return true;
    }
}
