var modExtraLayout = function (config) {
    config = config || {};
    modExtraLayout.superclass.constructor.call(this, config);
};
Ext.extend(modExtraLayout, Ext.Component, {
    page: {},
    window: {},
    grid: {},
    tree: {},
    panel: {},
    combo: {},
    config: {},
    view: {},
    utils: {},
    ux: {},
    fields: {},
});
Ext.reg('modextralayout', modExtraLayout);

modExtraLayout = new modExtraLayout();