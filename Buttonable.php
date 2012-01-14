<?php

class Buttonable {
    private $version = '1.0';
    private $autoLoader;

    public function __construct(ToppaAutoloader $autoLoader) {
        $this->autoLoader = $autoLoader;
    }

    public function getVersion() {
        return $this->version;
    }

    public function install() {
        try {
            $container = new ButtonableContainer($this->autoLoader);
            $installer = $container->getInstaller();
            $status = $installer->run();
        }

        catch (Exception $e) {
            return __('Activation of Extensible HTML Editor Buttons failed. Error Message: ', 'buttonable') . $e->getMessage();
        }

        return $status;
    }

    public function run() {
        add_action('admin_menu', array($this, 'initSettingsMenu'));
        add_action('admin_footer', array($this, 'initButtons'));
        add_action('admin_head', array($this, 'hideQtLinkAndImgButtonsIfRequested'));
    }

    public function initSettingsMenu() {
        add_options_page(
            'Extensible HTML Editor Buttons',
            'Extensible HTML Editor Buttons',
            'manage_options',
            'buttonable',
            array($this, 'displaySettingsMenu')
        );
    }

    public function displaySettingsMenu() {
        try {
            $container = new ButtonableContainer($this->autoLoader);
            $settingsMenuHandler = $container->getSettingsMenuHandler();
            echo $settingsMenuHandler->run();
        }

        catch (Exception $e) {
            echo $this->formatExceptionMessage($e);
        }
    }

    public function initButtons() {
        try {
            $container = new ButtonableContainer($this->autoLoader);
            $editorHandler = $container->getEditorHandler();
            echo $editorHandler->addButtons();
        }

        catch (Exception $e) {
            echo $this->formatExceptionMessage($e);
        }
    }

    public function hideQtLinkAndImgButtonsIfRequested() {
        try {
            $container = new ButtonableContainer($this->autoLoader);
            $editorHandler = $container->getEditorHandler();
            echo $editorHandler->hideQtLinkAndImgButtonsIfRequested();
        }

        catch (Exception $e) {
            echo $this->formatExceptionMessage($e);
        }

    }

    public function uninstall() {
        try {
            $container = new ButtonableContainer($this->autoLoader);
            $settings = $container->getSettings();
            $settings->delete();
        }

        catch (Exception $e) {
            echo $this->formatExceptionMessage($e);
        }

        return true;
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
     * @param string $handle the name to use when referring to the custom button (eg: anchor)
     * @param string $tag the tag to insert, not including delimiters (eg: a)
     * @param string $title the title attribute for the button tag (eg: add anchor tag)
     * @param string $id the id attribute for the button tag; should start with ed_ (eg: ed_anchor)
     * @param string $self_close 'y' if a self-closing tag (eg: an image tag) 'n' otherwise
     * @param string $shortcode 'y' if a WordPress shortcode tag, 'n' if an html tag
     * @param string $path optional path to the html file for the button's dialog, relative to the WP
     *     base dir (eg: /wp-content/plugins/your_plugin/anchor_dialog.html)
     * @static
     * @access public
     */
    public static function registerButton($handle, $tag, $title, $id, $selfClose, $shortcode, $path = null) {
        try {
            $buttonableAutoLoader = new ToppaAutoLoaderWp('/extensible-html-editor-buttons');
            $container = new ButtonableContainer($buttonableAutoLoader);
            $externalButtonHandler = $container->getExternalButtonHandler();
            $externalButtonHandler->registerButton($handle, $tag, $title, $id, $selfClose, $shortcode, $path);
        }

        catch (Exception $e) {
            return __('Registration of button failed. Error Message: ', 'buttonable') . $e->getMessage();
        }

        return true;
    }

    /**
     * To register a button. This should be called by your plugins deactivation hook
     *
     * @param string $handle the name to use when referring to the custom button (eg: anchor)
     * @static
     * @access public
     */
    public static function deregisterButton($handle) {
        try {
            $buttonableAutoLoader = new ToppaAutoLoaderWp('/extensible-html-editor-buttons');
            $container = new ButtonableContainer($buttonableAutoLoader);
            $externalButtonHandler = $container->getExternalButtonHandler();
            $externalButtonHandler->deregisterButton($handle);
        }

        catch (Exception $e) {
            return __('Deregistration of button failed. Error Message: ', 'buttonable') . $e->getMessage();
        }

        return true;
    }
}