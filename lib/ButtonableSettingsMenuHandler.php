<?php

class ButtonableSettingsMenuHandler {
    private $request;
    private $settings;
    private $cleanRequest;
    private $settingsMenuDisplayer;
    private $customButtonRefData;

    public function __construct() {
    }

    public function setRequest(array $request) {
        $this->request = $request;
        return $this->request;
    }

    public function setSettings(ButtonableSettings $settings) {
        $this->settings = $settings;
        return $this->settings;
    }

    public function setSettingsMenuDisplayer(ButtonableSettingsMenuDisplayer $settingsMenuDisplayer) {
        $this->settingsMenuDisplayer = $settingsMenuDisplayer;
        return $this->settingsMenuDisplayer;
    }

    public function setCustomButtonRefData(ButtonableCustomButtonRefData $customButtonRefData) {
        $this->customButtonRefData = $customButtonRefData;
        return $this->customButtonRefData;
    }

    public function run() {
        $this->setCleanRequest();

        if (isset($this->cleanRequest['buttonableAction'])) {
            switch ($this->cleanRequest['buttonableAction']) {
                case 'addButton':
                    $validateStatus = $this->validateNewCustomButton();
                    $finalActionStatus = $this->saveNewCustomButton($validateStatus);
                    break;
                case 'updateButtons':
                    $validateStatusForBuiltInButtons = $this->validateUpdatedBuiltInButtons();
                    $saveStatusForBuiltInButtons = $this->saveUpdatedBuiltInButtons($validateStatusForBuiltInButtons);
                    $validateStatusForCustomButtons = $this->validateUpdatedCustomButtons($saveStatusForBuiltInButtons);
                    $saveStatusForCustomButtons = $this->saveUpdatedCustomButtons($validateStatusForCustomButtons);
                    $deleteStatusForCustomButtons = $this->deleteCustomButtons($saveStatusForCustomButtons);
                    $validateStatusForExternalButtons = $this->validateUpdatedExternalPluginButtons($deleteStatusForCustomButtons);
                    $finalActionStatus = $this->saveUpdatedExternalPluginButtons($validateStatusForExternalButtons);
                    break;
                case null:
                    break;
                default:
                    throw New Exception(__('Requested action not recognized', 'buttonable'));
            }
        }

        if ($finalActionStatus === false) {
            $message = __('Settings not saved. All fields for custom buttons are required', 'buttonable');
        }

        elseif (is_array($finalActionStatus)) {
            $message = __('Settings saved', 'buttonable');
        }

        return $this->settingsMenuDisplayer->run($message);
    }

    public function setCleanRequest() {
        $this->cleanRequest = $this->request;
        array_walk_recursive($this->cleanRequest, 'ButtonableFunctions::htmlentitiesCallback');
        array_walk_recursive($this->cleanRequest, 'ButtonableFunctions::trimCallback');
        return $this->cleanRequest;
    }

    public function validateNewCustomButton() {
        if (!$this->cleanRequest['buttonableNewButton']['handle']) {
            return false;
        }

        $buttonRefData = $this->customButtonRefData->getRefData();

        foreach ($buttonRefData as $k=>$v) {
            if (!isset($this->cleanRequest['buttonableNewButton']['settings'][$k])) {
                return false;
            }
        }

        return true;
    }

    public function saveNewCustomButton($validateStatus) {
        if (!$validateStatus) {
            return false;
        }

        $handle = $this->cleanRequest['buttonableNewButton']['handle'];
        $settings = array();
        $settings['customButtons'] = $this->settings->customButtons;
        $settings['customButtons'][$handle] = array();

        foreach($this->cleanRequest['buttonableNewButton']['settings'] as $k=>$v) {
            $settings['customButtons'][$handle][$k] = $v;
        }

        $this->settings->set($settings);
        return $settings;
    }

    public function validateUpdatedBuiltInButtons() {
        return $this->validateRequestedButtons('buttonableBuiltInButtons', 'buttons');
    }

    private function validateRequestedButtons($formName, $settingsName) {
        if (is_array($this->cleanRequest[$formName])) {
            foreach ($this->cleanRequest[$formName] as $handle=>$newSettings) {
                if (isset($newSettings['active']) && $newSettings['active'] != 'y' && $newSettings['active'] != 'n') {
                    return false;
                }

                if (!array_key_exists($handle, $this->settings->$settingsName)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function saveUpdatedBuiltInButtons($validateStatus) {
        return $this->saveRequestedButtons($validateStatus, 'buttonableBuiltInButtons', 'buttons');
    }

    private function saveRequestedButtons($validateStatus, $formName, $settingsName) {
        if (!$validateStatus) {
            return false;
        }

        $settings = array();
        $settings[$settingsName] = $this->settings->$settingsName;

        if (is_array($this->cleanRequest[$formName])) {
            foreach ($this->cleanRequest[$formName] as $handle=>$newSettings) {
                $settings[$settingsName][$handle]['active'] = $newSettings;
            }
        }

        $this->settings->set($settings);
        return $settings;
    }

    public function validateUpdatedExternalPluginButtons() {
        return $this->validateRequestedButtons('buttonableExternalPluginButtons', 'externalPluginButtons');
    }

    public function saveUpdatedExternalPluginButtons($validateStatus) {
        return $this->saveRequestedButtons($validateStatus, 'buttonableExternalPluginButtons', 'externalPluginButtons');
    }

    public function validateUpdatedCustomButtons($saveStatusForBuiltInButtons) {
        if (!$saveStatusForBuiltInButtons) {
            return false;
        }

        $buttonRefData = $this->customButtonRefData->getRefData();

        if (isset($this->cleanRequest['buttonableCustomButtons'])) {
            foreach ($this->cleanRequest['buttonableCustomButtons'] as $handle=>$properties) {
                if (!array_key_exists($handle, $this->settings->customButtons)) {
                    return false;
                }

                foreach ($properties as $k=>$v) {
                    if (!$v || !array_key_exists($k, $buttonRefData)) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    public function saveUpdatedCustomButtons($validateStatusForCustomButtons) {
        if (!$validateStatusForCustomButtons) {
            return false;
        }

        $settings = array();
        $settings['customButtons'] = $this->settings->customButtons;

        if (isset($this->cleanRequest['buttonableCustomButtons'])) {
            foreach ($this->cleanRequest['buttonableCustomButtons'] as $handle=>$properties) {
                $settings['customButtons'][$handle] = $properties;
            }
        }

        $this->settings->set($settings);
        return $settings;
    }

    public function deleteCustomButtons($saveStatusForCustomButtons) {
        if (!$saveStatusForCustomButtons) {
            return false;
        }

        if (empty($this->cleanRequest['buttonableDeleteButton'])) {
            return array();
        }

        foreach ($this->cleanRequest['buttonableDeleteButton'] as $handle=>$delete) {
            $this->settings->purge(array('customButtons', $handle));
        }

        return $this->settings->customButtons;
    }
}
