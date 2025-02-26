/**
 * @param config
 * @returns {{object}}
 * @constructor
 */
modExtraLayout.fields.Object = function (config) {
    const data = config['record'] ? config.record['object'] : null;
    const fields = {
        xtype: 'modx-tabs',
        border: true,
        autoHeight: true,
        // style: {marginTop: '10px'},
        anchor: '100% 100%',
        items: [{
            title: _('mel_tab_main'),
            layout: 'form',
            cls: 'modx-panel mel-panel',
            autoHeight: true,
            items: [],
        }],
        listeners: {
            afterrender: function (tabs) {
                // Рендерим вторую вкладку, иначе данные с неё не передаются в процессор
                tabs.setActiveTab(1);
                tabs.setActiveTab(0);

                if (config['activeTab']) {
                    tabs.setActiveTab(config['activeTab']);
                }
            },
        },
    };
    const tabs = {
        main: fields.items[0].items,
    };


    //
    // Tab / Main
    tabs['main'].push({
        layout: 'column',
        border: false,
        style: {marginTop: '0px'},
        anchor: '100%',
        items: [{
            columnWidth: .5,
            layout: 'form',
            style: {marginRight: '5px'},
            items: [{
                xtype: 'mel-combo-group',
                id: config['id'] + '-group',
                name: 'group',
                fieldLabel: _('mel_field_group'),
                anchor: '100%',
            }, {
                xtype: 'textfield',
                id: config['id'] + '-name',
                name: 'name',
                fieldLabel: _('mel_field_name'),
                anchor: '100%',
            }],
        }, {
            columnWidth: .5,
            layout: 'form',
            style: {marginLeft: '5px'},
            items: [{
                xtype: 'textarea',
                id: config['id'] + '-description',
                name: 'description',
                fieldLabel: _('mel_field_description'),
                height: 102,
                anchor: '100%',
            }],
        }],
    }, {
        layout: 'column',
        border: false,
        style: {marginTop: '0px'},
        anchor: '100%',
        items: [{
            columnWidth: .5,
            layout: 'form',
            style: {marginRight: '5px'},
            items: [{
                xtype: 'mel-combo-parent',
                id: config['id'] + '-parent',
                name: 'parent',
                fieldLabel: _('mel_field_parent'),
                anchor: '100%',
            }],
        }, {
            columnWidth: .5,
            layout: 'form',
            style: {marginLeft: '5px'},
            items: [{
                xtype: 'mel-datetime',
                id: config['id'] + '-createdon',
                name: 'createdon',
                fieldLabel: _('mel_field_createdon'),
                anchor: '100%',
                // hideTime: true,
                // timeWidth: 0,
            }],
        }],
    }, {
        layout: 'column',
        border: false,
        style: {marginTop: '0px'},
        anchor: '100%',
        items: [{
            columnWidth: 1,
            layout: 'form',
            style: {margin: '0px'},
            items: [{
                xtype: 'mel-gridlocal-subobjects',
                id: config['id'] + '-subobjects-grid',
                name: 'subobjects',
                fieldLabel: _('mel_field_subobjects'),
                anchor: '100%',
            }],
        }],
    }, {
        layout: 'column',
        border: false,
        style: {marginTop: '0px'},
        anchor: '100%',
        items: [{
            columnWidth: 1,
            layout: 'form',
            style: {margin: '0px'},
            items: [{
                xtype: 'mel-gridlocal-files',
                id: config['id'] + '-files-grid',
                name: 'files',
                fieldLabel: _('mel_field_files'),
                anchor: '100%',
                source: data ? data['source'] : undefined,
            }],
        }],
    }, {
        layout: 'column',
        border: false,
        style: {marginTop: '0px'},
        anchor: '100%',
        items: [{
            columnWidth: 1,
            layout: 'form',
            items: [{
                xtype: 'xcheckbox',
                id: config['id'] + '-active',
                name: 'active',
                boxLabel: _('mel_field_active'),
            }],
        }],
    });

    tabs['main'].push({
        xtype: 'hidden',
        id: config['id'] + '-source',
        name: 'source',
    });

    if (data) {
        tabs['main'].push({
            xtype: 'hidden',
            id: config['id'] + '-id',
            name: 'id',
        });
    }

    return fields;
};


/**
 * @param config
 * @constructor
 */
modExtraLayout.window.ObjectCreate = function (config) {
    config = config || {};
    if (!config['id']) {
        config['id'] = 'mel-window-object-create';
    }
    Ext.applyIf(config, {
        title: _('mel_window_create'),
        baseParams: {
            action: 'mgr/objects/create',
        },
        modal: true,
    });
    modExtraLayout.window.ObjectCreate.superclass.constructor.call(this, config);
};
Ext.extend(modExtraLayout.window.ObjectCreate, modExtraLayout.window.Default, {
    getFields: function (config) {
        return modExtraLayout.fields.Object.bind(this)(config);
    },
});
Ext.reg('mel-window-object-create', modExtraLayout.window.ObjectCreate);


/**
 * @param config
 * @constructor
 */
modExtraLayout.window.ObjectUpdate = function (config) {
    config = config || {};
    if (!config['id']) {
        config['id'] = 'mel-window-object-update';
    }
    Ext.applyIf(config, {
        title: _('mel_window_update'),
        baseParams: {
            action: 'mgr/objects/update',
        },
        modal: true,
    });
    modExtraLayout.window.ObjectUpdate.superclass.constructor.call(this, config);
};
Ext.extend(modExtraLayout.window.ObjectUpdate, modExtraLayout.window.Default, {
    getFields: function (config) {
        return modExtraLayout.fields.Object.bind(this)(config);
    },
});
Ext.reg('mel-window-object-update', modExtraLayout.window.ObjectUpdate);
