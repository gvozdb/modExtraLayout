modExtraLayout.panel.Home = function (config) {
    config = config || {};
    Ext.apply(config, {
        baseCls: 'modx-formpanel',
        layout: 'anchor',
        hideMode: 'offsets',
        items: [{
            html: '<h2>' + _('modextralayout') + '</h2>',
            cls: '',
            style: {margin: '15px 0'}
        }, {
            xtype: 'modx-tabs',
            defaults: {border: false, autoHeight: true},
            border: true,
            hideMode: 'offsets',
            enableTabScroll: false,
            /*stateful: true,
            stateId: 'modextra-panel-home',
            stateEvents: ['tabchange'],
            getState: function () {
                return {activeTab: this.items.indexOf(this.getActiveTab())};
            },*/
            items: [{
                title: _('mel_tab_objects'),
                layout: 'anchor',
                items: [{
                    xtype: 'mel-grid-objects',
                    cls: 'main-wrapper',
                }]
            }]
        }]
    });
    modExtraLayout.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(modExtraLayout.panel.Home, MODx.Panel);
Ext.reg('modextralayout-panel-home', modExtraLayout.panel.Home);
