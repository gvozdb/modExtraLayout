var modExtraLayout = function (config) {
    config = config || {};
    modExtraLayout.superclass.constructor.call(this, config);
};
Ext.extend(modExtraLayout, Ext.Component, {
    config: {},
    utils: {},
    page: {},
    view: {},
    panel: {},
    formpanel: {},
    grid: {},
    gridlocal: {},
    tree: {},
    window: {},
    combo: {},
    renderer: {},
    fields: {},
    ux: {},
});
Ext.reg('modextralayout', modExtraLayout);

modExtraLayout = new modExtraLayout();