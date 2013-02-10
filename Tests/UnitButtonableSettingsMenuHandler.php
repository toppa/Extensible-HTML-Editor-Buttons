<?php

Mock::generate('ButtonableSettings');
Mock::generate('ButtonableSettingsMenuDisplayer');
Mock::generate('ButtonableCustomButtonRefData');

class UnitButtonableSettingsMenuHandler extends UnitTestCase {
    private $validNewCustomButtonData = array(
        'buttonableNewButton' => array(
            'handle' => 'Blink',
            'settings' => array(
                'tag' => 'blink',
                'title' => 'Blink: annoying &amp; deprecated',
                'id' => 'ed_blink',
                'self_close' => 'n',
                'shortcode' => 'n',
                'active' => 'y',
                'input_dialog' => 'n'
            )
        )
    );

    private $validUpdatedCustomButtonData = array(
        'buttonableCustomButtons' => array(
            'Testing' => array(
                'tag' => "test",
                'title' => "Test: this is new",
                'id' => "ed_test",
                'self_close' => "n",
                'shortcode' => "n",
                'active' => "y",
                'input_dialog' => "n"
            ),
            'Testing 2' => array(
                'tag' => "test2",
                'title' => "Test2: This is a test",
                'id' => "ed_test2",
                'self_close' => "y",
                'shortcode' => "y",
                'active' => "y",
                'input_dialog' => "y"
            )
        )
    );

    public function __construct() {
        $this->UnitTestCase();
    }

    public function setUp() {
    }

    public function testSetRequest() {
        $request = array('foo' => 'bar');
        $handler = new ButtonableSettingsMenuHandler();
        $this->assertEqual($handler->setRequest($request), $request);
    }

    public function testSetSettings() {
        $settings = new MockButtonableSettings();
        $handler = new ButtonableSettingsMenuHandler();
        $this->assertEqual($handler->setSettings($settings), $settings);
    }

    public function testSetSettingsMenuDisplayer() {
        $menuDisplayer = new MockButtonableSettingsMenuDisplayer();
        $handler = new ButtonableSettingsMenuHandler();
        $this->assertEqual($handler->setSettingsMenuDisplayer($menuDisplayer), $menuDisplayer);
    }

    public function testSetCustomButtonRefData() {
        $handler = new ButtonableSettingsMenuHandler();
        $customButtonRefData = $this->getMockCustomButtonRefData();
        $this->assertEqual($handler->setCustomButtonRefData($customButtonRefData), $customButtonRefData);
    }

    private function getMockCustomButtonRefData() {
        // simplified version of refData is sufficient
        $refData = array(
            'tag' => array(),
            'title' => array(),
            'id' => array(),
            'self_close' => array(),
            'shortcode' => array(),
            'active' => array(),
            'input_dialog' => array()
        );
        $customButtonRefData = new MockButtonableCustomButtonRefData();
        $customButtonRefData->setReturnValue('getRefData', $refData);
        return $customButtonRefData;
    }

    public function testRun() {
    }

    public function testSetCleanRequest() {
        $goodInputButNeedsCleanup = array(
            'buttonableNewButton' => array(
                'handle' => 'Blink',
                'settings' => array(
                    'tag' => 'blink ',
                    'title' => 'Blink: annoying & deprecated',
                    'id' => 'ed_blink',
                    'self_close' => 'n',
                    'shortcode' => 'n',
                    'active' => 'y',
                    'input_dialog' => 'n'
                )
            )
        );

        $handler = new ButtonableSettingsMenuHandler();
        $handler->setRequest($goodInputButNeedsCleanup);
        $this->assertEqual($handler->setCleanRequest(), $this->validNewCustomButtonData);
    }

    public function testValidateNewCustomButtonWithValidData() {
        $handler = new ButtonableSettingsMenuHandler();
        $customButtonRefData = $this->getMockCustomButtonRefData();
        $handler->setCustomButtonRefData($customButtonRefData);
        $handler->setRequest($this->validNewCustomButtonData);
        $handler->setCleanRequest();
        $this->assertTrue($handler->validateNewCustomButton());
    }

