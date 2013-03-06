<div class="wrap">
    <div style="float: right; font-weight: bold; margin-top: 15px; width: 340px;">
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
        <input type="hidden" name="cmd" value="_s-xclick">
        <input type="hidden" name="hosted_button_id" value="5378623">
        <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" /><?php _e('Support this plugin', 'buttonable'); ?> &raquo;
        <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="<?php _e('Support this plugin', 'buttonable'); ?>" title="<?php _e("Support Extensible HTML Editor Buttons", 'buttonable'); ?>" style="vertical-align: middle; padding-right: 20px;" />
        <a href="http://www.toppa.com/extensible-html-editor-buttons-wordpress-plugin/" target="_blank"><?php _e('Extensible HTML Editor Buttons Help', 'buttonable'); ?></a>
        </form>
    </div>
    <?php screen_icon(); ?>
    <h2><?php _e('Extensible HTML Editor Buttons Settings', 'buttonable'); ?></h2>

    <?php if ($message) {
        echo '<div id="message" class="updated"><p>' . $message . '</p></div>';
    } ?>

    <form method="post">
        <?php settings_fields('buttonable'); ?>
        <input type="hidden" name="buttonableAction" value="updateButtons" />
        <table class="form-table" style="width: auto;">
        <?php
            echo $this->displayHtmlForSettingsGroupHeader(__('Extensible HTML Editor Buttons', 'buttonable'));
            echo $this->displayHtmlForBuiltInButtons();
            echo $this->displayHtmlForSettingsGroupHeader(__('Your custom buttons', 'buttonable'));
            echo $this->displayHtmlForCustomButtons();
            echo $this->displayHtmlForSettingsGroupHeader(__('Buttons from other plugins', 'buttonable'));
            echo $this->displayHtmlForExternalPluginButtons();
        ?>
        </table>
        <p class="submit"><input class="button-primary" type="submit" name="update" value="<?php _e('Update Buttons', 'buttonable'); ?>" /></p>
    </form>

    <hr />

    <form method="post">
    <?php settings_fields('buttonable'); ?>
    <input type="hidden" name="buttonableAction" value="addButton" />
    <table class="form-table" style="width: auto;">
    <?php
        echo $this->displayHtmlForSettingsGroupHeader(__('Add a custom button', 'buttonable'));
        echo $this->displayHtmlForAddingButton();
    ?>
    </table>
    <p class="submit"><input class="button-primary" type="submit" name="save" value="<?php _e("Add Button", 'buttonable'); ?>" /></p>
    </form>
</div>

