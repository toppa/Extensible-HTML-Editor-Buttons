jQuery(document).ready(function($) {
    // need to convert the buttonable data strings back to arrays
    var buttonableHandles = buttonableButtons.handles.split(',');
    var buttonableTags = buttonableButtons.tags.split(',');
    var buttonableTitles = buttonableButtons.titles.split(',');
    var buttonableIds = buttonableButtons.ids.split(',');
    var buttonableSelfClose = buttonableButtons.selfClose.split(',');
    var buttonableShortcodes = buttonableButtons.shortcodes.split(',');
    var buttonableInputDialogs = buttonableButtons.inputDialogs.split(',');

    // WP has a required set of arguments for the custom function passed to
    // QTags.addButton(), so certain vars can be accessed in the function
    // only by making them globals
    window.buttonableTagOpen = new Array();
    window.buttonableTagClose = new Array();
    window.buttonableTags = new Array();
    window.buttonableSelfClose = new Array();

    $.each(buttonableHandles, function(index, handle) {
        if (buttonableShortcodes[index] == 'y') {
            window.buttonableTagOpen[handle] = '[';
            window.buttonableTagClose[handle] = ']';
        }

        else {
            window.buttonableTagOpen[handle] = '<';

            if (buttonableSelfClose[index] == 'y') {
                window.buttonableTagClose[handle] = ' />';
            }

            else {
                window.buttonableTagClose[handle] = '>';
            }
        }

        if (buttonableInputDialogs[index] == 'y') {
            $('#buttonable_' + handle + '_dialog').dialog({
                dialogClass: 'wp-dialog',
                modal: true,
                autoOpen: false,
                closeOnEscape: true,
                width: 'auto'
            });

            window.buttonableTags[handle] = buttonableTags[index];
            window.buttonableSelfClose[handle] = buttonableSelfClose[index];
            QTags.addButton(buttonableIds[index], handle, handleButtonableDialog, null, null, buttonableTitles[index]);
        }

        else {
            var currentButtonableTag = window.buttonableTagOpen[handle] + buttonableTags[index] + window.buttonableTagClose[handle];

            if (buttonableSelfClose[index] == 'y') {
                QTags.addButton(buttonableIds[index], handle, currentButtonableTag);
            }

            else {
                QTags.addButton(buttonableIds[index], handle, currentButtonableTag, window.buttonableTagOpen[handle] + '/' + buttonableTags[index] + window.buttonableTagClose[handle]);
            }
        }

    });

    function handleButtonableDialog(element, canvas, ed) {
        var handle = element.value;
        var dialogId = '#buttonable_' + handle + '_dialog';
        var formId = '#buttonable_' + handle + '_form';
        $(dialogId).dialog('open');

        $(dialogId).delegate('input:submit', 'click', function(event) {
            $(formId).ajaxSubmit({
                beforeSubmit: function() {
                    var tagStart = window.buttonableTagOpen[handle]
                        + window.buttonableTags[handle];
                    var tagEnd = window.buttonableTagOpen[handle]
                        + '/' + window.buttonableTags[handle]
                        + window.buttonableTagClose[handle];
                    var inputs = $('#buttonable_' + handle
                        + '_form select, #buttonable_' + handle
                        + '_form input, #buttonable_' + handle
                        + '_form textarea');

                    inputs.each(function(i, el) {
                        var cleanVal = $(el).val().replace(/\"/g, "&quot;");

                        if (cleanVal != "" && el.type != 'submit') {
                            tagStart = tagStart + ' ' + el.name + '="' + cleanVal + '"';
                        }
                    });

                    tagStart = tagStart + window.buttonableTagClose[handle];
                    this.tagStart = tagStart;

                    if (window.buttonableSelfClose[handle] == 'n') {
                        this.tagEnd = tagEnd;
                    }

                    // unfortunately WP's quicktags API is incompletere.
                    // Expected methods don't exist when called through it. We
                    // need our own versions of isOpen() and openTag() to
                    // attach to "this" so we don't get javascript errors.
                    // isOpen() is only called when there is no selected text
                    // - appending the end tag to the start tag through here
                    // seems to be the only opportunity to get it in (because
                    // this,tagEnd is ignored in this situation)
                    this.isOpen = function(dummyEd) {
                        this.tagStart = this.tagStart + tagEnd;
                        return false;
                    }

                    this.openTag = function(dummyElement, dummyEd) {
                        return null;
                    }
                },
                clearForm: true,
                resetForm: true,
                success: function() {
                    $(dialogId).dialog('close');
                    QTags.TagButton.prototype.callback.call(this, element, canvas, ed);
                    // since this function is called each time the button is
                    // clicked, we need to prevent an accumulation of handlers
                    $(dialogId).undelegate('input:submit', 'click');
                }
            });

            event.preventDefault();
        });
    }
});
