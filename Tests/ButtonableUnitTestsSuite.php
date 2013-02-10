<?php

// this is needed for simpletest's addFile method
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));

$buttonableTestsAutoLoaderPath = dirname(__FILE__) . '/../lib/ButtonableAutoLoaderWp.php';

if (file_exists($buttonableTestsAutoLoaderPath)) {
    require_once($buttonableTestsAutoLoaderPath);
    $buttonableTestsAutoLoader = new ButtonableAutoLoader('/extensible-html-editor-buttons/lib');
}

class ButtonableUnitTestsSuite extends TestSuite {
   function __construct() {
       parent::__construct();
       $this->addFile('UnitButtonableCustomButtonRefData.php');
       $this->addFile('UnitButtonableEditorHandler.php');
       $this->addFile('UnitButtonableSettings.php');
       $this->addFile('UnitButtonableInstall.php');
       $this->addFile('UnitButtonableSettingsMenuHandler.php');
   }
}



