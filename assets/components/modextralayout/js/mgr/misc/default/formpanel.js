modExtraLayout.formpanel.Default = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        url: modExtraLayout.config['connector_url'],
        baseParams: {},
        items: this.getFields(config),
        keys: this.getKeys(config),
        buttons: this.getButtons(config),
        listeners: this.getListeners(config),

        fileUpload: false,

        baseCls: 'modx-formpanel',
        layout: 'anchor',
        hideMode: 'offsets',
        formFrame: true,
        border: false,
        bodyBorder: false,
        autoHeight: true,

        labelAlign: 'top',
        buttonAlign: 'left',
    });
    modExtraLayout.formpanel.Default.superclass.constructor.call(this, config);
};
Ext.extend(modExtraLayout.formpanel.Default, MODx.FormPanel, {
    getFields: function (config) {
        return [];
    },

    getButtons: function (config) {
        return [{
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
        return {
            success: {fn: this._listenerHandler, scope: this},
            failure: {fn: this._listenerHandler, scope: this},
        };
    },

    _listenerHandler: function (resp, callback) {
        if (typeof(callback) == 'function') {
            callback(resp);
        }

        var success = false;
        var message = '';
        if (typeof(resp['message']) != 'undefined') {
            success = resp['success'];
            message = resp['message'];
        } else if (typeof(resp.result['message']) != 'undefined') {
            success = resp.result['success'];
            message = resp.result['message'];
        } else if (typeof(resp.a.result['message']) != 'undefined') {
            success = resp.a.result['success'];
            message = resp.a.result['message'];
        }

        if (message) {
            MODx.msg.alert(_(success ? 'success' : 'failure'), message);
        }
    },
});
Ext.reg('mel-formpanel-default', modExtraLayout.formpanel.Default);
