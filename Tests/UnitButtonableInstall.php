<?php

Mock::generate('ButtonableSettings');
Mock::generate('ToppaFunctionsFacadeWp');

class UnitButtonableInstall extends UnitTestCase {
    public function __construct() {
        $this->UnitTestCase();
    }

    public function setUp() {
    }

    public function testSetSettings() {
        $settings = new MockButtonableSettings();
        $installer = new ButtonableInstall();
        $this->assertEqual($installer->setSettings($settings), $settings);
    }

    public function testRun() {
        $settings = new MockButtonableSettings();
        $settings->setReturnValue('set', array());
        $functionsFacade = new MockToppaFunctionsFacadeWp();
        $functionsFacade->setReturnValue('callFunctionForNetworkSites', true);
        $installer = new ButtonableInstall();
        $installer->setSettings($settings);
        $installer->setFunctionsFacade($functionsFacade);
        $this->assertTrue($installer->run());
    }
}
