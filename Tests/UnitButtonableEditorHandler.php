<?php

Mock::generate('ButtonableSettings');
Mock::generate('ToppaFunctionsFacadeWp');

class UnitButtonableEditorHandler extends UnitTestCase {
    public function __construct() {
        $this->UnitTestCase();
    }

    public function setUp() {
    }

    public function testSetSettings() {
        $settings = new MockButtonableSettings();
        $handler = new ButtonableEditorHandler();
        $this->assertEqual($handler->setSettings($settings), $settings);
    }

    public function testSetFunctionsFacade() {
        $functionsFacade = new MockToppaFunctionsFacadeWp();
        $handler = new ButtonableEditorHandler();
        $this->assertEqual($handler->setFunctionsFacade($functionsFacade), $functionsFacade);
    }

    public function testSetScriptName() {
        $handler = new ButtonableEditorHandler();
        $this->assertEqual($handler->setScriptName('/wordpress/wp-admin/post.php'), '/wordpress/wp-admin/post.php');
    }

    public function testAddButtonsWhenNotOnEditorPage() {
        $handler = new ButtonableEditorHandler();
        $handler->setScriptName('/wordpress/wp-admin/edit.php');
        $this->assertNull($handler->addButtons());
    }

    // the WP enqueue methods never return anything, so there's no good way to test this
    public function testEnqueueScriptsAndStylesheets() {
        $handler = new ButtonableEditorHandler();
        $functionsFacade = new MockToppaFunctionsFacadeWp();
        $handler->setFunctionsFacade($functionsFacade);
        $handler->setScriptName('/wordpress/wp-admin/edit.php');
        $this->assertTrue($handler->enqueueScriptsAndStylesheets());
    }

    public function testLocalizeButtonableJs() {
        $handler = new ButtonableEditorHandler();
        $settings = new MockButtonableSettings();
        $buttons = array(
            'div' => array(
                'tag' => "div",
                'title' => "add div tag",
                'id' => "ed_div",
                'self_close' => "n",
                'shortcode' => "n",
                'active' => "y",
                'input_dialog' => "y"
            ),
            'span' => array(
                'tag' => "span",
                'title' => "add span tag",
                'id' => "ed_span",
                'self_close' => "n",
                'shortcode' => "n",
                'active' => "y",
                'input_dialog' => "y"
            )
        );

        $settings->setReturnValue('__get', $buttons, array('buttons'));
        $handler->setSettings($settings);
        $functionsFacade = new MockToppaFunctionsFacadeWp();
        $handler->setFunctionsFacade($functionsFacade);
        $expectedResult = array(
            'handles' => "div,span",
            'tags' => "div,span",
            'titles' => "add div tag,add span tag",
            'ids' => "ed_div,ed_span",
            'selfClose' => "n,n",
            'shortcodes' => "n,n",
            'inputDialogs' => "y,y"
        );
        $this->assertEqual($handler->localizeButtonableJs(), $expectedResult);
    }

    private function setupHandlerForFileSystemMocking() {
        $handler = new ButtonableEditorHandler();
        $functionsFacade = new MockToppaFunctionsFacadeWp();
        $functionsFacade->setReturnValue('directoryName', '/some/path/to');
        $functionsFacade->setReturnValue('requireOnce', 1);
        $functionsFacade->setReturnValue('checkFileExists', true);
        $handler->setFunctionsFacade($functionsFacade);
        $fakeDir = '/some/path/to/file.php';
        $handler->setDisplayDir($fakeDir);
        return $handler;
    }

    public function testSetDisplayDir() {
        $handler = $this->setupHandlerForFileSystemMocking();
        $expectedResult = '/some/path/to/Display/';
        $this->assertEqual($handler->getDisplayDir(), $expectedResult);
    }

    public function testIncludeDialogs() {
        $handler = $this->setupHandlerForFileSystemMocking();
        $this->assertEqual($handler->includeDialogs(), 1);
    }

    public function testIncludeCustomDialogs() {
        $handler = $this->setupHandlerForFileSystemMocking();
        $this->assertEqual($handler->includeCustomDialogs(), 1);
    }

    public function testIncludeExternalDialogs() {
        $handler = $this->setupHandlerForFileSystemMocking();
        $this->assertEqual($handler->includeExternalDialogs(), true);
    }

}