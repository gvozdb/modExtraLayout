/**
 * @param config
 * @constructor
 */
modExtraLayout.gridlocal.Subobjects = function (config) {
    config = config || {};
    if (!config['id']) {
        config['id'] = 'mel-gridlocal-subobjects';
    }
    Ext.applyIf(config, {
        tbarCls: 'mel-grid-toptbar_field',
        style: {marginTop: '0px', padding: 0},
    });
    modExtraLayout.gridlocal.Subobjects.superclass.constructor.call(this, config);
};
Ext.extend(modExtraLayout.gridlocal.Subobjects, modExtraLayout.gridlocal.Default, {
    //
});
Ext.reg('mel-gridlocal-subobjects', modExtraLayout.gridlocal.Subobjects);
