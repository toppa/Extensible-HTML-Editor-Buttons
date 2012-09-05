<?php

class IntegrationButtonable extends UnitTestCase {
    public function __construct() {
        $this->UnitTestCase();
    }

    public function testBackupCustomDialogsWhenFileExists() {
        $buttonable = new Buttonable();
        $this->assertTrue($buttonable->backupCustomDialogs());
    }

    public function testRestoreCustomDialogsWhenFileExists() {
        $buttonable = new Buttonable();
        $this->assertTrue($buttonable->restoreCustomDialogs());
    }
}