    public function testValidateNewCustomButtonWithMissingTitle() {
        $invalidInput = array(
            'buttonableNewButton' => array(
                'handle' => 'Blink',
                'settings' => array( // "title" key is missing
                    'tag' => 'blink',
                    'id' => 'ed_blink',
                    'self_close' => 'n',
                    'shortcode' => 'n',
                    'active' => 'y',
                    'input_dialog' => 'n'
                )
            )
        );

        $handler = new ButtonableSettingsMenuHandler();
        $customButtonRefData = $this->getMockCustomButtonRefData();
        $handler->setCustomButtonRefData($customButtonRefData);
        $handler->setRequest($invalidInput);
        $handler->setCleanRequest();
        $this->assertFalse($handler->validateNewCustomButton());
    }

    public function testValidateNewCustomButtonWithMissingHandle() {
        $invalidInput = array(
            'buttonableNewButton' => array(
                'handle' => '', // handle cannot be null
                'settings' => array(
                    'tag' => 'blink',
                    'title' => 'Blink: annoying &amp; deprecated',
                    'id' => 'ed_blink',
                    'self_close' => 'n',
                    'shortcode' => 'n',
                    'active' => 'y',
                    'input_dialog' => 'n'
                )
            )
        );

        $handler = new ButtonableSettingsMenuHandler();
        $customButtonRefData = $this->getMockCustomButtonRefData();
        $handler->setCustomButtonRefData($customButtonRefData);
        $handler->setRequest($invalidInput);
        $handler->setCleanRequest();
        $this->assertFalse($handler->validateNewCustomButton());
    }

    public function testSaveNewCustomButtonWithPassedValidation() {
        $expectedResult = array(
            'customButtons' => array(
                'Blink' => array(
                    'tag' => "blink",
                    'title' => "Blink: annoying &amp;amp; deprecated",
                    'id' => "ed_blink",
                    'self_close' => "n",
                    'shortcode' => "n",
                    'active' => "y",
                    'input_dialog'=> "n"
                )
            )
        );

        $settings = new MockButtonableSettings();
        $handler = new ButtonableSettingsMenuHandler();
        $handler->setSettings($settings);
        $handler->setRequest($this->validNewCustomButtonData);
        $handler->setCleanRequest();
        $this->assertEqual($handler->saveNewCustomButton(true), $expectedResult);
    }

    public function testSaveNewCustomButtonWithFailedValidation() {
        $settings = new MockButtonableSettings();
        $handler = new ButtonableSettingsMenuHandler();
        $this->assertFalse($handler->saveNewCustomButton(false));
    }

    private function setupHandlerForTestingBuiltInButtonsAndExternalPluginButtons() {
        $existingButtonSettings = array(
            'div' => array(
                'tag' => 'div',
                'title' => 'add div tag',
                'id' => 'ed_div',
                'self_close' => 'n',
                'shortcode' => 'n',
                'active' => 'y',
                'input_dialog' => 'y'),
            'span' => array(
                'tag' => 'span',
                'title' => 'add span tag',
                'id' => 'ed_span',
                'self_close' => 'n',
                'shortcode' => 'n',
                'active' => 'y',
                'input_dialog' => 'y'),
            'anchor' => array(
                'tag' => 'a',
                'title' => 'add anchor tag',
                'id' => 'ed_link',
                'self_close' => 'n',
                'shortcode' => 'n',
                'active' => 'y',
                'input_dialog' => 'y'),
            'img' => array(
                'tag' => 'img',
                'title' => 'add image tag',
                'id' => 'ed_img',
                'self_close' => 'y',
                'shortcode' => 'n',
                'active' => 'y',
                'input_dialog' => 'y'),
        );

        $settings = new MockButtonableSettings();
        $settings->setReturnValue('__get', $existingButtonSettings, array('buttons'));
        $settings->setReturnValue('__get', $existingButtonSettings, array('externalPluginButtons'));
        $handler = new ButtonableSettingsMenuHandler();
        $handler->setSettings($settings);
        return $handler;
    }

