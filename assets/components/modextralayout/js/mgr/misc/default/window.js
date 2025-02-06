/**
 * @param config
 * @constructor
 */
modExtraLayout.window.Default = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        title: '',
        url: modExtraLayout.config['connector_url'],
        record: {},
        baseParams: {},
        fields: this.getFields(config),
        keys: this.getKeys(config),
        buttons: this.getButtons(config),
        listeners: this.getListeners(config),
        cls: (config['cls'] || 'modx-window') + ' mel-window',
        bodyCssClass: 'mel-window-tabs',
        width: 600,
        autoHeight: true,
        allowDrop: false,
        firstFieldFocus: false,
    });
    modExtraLayout.window.Default.superclass.constructor.call(this, config);

    this.on('hide', function () {
        var w = this;
        window.setTimeout(function () {
            w.close();
        }, 200);
    });
};
Ext.extend(modExtraLayout.window.Default, MODx.Window, {
    getFields: function (config) {
        return [];
    },

    getButtons: function (config) {
        return [{
            text: config['cancelBtnText'] || _('cancel'),
            scope: this,
            handler: function () {
                (config['closeAction'] !== 'close')
                    ? this.hide()
                    : this.close();
            }
        }, {
            text: config['saveBtnText'] || _('save'),
            cls: 'primary-button',
            scope: this,
            handler: this.submit,
        }];
    },

    getKeys: function (config) {
        return [{
            key: Ext.EventObject.ENTER,
            shift: true,
            fn: function () {
                this.submit();
            }, scope: this
        }];
    },

    getListeners: function (config) {
        return {};
    },

    focusFirstField: function () {
        if (this.firstFieldFocus && this.fp && this.fp.getForm() && this.fp.getForm().items.getCount() > 0) {
            var fld = this.findFirstTextField();
            if (fld) {
                fld.focus(false, 200);
            }
        }
    },
});
Ext.reg('mel-window-default', modExtraLayout.window.Default);
