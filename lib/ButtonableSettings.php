<?php

class ButtonableSettings {
    private $functionsFacade;
    private $name = 'buttonable';
    private $data = array();

    public function __construct(ButtonableFunctionsFacade $functionsFacade) {
        $this->functionsFacade = $functionsFacade;
    }

    public function __get($name) {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        else {
            $this->refresh();
        }

        // and try again...
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        throw New Exception(__('Invalid data property __get for ', 'buttonable') . htmlentities($name));
    }

    public function refresh() {
        $currentSettings = $this->functionsFacade->getSetting($this->name);

        if (is_array($currentSettings)) {
            $this->data = $currentSettings;
        }

        return $this->data;
    }

    public function set(array $newSettings, $preferExisting = false) {
        $this->refresh();

        if ($preferExisting) {
            $this->data = ButtonableFunctions::arrayMergeRecursiveForSettings($newSettings, $this->data);
        }

        else {
            $this->data = ButtonableFunctions::arrayMergeRecursiveForSettings($this->data, $newSettings);
        }

        $this->functionsFacade->setSetting($this->name, $this->data);
        return $this->data;
    }

    public function delete() {
        $this->functionsFacade->deleteSetting($this->name);

        if ($this->functionsFacade->getSetting($this->name)) {
            throw new Exception(__('Failed to delete settings', 'buttonable'));
        }

        return true;
    }

    // the set method merges old and new settings, which means you can't
    // use it to delete settings data. Purge will remove the
    // specified settings element, up to 3 levels deep
    public function purge(array $settingToRemove) {
        $this->refresh();

        if (count($settingToRemove) == 1) {
            unset($this->data[$settingToRemove[0]]);
        }
        elseif (count($settingToRemove) == 2) {
            unset($this->data[$settingToRemove[0]][$settingToRemove[1]]);
        }
        elseif (count($settingToRemove) == 3) {
            unset($this->data[$settingToRemove[0]][$settingToRemove[1]][$settingToRemove[2]]);
        }
        else {
            throw new Exception(__('Failed to purge settings. Unsupport argument provided.', 'buttonable'));
        }

        $this->functionsFacade->setSetting($this->name, $this->data);
        return $this->data;
    }
}


