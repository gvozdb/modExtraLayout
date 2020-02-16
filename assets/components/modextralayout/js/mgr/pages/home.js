modExtraLayout.page.Home = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        components: [{
            xtype: 'modextralayout-panel-home',
            renderTo: 'modextralayout-panel-home-div'
        }]
    });
    modExtraLayout.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(modExtraLayout.page.Home, MODx.Component);
Ext.reg('modextralayout-page-home', modExtraLayout.page.Home);