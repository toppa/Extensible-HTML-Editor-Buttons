<?php

class ButtonableSettingsMenuDisplayer {
    private $settings;
    private $pathToTemplate;
    private $customButtonRefData;

    public function __construct($startPath) {
        $this->pathToTemplate = dirname($startPath) . '/display/settings.php';
    }

    public function setSettings(ButtonableSettings $settings) {
        $this->settings = $settings;
        return $this->settings;
    }

    public function setCustomButtonRefData(ButtonableCustomButtonRefData $customButtonRefData) {
        $this->customButtonRefData = $customButtonRefData;
        return $this->customButtonRefData;
    }

    public function run($message = null) {
        ob_start();
        require_once $this->pathToTemplate;
        $settingsMenu = ob_get_contents();
        ob_end_clean();
        return $settingsMenu;
    }

    public function displayHtmlForSettingsGroupHeader($groupName) {
        return '<tr>' . PHP_EOL
            . '<th scope="row" colspan="2"><h3>' . $groupName . '</h3></th>' . PHP_EOL
            . '</tr>' . PHP_EOL;
    }

    public function displayHtmlForBuiltInButtons() {
        return $this->displayHtmlForRequestedButtons('buttonableBuiltInButtons', $this->settings->buttons);
    }

    private function displayHtmlForRequestedButtons($type, array $buttons = null) {
        $refData = array(
            'input' => array(
                'type' => 'radio',
                'subgroup' => array('y' => __('Yes', 'buttonable'), 'n' => __('No', 'buttonable'))
            )
        );

        $html = '';
        if ($buttons) {
            foreach ($buttons as $handle=>$values) {
                $html .= '<tr valign="top">' . PHP_EOL;
                $html .= '<td>' . __('Use', 'buttonable') . " <strong>$handle</strong> " . __('button?', 'buttonable') . '</td>' . PHP_EOL;
                $inputField = $type . '[' . $handle . ']';
                $html .= '<td>' . ButtonableHtmlFormField::quickBuild($inputField, $refData, $values['active']) . '</td>' . PHP_EOL;
                $html .= '</tr>' . PHP_EOL;
            }
        }

        else {
            $html .= '<tr><td colspan="2">'
                . __('No buttons of this type are currently defined', 'buttonable')
                . '</td></tr>' . PHP_EOL;
        }

        return $html;
    }


    public function displayHtmlForCustomButtons() {
        $html = '';

        if ($this->settings->customButtons) {
            foreach ($this->settings->customButtons as $handle=>$values) {
                $html .= '<tr valign="top">' . PHP_EOL;
                $html .= '<th><strong>' . __('Name', 'buttonable') . '</strong></th>' . PHP_EOL;
                $html .= "<th><strong>$handle</strong></th>" . PHP_EOL;
                $html .= '</tr>' . PHP_EOL;

                $buttonRefData = $this->customButtonRefData->getRefData();

                foreach ($values as $k=>$v) {
                    $buttonRefData[$k]['options'] = isset($buttonRefData[$k]['options'])
                        ? $buttonRefData[$k]['options']
                        : null;

                    $refData = $this->getRefDataForButtonProperty(
                        $buttonRefData[$k]['type'],
                        $buttonRefData[$k]['options']
                    );
                    $html .= '<tr valign="top">' . PHP_EOL;
                    $html .= '<td>' . $buttonRefData[$k]['name'] . '</td>' . PHP_EOL;
                    $html .= '<td>';
                    $html .= ButtonableHtmlFormField::quickBuild("buttonableCustomButtons[$handle][$k]", $refData, $v);
                    $html .= '</td>' . PHP_EOL;
                    $html .= '</tr>' . PHP_EOL;
                }

                $checkboxRefData = array(
                    'input' => array(
                        'type' => 'checkbox',
                        'subgroup' => array('y' => __('Delete', 'buttonable'))
                    )
                );

                $html .= '<tr valign="top">' . PHP_EOL;
                $html .= '<td colspan="2">'
                    . ButtonableHtmlFormField::quickBuild("buttonableDeleteButton[$handle]", $checkboxRefData)
                    . '</td>' . PHP_EOL;
                $html .= '</tr>' . PHP_EOL;
            }
        }

        else {
            $html .= '<tr valign="top">' . PHP_EOL;
            $html .= '<td colspan="2">' . __('No buttons of this type are currently defined', 'buttonable') . '</td>' . PHP_EOL;
            $html .= '</tr>' . PHP_EOL;
        }

        return $html;
    }

    public function getRefDataForButtonProperty($inputType, $inputOptions = null) {
        switch ($inputType) {
            case 'radio':
                $refData = array(
                    'input' => array(
                        'type' => 'radio',
                        'subgroup' => $inputOptions
                    )
                );
            break;
            case 'text':
                $refData = array(
                    'input' => array(
                        'type' => 'text',
                        'size' => 20
                    )
                );
            break;
            default:
                throw New Exception(__('Unrecognized button property type', 'buttonable'));
        }

        return $refData;
    }

    public function displayHtmlForExternalPluginButtons() {
        return $this->displayHtmlForRequestedButtons('buttonableExternalPluginButtons', $this->settings->externalPluginButtons);
    }

    public function displayHtmlForAddingButton(array $addButtonValidation = null) {
        $refData = array(
            'input' => array(
                'type' => 'text',
                'size' => 20
            )
        );

        $html = '<tr valign="top">' . PHP_EOL;
        $html .= '<td nowrap="nowrap">' . __('Name', 'buttonable') . '</td>' . PHP_EOL;
        $html .= '<td>'
            . ButtonableHtmlFormField::quickBuild('buttonableNewButton[handle]', $refData, $addButtonValidation['handle'])
            . '</td>' . PHP_EOL;
        $html .= '<td>'
            . __('The label shown on the button in the HTML Editor, so keep it short. Also, if you create a custom dialog, use this for naming it\'s elements - example: for "blink" the div id of your form would be "buttonable_blink_dialog"', 'buttonable')
            . '</td>' . PHP_EOL;
        $html .= '</tr>' . PHP_EOL;

        $buttonRefData = $this->customButtonRefData->getRefData();

        foreach ($buttonRefData as $k=>$v) {
            $v['options'] = isset($v['options']) ? $v['options'] : null;
            $refData = $this->getRefDataForButtonProperty($v['type'], $v['options']);
            $inputValue = ($addButtonValidation) ? $addButtonValidation["settings[$k]"] : $v['default'];
            $html .= '<tr valign="top">' . PHP_EOL;
            $html .= '<td nowrap="nowrap">' . $v['name'] . '</td>' . PHP_EOL;
            $html .= '<td>';
            $html .= ButtonableHtmlFormField::quickBuild("buttonableNewButton[settings][$k]", $refData, $inputValue);
            $html .= '</td>' . PHP_EOL;
            $html .= '<td>' . $v['help'] . '</td>' . PHP_EOL;
            $html .= '</tr>' . PHP_EOL;
        }

        return $html;
    }

}
