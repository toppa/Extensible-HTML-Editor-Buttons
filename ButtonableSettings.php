<?php

class ButtonableSettings extends ToppaSettingsWp {
    public function __construct(ToppaFunctionsFacade $functionsFacade) {
        parent::__construct('buttonable', $functionsFacade);
    }
}