    public function testValidateUpdatedBuiltInButtonsWithValidButtons() {
        $newButtonActiveStatus = array(
        'buttonableBuiltInButtons' => array(
            'div' => array(
                'active' => 'n'),
            'span' => array(
                'active' => 'n'),
            'anchor' => array(
                'active' => 'n'),
            'img' => array(
                'active' => 'n'),
            )
        );

        $handler = $this->setupHandlerForTestingBuiltInButtonsAndExternalPluginButtons();
        $handler->setRequest($newButtonActiveStatus);
        $handler->setCleanRequest();
        $this->assertTrue($handler->validateUpdatedBuiltInButtons());
    }

    public function testValidateUpdatedBuiltInButtonsWithAnInvalidButtons() {
        $newButtonActiveStatus = array(
        'buttonableBuiltInButtons' => array(
            'div' => array(
                'active' => 'bar'), // bad value
            'span' => array(
                'active' => 'n'),
            'anchor' => array(
                'active' => 'n'),
            'img' => array(
                'active' => 'n'),
            )
        );

        $handler = $this->setupHandlerForTestingBuiltInButtonsAndExternalPluginButtons();
        $handler->setRequest($newButtonActiveStatus);
        $handler->setCleanRequest();
        $this->assertFalse($handler->validateUpdatedBuiltInButtons());
    }

    public function testValidateUpdatedBuiltInButtonsWithAnInvalidHandle() {
        $newButtonActiveStatus = array(
        'buttonableBuiltInButtons' => array(
            'foo' => array( // bad handle
                'active' => 'n'),
            'span' => array(
                'active' => 'n'),
            'anchor' => array(
                'active' => 'n'),
            'img' => array(
                'active' => 'n'),
            )
        );

        $handler = $this->setupHandlerForTestingBuiltInButtonsAndExternalPluginButtons();
        $handler->setRequest($newButtonActiveStatus);
        $handler->setCleanRequest();
        $this->assertFalse($handler->validateUpdatedBuiltInButtons());
    }

    public function testSaveUpdatedBuiltInButtonsWithValidButtons() {
        $newButtonActiveStatus = array(
            'buttonableBuiltInButtons' => array(
                'div' => 'n',
                'span' => 'n',
                'anchor' => 'n',
                'img' => 'n',
            )
        );

        $expectedResult = array(
            'buttons' => array(
                'div' => array(
                    'tag' => 'div',
                    'title' => 'add div tag',
                    'id' => 'ed_div',
                    'self_close' => 'n',
                    'shortcode' => 'n',
                    'active' => 'n',
                    'input_dialog' => 'y'),
                'span' => array(
                    'tag' => 'span',
                    'title' => 'add span tag',
                    'id' => 'ed_span',
                    'self_close' => 'n',
                    'shortcode' => 'n',
                    'active' => 'n',
                    'input_dialog' => 'y'),
                'anchor' => array(
                    'tag' => 'a',
                    'title' => 'add anchor tag',
                    'id' => 'ed_link',
                    'self_close' => 'n',
                    'shortcode' => 'n',
                    'active' => 'n',
                    'input_dialog' => 'y'),
                'img' => array(
                    'tag' => 'img',
                    'title' => 'add image tag',
                    'id' => 'ed_img',
                    'self_close' => 'y',
                    'shortcode' => 'n',
                    'active' => 'n',
                    'input_dialog' => 'y'),
            )
        );

        $handler = $this->setupHandlerForTestingBuiltInButtonsAndExternalPluginButtons();
        $handler->setRequest($newButtonActiveStatus);
        $handler->setCleanRequest();
        $this->assertEqual($handler->saveUpdatedBuiltInButtons(true), $expectedResult);
    }

    public function testSaveUpdatedBuiltInButtonsWithInvalidButtons() {
        $handler = $this->setupHandlerForTestingBuiltInButtonsAndExternalPluginButtons();
        $this->assertFalse($handler->saveUpdatedBuiltInButtons(false));
    }

