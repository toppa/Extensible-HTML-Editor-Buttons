<?php

class ButtonableCustomButtonRefData {
    private $refData;

    public function __construct() {
        $this->refData = array(
            'tag' => array(
                'type' => 'text',
                'name' => __('Tag', 'buttonable'),
                'help'=> __('The tag your button will insert, not including delimiters - do not include &lt; &gt; or [ ] - example: "blink"', 'buttonable'),
                'default' => ''
            ),
            'title' => array(
                'type' => 'text',
                'name' => __('Title', 'buttonable'),
                'help'=> __('Title text for your button; appears as mouseover help text in most browsers - example: "For really annoying blinking text"', 'buttonable'),
                'default' => ''
            ),
            'id' => array(
                'type' => 'text',
                'name' => __('ID', 'buttonable'),
                'help'=> __('The id of your button. This should start with "ed_" - example: "ed_blink"', 'buttonable'),
                'default' => 'ed_'
            ),
            'self_close' => array(
                'type' => 'radio',
                'options' => array('y' => __('Yes', 'buttonable'), 'n' => __('No', 'buttonable')),
                'name' => __('Self closing tag', 'buttonable'),
                'help'=> __('Whether your tag self closes (like an &lt;img&gt; tag) or has a separate closing tag (like a &lt;/p&gt; tag)', 'buttonable'),
                'default' => 'y'
            ),
            'shortcode' => array(
                'type' => 'radio',
                'options' => array('y' => __('Shortcode', 'buttonable'), 'n' => __('HTML', 'buttonable')),
                'name' => __('Tag type', 'buttonable'),
                'help'=> __('Whether your tag is a shortcode tag with [ ] delimiters, or an HTML tag with &lt; &gt; delimiters', 'buttonable'),
                'default' => 'n'
            ),
            'active' => array(
                'type' => 'radio',
                'options' => array('y' => __('Yes', 'buttonable'), 'n' => __('No', 'buttonable')),
                'name' => __('Enabled', 'buttonable'),
                'help'=> __('Whether your button is enabled in the button bar, or disabled and not shown in the button bar', 'buttonable'),
                'default' => 'y'
            ),
            'input_dialog' => array(
                'type' => 'radio',
                'options' => array('y' => __('Yes', 'buttonable'), 'n' => __('No', 'buttonable')),
                'name' => __('Custom Dialog', 'buttonable'),
                'help'=> __('Select "yes" to enable a custom input dialog for your button. The dialog is where you can accept user input for your tag\'s attributes. Example: for a blink tag, your form dialog would accept a value for blink\'s "rate" attribute. All custom dialogs belong in your plugin directory, in the file buttonable/display/custom-dialogs.html (further instructions are in custom-dialogs-example.html).', 'buttonable'),
                'default' => 'n'
            ),
        );
    }

    public function getRefData() {
        return $this->refData;
    }
}
