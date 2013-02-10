<?php

Mock::generate('ButtonableFunctionsFacade');

class UnitButtonableSettings extends UnitTestCase {
    private $functionsFacade;
    private $userSettings = array(
        'buttons' => array(
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
        ),
        'customButtons' => array('article' =>
            array(
                'title' => 'Article tag',
                'id' => 'ed_article',
                'selfClose' => 'n',
                'shortcode' => 'n',
                'active' => 'y',
                'inputDialog' => 'n'
            )
        ),
        'externalPluginButtons' => array()
    );

    public function __construct() {
        $this->UnitTestCase();
    }

    public function setUp() {
        $this->functionsFacade = new MockButtonableFunctionsFacade();
        $this->functionsFacade->setReturnValue('getSetting', $this->userSettings);
    }

    public function testMagicGetWithValidKey() {
        $settings = new ButtonableSettings($this->functionsFacade);
        $this->assertEqual($settings->buttons, $this->userSettings['buttons']);
    }

    public function testMagicGetWithInvalidKey() {
        try {
            $settings = new ButtonableSettings($this->functionsFacade);
            $settings->foobar;
        }

        catch (Exception $e) {
            $this->pass('Received expected exception');
        }
    }

    public function testRefresh() {
        $settings = new ButtonableSettings($this->functionsFacade);
        $this->assertEqual($settings->refresh(), $this->userSettings);
    }

    public function testSetWithPreferNew() {
        $settings = new ButtonableSettings($this->functionsFacade);
        $customButtons = array('customButtons' =>
            array('article' =>
                array(
                    'title' => 'This article tag should replace the existing one',
                    'id' => 'ed_article',
                    'selfClose' => 'n',
                    'shortcode' => 'n',
                    'active' => 'n',
                    'inputDialog' => 'n'
                )
            )
        );
        $updatedUserSettings = $settings->set($customButtons);
        $this->assertEqual($updatedUserSettings, array_merge($this->userSettings, $customButtons));
        $this->assertEqual($updatedUserSettings['customButtons']['article'], $customButtons['customButtons']['article']);
    }

    public function testSetWithPreferExisting() {
        $settings = new ButtonableSettings($this->functionsFacade);
        $customButtons = array('customButtons' =>
            array('article' =>
                array(
                    'title' => 'This article tag should not replace the existing one',
                    'id' => 'ed_article',
                    'selfClose' => 'n',
                    'shortcode' => 'n',
                    'active' => 'y',
                    'inputDialog' => 'n'
                )
            )
        );
        $updatedUserSettings = $settings->set($customButtons, true);
        $this->assertEqual($updatedUserSettings, array_merge($customButtons, $this->userSettings));
        $this->assertNotEqual($updatedUserSettings['customButtons']['article'], $customButtons['customButtons']['article']);
    }

    public function testDeleteSucceeded() {
        $functionsFacade = new MockButtonableFunctionsFacade();
        $functionsFacade->setReturnValue('getSetting', null);
        $settings = new ButtonableSettings($functionsFacade);
        $this->assertTrue($settings->delete());
    }

    public function testDeleteFailed() {
        try {
            $settings = new ButtonableSettings($this->functionsFacade);
            $settings->delete();
        }

        catch (Exception $e) {
            $this->pass('Received expected exception');
        }
    }
}