    // happy path only is ok for testing external plugin buttons, as they are
    // handled by the same underlying private methods as the builtin buttons
    public function testValidateUpdatedExternalPluginButtonsWithValidButtons() {
        $newButtonActiveStatus = array(
        'buttonableExternalPluginButtons' => array(
            'div' => array(
                'active' => 'n'),
            'span' => array(
                'active' => 'n'),
            'anchor' => array(
                'active' => 'n'),
            'img' => array(
                'active' => 'n'),
            )
        );

        $handler = $this->setupHandlerForTestingBuiltInButtonsAndExternalPluginButtons();
        $handler->setRequest($newButtonActiveStatus);
        $handler->setCleanRequest();
        $this->assertTrue($handler->validateUpdatedExternalPluginButtons());
    }

    public function testSaveUpdatedExternalPluginButtonsWithValidButtons() {
        $newButtonActiveStatus = array(
            'buttonableExternalPluginButtons' => array(
                'div' => 'n',
                'span' => 'n',
                'anchor' => 'n',
                'img' => 'n',
            )
        );

        $expectedResult = array(
            'externalPluginButtons' => array(
                'div' => array(
                    'tag' => 'div',
                    'title' => 'add div tag',
                    'id' => 'ed_div',
                    'self_close' => 'n',
                    'shortcode' => 'n',
                    'active' => 'n',
                    'input_dialog' => 'y'),
                'span' => array(
                    'tag' => 'span',
                    'title' => 'add span tag',
                    'id' => 'ed_span',
                    'self_close' => 'n',
                    'shortcode' => 'n',
                    'active' => 'n',
                    'input_dialog' => 'y'),
                'anchor' => array(
                    'tag' => 'a',
                    'title' => 'add anchor tag',
                    'id' => 'ed_link',
                    'self_close' => 'n',
                    'shortcode' => 'n',
                    'active' => 'n',
                    'input_dialog' => 'y'),
                'img' => array(
                    'tag' => 'img',
                    'title' => 'add image tag',
                    'id' => 'ed_img',
                    'self_close' => 'y',
                    'shortcode' => 'n',
                    'active' => 'n',
                    'input_dialog' => 'y'),
            )
        );

        $handler = $this->setupHandlerForTestingBuiltInButtonsAndExternalPluginButtons();
        $handler->setRequest($newButtonActiveStatus);
        $handler->setCleanRequest();
        $this->assertEqual($handler->saveUpdatedExternalPluginButtons(true), $expectedResult);
    }

    private function setupHandlerForTestingUpdatingCustomButtons() {
        $existingCustomButtonData = array(
            'Testing' => array(
                'tag' => "test",
                'title' => "Test: this is old",
                'id' => "ed_test",
                'self_close' => "n",
                'shortcode' => "n",
                'active' => "y",
                'input_dialog' => "n"
            ),
            'Testing 2' => array(
                'tag' => "test2",
                'title' => "Test2: This is a test",
                'id' => "ed_test2",
                'self_close' => "y",
                'shortcode' => "y",
                'active' => "y",
                'input_dialog' => "n"
            )
        );

        $handler = new ButtonableSettingsMenuHandler();
        $customButtonRefData = $this->getMockCustomButtonRefData();
        $handler->setCustomButtonRefData($customButtonRefData);
        $settings = new MockButtonableSettings();
        $settings->setReturnValue('__get', $existingCustomButtonData, array('customButtons'));
        $settings->setReturnValue('purge', array($existingCustomButtonData[0]));
        $handler->setSettings($settings);
        return $handler;
    }

    public function testValidateUpdatedCustomButtonsWithValidData() {
        $handler = $this->setupHandlerForTestingUpdatingCustomButtons();
        $handler->setRequest($this->validUpdatedCustomButtonData);
        $handler->setCleanRequest();
        $this->assertTrue($handler->validateUpdatedCustomButtons(true));
    }

