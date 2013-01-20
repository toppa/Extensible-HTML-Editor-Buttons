<?php

class ButtonableEditorHandler {
    private $settings;
    private $functionsFacade;
    private $scriptName;
    private $pages = array('post-new.php', 'page-new.php', 'post.php', 'page.php', 'comments.php');
    private $displayDir;

    public function __construct() {
    }

    public function setSettings(ButtonableSettings $settings) {
        $this->settings = $settings;
        return $this->settings;
    }

    public function setFunctionsFacade(ToppaFunctionsFacade $functionsFacade) {
        $this->functionsFacade = $functionsFacade;
        return $this->functionsFacade;
    }

    public function setScriptName($scriptName) {
        $this->scriptName = $scriptName;
        return $this->scriptName;
    }

    public function hideInactiveElements() {
        if (!in_array(basename($_SERVER['SCRIPT_NAME']), $this->pages) ) {
            return null;
        }

        $style = '<style type="text/css">' . PHP_EOL;

        if ($this->settings->buttons['anchor']['active'] == 'y') {
            $style .= '#qt_content_link { display: none; }' . PHP_EOL;
        }

        else {
            $style .= '#buttonable_anchor_dialog { display: none; }' . PHP_EOL;
        }

        if ($this->settings->buttons['image']['active'] == 'y') {
            $style .= '#qt_content_img { display: none; }' . PHP_EOL;
        }

        else {
            $style .= '#buttonable_image_dialog { display: none; }' . PHP_EOL;
        }

        if ($this->settings->buttons['div']['active'] == 'n') {
            $style .= '#buttonable_div_dialog { display: none; }' . PHP_EOL;
        }

        if ($this->settings->buttons['span']['active'] == 'n') {
            $style .= '#buttonable_span_dialog { display: none; }' . PHP_EOL;
        }

        $style .= '</style>' . PHP_EOL;
        return $style;
    }

    public function addButtons() {
        if (!in_array(basename($this->scriptName), $this->pages) ) {
            return null;
        }

        $this->enqueueScriptsAndStylesheets();
        $this->localizeButtonableJs();
        $this->setDisplayDir(__FILE__);
        $this->includeDialogs();
        $this->includeCustomDialogs();
        $this->includeExternalDialogs();
    }

    public function enqueueScriptsAndStylesheets() {
        $displayUrl = $this->functionsFacade->getPluginsUrl('/Display/', __FILE__);
        $this->functionsFacade->enqueueScript(
            'buttonableJs',
            $displayUrl . 'buttonController.js',
            array(
                'jquery',
                'jquery-form',
                'jquery-ui-dialog',
                'jquery-ui-draggable',
                'quicktags'),
            false
        );
        $this->functionsFacade->enqueueStylesheet('buttonableStyle', $displayUrl . 'buttonable.css');
        return true;
    }

    public function localizeButtonableJs() {
        $buttonGroups = array('buttons', 'customButtons', 'externalPluginButtons');
        $handles = array();
        $tags = array();
        $titles = array();
        $ids = array();
        $selfClose = array();
        $shortcode = array();
        $inputDialogs = array();

        foreach ($buttonGroups as $group) {
            if (!is_array($this->settings->$group)) {
                continue;
            }

            foreach ($this->settings->$group as $handle=>$button) {
                if ($button['active'] == 'y') {
                    $handles[] = strtolower($handle);
                    $tags[] = $button['tag'];
                    $titles[] = $button['title'];
                    $ids[] = $button['id'];
                    $selfClose[] = $button['self_close'];
                    $shortcode[] = $button['shortcode'];
                    $inputDialogs[] = $button['input_dialog'];
                }
            }
        }

        // wp_localize_script takes an array of scalars only, so
        // convert the button data to strings, and then we'll
        // convert back to arrays in the js file.
        $implodedButtonGroups = array(
            'handles' => implode(",",$handles),
            'tags' => implode(",",$tags),
            'titles' => implode(",",$titles),
            'ids' => implode(",",$ids),
            'selfClose' => implode(",",$selfClose),
            'shortcodes' => implode(",",$shortcode),
            'inputDialogs' => implode(",",$inputDialogs)
        );

        $this->functionsFacade->localizeScript('buttonableJs', 'buttonableButtons', $implodedButtonGroups);
        return $implodedButtonGroups;
    }

    public function setDisplayDir($file) {
        $directoryName = $this->functionsFacade->directoryName($file);
        $this->displayDir = $directoryName . '/Display/';
        return $this->displayDir;
    }

    public function getDisplayDir() {
        return $this->displayDir;
    }

    public function includeDialogs() {
        return $this->functionsFacade->requireOnce($this->displayDir . 'dialogs.html');
    }

    public function includeCustomDialogs() {
        if ($this->functionsFacade->checkFileExists($this->displayDir . 'custom-dialogs.html')) {
            return $this->functionsFacade->requireOnce($this->displayDir . 'custom-dialogs.html');
        }

        return null;
    }

    public function includeExternalDialogs() {
        if (is_array($this->settings->externalPluginButtons)) {
            foreach($this->settings->externalPluginButtons as $button) {
                if ($button['active'] == 'y' && $button['input_dialog'] == 'y') {
                    if ($this->functionsFacade->checkFileExists($button['path'])) {
                        $this->functionsFacade->requireOnce($button['path']);
                    }
                }
            }
        }

        return true;
    }
}
