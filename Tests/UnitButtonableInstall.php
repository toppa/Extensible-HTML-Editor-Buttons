<?php

Mock::generate('ButtonableSettings');

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
        $installer = new ButtonableInstall();
        $installer->setSettings($settings);
        $this->assertTrue(is_array($installer->run()));
    }
}
