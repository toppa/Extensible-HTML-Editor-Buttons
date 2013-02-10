<?php

class ButtonableInstall {
    private $settings;
    private $functionsFacade;
    private $settingsDefaults = array(
        'buttons' => array(
            'div' => array(
                'tag' => 'div',
                'title' => 'add div tag',
                'id' => 'ed_div',
                'self_close' => 'n',
                'shortcode' => 'n',
                'active' => 'y',
                'input_dialog' => 'y'),
            'span' => array(
                'tag' => 'span',
                'title' => 'add span tag',
                'id' => 'ed_span',
                'self_close' => 'n',
                'shortcode' => 'n',
                'active' => 'y',
                'input_dialog' => 'y'),
            'link' => array(
                'tag' => 'a',
                'title' => 'add link tag',
                'id' => 'ed_anchor',
                'self_close' => 'n',
                'shortcode' => 'n',
                'active' => 'y',
                'input_dialog' => 'y'),
            'image' => array(
                'tag' => 'img',
                'title' => 'add image tag',
                'id' => 'ed_image',
                'self_close' => 'y',
                'shortcode' => 'n',
                'active' => 'y',
                'input_dialog' => 'y'),
        ),
        'customButtons' => array(),
        'externalPluginButtons' => array()
    );

    public function __construct() {
    }

    public function setSettings(ButtonableSettings $settings) {
        $this->settings = $settings;
        return $this->settings;
    }

    public function setFunctionsFacade(ButtonableFunctionsFacade $functionsFacade) {
        $this->functionsFacade = $functionsFacade;
        return $this->functionsFacade;
    }

    public function run() {
        return $this->functionsFacade->callFunctionForNetworkSites(array($this, 'runForNetworkSites'));
    }

    public function runForNetworkSites() {
        return $this->settings->set($this->settingsDefaults, true);
    }

    public function runtimeUpgrade() {
        $status = buttonableActivationChecks();

        if (is_string($status)) {
            buttonableCancelActivation($status);
            return null;
        }

        return $this->run();
    }

}