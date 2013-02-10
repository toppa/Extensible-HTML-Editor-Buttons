<?php

class ButtonableExternalButtonHandler {
    private $settings;
    private $functionsFacade;

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

    public function registerButton($handle, $tag, $title, $id, $selfClose, $shortcode, $path = null) {
        $selfClose = strtolower($selfClose);
        $shortcode = strtolower($shortcode);

        if (!is_string($handle)) {
            throw New Exception(__('the 1st argument ($handle) must be a string', 'buttonable'));
        }

        if (!is_string($tag)) {
            throw New Exception(__('the 2nd argument ($tag) must be a string', 'buttonable'));
        }

        if (!is_string($title)) {
            throw New Exception(__('the 3rd argument ($title) must be a string', 'buttonable'));
        }

        if (!is_string($id)) {
            throw New Exception(__('the 4th argument ($id) must be a string', 'buttonable'));
        }

        if ($selfClose != 'y' && $selfClose != 'n') {
            throw New Exception(__('the 5th argument ($selfClose) must be "y" or "n"', 'buttonable'));
        }

        if ($shortcode != 'y' && $shortcode != 'n') {
            throw New Exception(__('the 6th argument ($shortcode) must be "y" or "n"', 'buttonable'));
        }

        $buttonableSettings = array(
            'externalPluginButtons' => array(
                $handle => array(
                    'tag' => $tag,
                    'title' => $title,
                    'id' => $id,
                    'self_close' => $selfClose,
                    'shortcode' => $shortcode,
                    'active' => 'y',
                    'input_dialog' => 'n'
                )
            )
        );

        if (is_string($path) && file_exists($path)) {
            $buttonableSettings['externalPluginButtons'][$handle]['input_dialog'] = 'y';
            $buttonableSettings['externalPluginButtons'][$handle]['path'] = $path;
        }

        $this->settings->set($buttonableSettings);
        return true;
    }

    public function deregisterButton($handle) {
        $this->settings->purge(array('externalPluginButtons', $handle));
        return true;
    }
}