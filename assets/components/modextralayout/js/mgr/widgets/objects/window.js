/**
 * Вкладки/поля для окон добавления/редактирования
 *
 * @param config
 * @returns {{object}}
 * @constructor
 */
modExtraLayout.fields.Object = function (config) {
    var data = config['record'] ? config.record['object'] : null;

    var r = {
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

    r.items[0].items.push({
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
            items: [{
                xtype: 'xcheckbox',
                id: config['id'] + '-active',
                name: 'active',
                boxLabel: _('mel_field_active'),
            }],
        }],
    });

    if (data) {
        r.items[0].items.push({
            xtype: 'hidden',
            id: config['id'] + '-id',
            name: 'id',
        });
    }

    return r;
};

/**
 * Окно добавления объекта
 *
 * @param config
 * @constructor
 */
modExtraLayout.window.ObjectCreate = function (config) {
    config = config || {};
    if (!config['id']) {
        config['id'] = 'mel-window-object-create';
    }
    Ext.applyIf(config, {
        title: _('mel_window_object_create'),
        baseParams: {
            action: 'mgr/object/create',
        },
        modal: true,
    });
    modExtraLayout.window.ObjectCreate.superclass.constructor.call(this, config);
};
Ext.extend(modExtraLayout.window.ObjectCreate, modExtraLayout.window.Default, {
    getFields: function (config) {
        return modExtraLayout.fields.Object(config);
    },
});
Ext.reg('mel-window-object-create', modExtraLayout.window.ObjectCreate);

/**
 * Окно редактирования объекта
 *
 * @param config
 * @constructor
 */
modExtraLayout.window.ObjectUpdate = function (config) {
    config = config || {};
    if (!config['id']) {
        config['id'] = 'mel-window-object-update';
    }
    Ext.applyIf(config, {
        title: _('mel_window_object_update'),
        baseParams: {
            action: 'mgr/object/update',
        },
        modal: true,
    });
    modExtraLayout.window.ObjectUpdate.superclass.constructor.call(this, config);
};
Ext.extend(modExtraLayout.window.ObjectUpdate, modExtraLayout.window.Default, {
    getFields: function (config) {
        return modExtraLayout.fields.Object(config);
    },
});
Ext.reg('mel-window-object-update', modExtraLayout.window.ObjectUpdate);