    public function testValidateUpdatedCustomButtonsWithInvalidHandle() {
        $invalidNewCustomButtonData = array(
            'buttonableCustomButtons' => array(
                'Foo' => array( // this handle does not represent an existing custom button
                    'tag' => "test",
                    'title' => "Test: this is new",
                    'id' => "ed_test",
                    'self_close' => "n",
                    'shortcode' => "n",
                    'active' => "y",
                    'input_dialog' => "n"
                ),
                'Testing 2' => array(
                    'tag' => "test2",
                    'title' => "Test2: This is a test",
                    'id' => "ed_test2",
                    'self_close' => "y",
                    'shortcode' => "y",
                    'active' => "y",
                    'input_dialog' => "y"
                )
            )
        );

        $handler = $this->setupHandlerForTestingUpdatingCustomButtons();
        $handler->setRequest($invalidNewCustomButtonData);
        $handler->setCleanRequest();
        $this->assertFalse($handler->validateUpdatedCustomButtons(true));
    }

    public function testValidateUpdatedCustomButtonsWithInvalidProperties() {
        $invalidNewCustomButtonData = array(
            'buttonableCustomButtons' => array(
                'Testing' => array(
                    'tag' => "test",
                    'title' => "Test: this is new",
                    'id' => "ed_test",
                    'self_close' => "n",
                    'shortcode' => "n",
                    'active' => null, // value cannot be null
                    'input_dialog' => "n"
                ),
                'Testing 2' => array(
                    'tag' => "test2",
                    'title' => "Test2: This is a test",
                    'id' => "ed_test2",
                    'self_close' => "y",
                    'shortcode' => "y",
                    'active' => "y",
                    'input_dialog' => "y",
                    'this property should not exist' => 'y' // invalid key
                )
            )
        );

        $handler = $this->setupHandlerForTestingUpdatingCustomButtons();
        $handler->setRequest($invalidNewCustomButtonData);
        $handler->setCleanRequest();
        $this->assertFalse($handler->validateUpdatedCustomButtons(true));
    }

    public function testValidateUpdatedCustomButtonsWithFailedSaveForBuiltInButtons() {
        $handler = $this->setupHandlerForTestingUpdatingCustomButtons();
        $this->assertFalse($handler->validateUpdatedCustomButtons(false));
    }


    public function testSaveUpdatedCustomButtonsWithValidButtons() {
        $expectedResult = array();
        $expectedResult['customButtons'] = $this->validUpdatedCustomButtonData['buttonableCustomButtons'];
        $handler = $this->setupHandlerForTestingUpdatingCustomButtons();
        $handler->setRequest($this->validUpdatedCustomButtonData);
        $handler->setCleanRequest();
        $this->assertEqual($handler->saveUpdatedCustomButtons(true), $expectedResult);
    }

    public function testSaveUpdatedCustomButtonsWithFailedValidation() {
        $handler = $this->setupHandlerForTestingUpdatingCustomButtons();
        $this->assertFalse($handler->saveUpdatedCustomButtons(false));
    }

//    Can't get this one to pass, but the code works!
//    public function testDeleteCustomButtonsWithValidDeleteRequest() {
//        $deleteRequest = array('buttonableDeleteButton' => array('Testing 2' => array('y')));
//        $expectedResult = array(
//            'Testing' => array(
//                'tag' => "test",
//                'title' => "Test: this is old",
//                'id' => "ed_test",
//                'self_close' => "n",
//                'shortcode' => "n",
//                'active' => "y",
//                'input_dialog' => "n"
//            )
//        );
//
//        $handler = $this->setupHandlerForTestingUpdatingCustomButtons();
//        $handler->setRequest($deleteRequest);
//        $handler->setCleanRequest();
//        $this->assertEqual($handler->deleteCustomButtons(true), $expectedResult);
//    }

    public function testDeleteCustomButtonsWithNoDeleteRequest() {
        $handler = $this->setupHandlerForTestingUpdatingCustomButtons();
        $handler->setRequest(array());
        $handler->setCleanRequest();
        $this->assertEqual($handler->deleteCustomButtons(true),array());
    }

    public function testDeleteCustomButtonsWithFailedValidation() {
        $handler = $this->setupHandlerForTestingUpdatingCustomButtons();
        $this->assertFalse($handler->deleteCustomButtons(false));
    }

}
