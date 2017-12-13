modExtraLayout.panel.Default = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        // id: Ext.id(),
        items: this.getItems(config),
        listeners: this.getListeners(config),

        baseCls: 'modx-formpanel',
        layout: 'anchor',
        hideMode: 'offsets',
        autoHeight: true,
    });
    modExtraLayout.panel.Default.superclass.constructor.call(this, config);
};
Ext.extend(modExtraLayout.panel.Default, MODx.Panel, {
    getItems: function (config) {
        return [];
    },

    getListeners: function (config) {
        return {};
    },
});
Ext.reg('mel-panel-default', modExtraLayout.panel.Default);
