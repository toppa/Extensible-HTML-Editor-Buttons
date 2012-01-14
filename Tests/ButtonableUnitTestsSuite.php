<?php

// this is needed for simpletest's addFile method
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));

$buttonableTestsAutoLoaderPath = dirname(__FILE__) . '/../../toppa-plugin-libraries-for-wordpress/ToppaAutoLoaderWp.php';

if (file_exists($buttonableTestsAutoLoaderPath)) {
    require_once($buttonableTestsAutoLoaderPath);
    $buttonableTestsToppaAutoLoader = new ToppaAutoLoaderWp('/toppa-plugin-libraries-for-wordpress');
    $buttonableTestsAutoLoader = new ToppaAutoLoaderWp('/extensible-html-editor-buttons');
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



