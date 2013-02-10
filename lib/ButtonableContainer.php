<?php

class ButtonableContainer {
    private $functionsFacade;
    private $settings;
    private $installer;
    private $customButtonRefData;
    private $settingsMenuDisplayer;
    private $settingsMenuHandler;
    private $editorHandler;
    private $externalButtonHandler;

    public function __construct() {
    }

    public function getFunctionsFacade() {
        if (!$this->functionsFacade) {
            $this->functionsFacade = new ButtonableFunctionsFacade();
        }
        return $this->functionsFacade;
    }


    public function getSettings() {
        if (!$this->settings) {
            $this->getFunctionsFacade();
            $this->settings = new ButtonableSettings($this->functionsFacade);
        }

        return $this->settings;
    }

    public function getInstaller() {
        if (!$this->installer) {
            $this->getSettings();
            $this->getFunctionsFacade();
            $this->installer = new ButtonableInstall();
            $this->installer->setSettings($this->settings);
            $this->installer->setFunctionsFacade($this->functionsFacade);
        }

        return $this->installer;
    }

    public function getCustomButtonRefData() {
        if (!$this->customButtonRefData) {
            $this->customButtonRefData = new ButtonableCustomButtonRefData();
        }

        return $this->customButtonRefData;
    }

    public function getSettingsMenuDisplayer($startPath) {
        if (!$this->settingsMenuDisplayer) {
            $this->getSettings();
            $this->getCustomButtonRefData();
            $this->settingsMenuDisplayer = new ButtonableSettingsMenuDisplayer($startPath);
            $this->settingsMenuDisplayer->setSettings($this->settings);
            $this->settingsMenuDisplayer->setCustomButtonRefData($this->customButtonRefData);
        }

        return $this->settingsMenuDisplayer;
    }

    public function getSettingsMenuHandler($startPath) {
        if (!$this->settingsMenuHandler) {
            $this->getSettings();
            $this->getSettingsMenuDisplayer($startPath);
            $this->getCustomButtonRefData();
            $this->settingsMenuHandler = new ButtonableSettingsMenuHandler();
            $this->settingsMenuHandler->setRequest($_REQUEST);
            $this->settingsMenuHandler->setSettingsMenuDisplayer($this->settingsMenuDisplayer);
            $this->settingsMenuHandler->setSettings($this->settings);
            $this->settingsMenuHandler->setCustomButtonRefData($this->customButtonRefData);
        }

        return $this->settingsMenuHandler;
    }

    public function getEditorHandler($startPath) {
        if (!$this->editorHandler) {
            $this->getSettings();
            $this->getFunctionsFacade();
            $this->editorHandler = new ButtonableEditorHandler($startPath);
            $this->editorHandler->setSettings($this->settings);
            $this->editorHandler->setFunctionsFacade($this->functionsFacade);
            $this->editorHandler->setScriptName($_SERVER['SCRIPT_NAME']);
        }

        return $this->editorHandler;
    }

    public function getExternalButtonHandler() {
        if (!$this->externalButtonHandler) {
            $this->getSettings();
            $this->getFunctionsFacade();
            $this->externalButtonHandler = new ButtonableExternalButtonHandler();
            $this->externalButtonHandler->setSettings($this->settings);
            $this->externalButtonHandler->setFunctionsFacade($this->functionsFacade);
        }

        return $this->externalButtonHandler;
    }
}

