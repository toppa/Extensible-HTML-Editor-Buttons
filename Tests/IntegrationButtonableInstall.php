<?php

class IntegrationButtonableInstall extends UnitTestCase {
    private $settings;

    public function __construct() {
        $this->UnitTestCase();
    }

    public function setUp() {
        $functionsFacade = new ToppaFunctionsFacadeWp();
        $this->settings = new ButtonableSettings($functionsFacade);
    }

    public function testSetSettings() {
        $installer = new ButtonableInstall();
        $this->assertEqual($installer->setSettings($this->settings), $this->settings);
    }

    public function testRun() {
        $installer = new ButtonableInstall();
        $installer->setSettings($this->settings);
        $this->assertTrue(is_array($installer->run()));
    }
}
