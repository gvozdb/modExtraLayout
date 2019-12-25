/**
 *
 * @param config
 * @constructor
 */
modExtraLayout.combo.Default = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        name: 'name',
        hiddenName: config['name'] || 'name',
        fieldLabel: config['name'] || 'name',
        displayField: 'display',
        valueField: 'value',
        fields: ['value', 'display'],
        url: modExtraLayout.config['connector_url'],
        baseParams: {
            action: 'mgr/combo/getvalues',
        },
        pageSize: 20,
        typeAhead: false,
        editable: true,
        anchor: '100%',
        classRow: 'mel-combo-row mel-combo-row__' + config['name'],
        listEmptyText: '<div style="padding: 7px;">' + _('mel_combo_list_empty') + '</div>',
    });
    Ext.applyIf(config, {
        tpl: new Ext.XTemplate('\
            <tpl for="."><div class="x-combo-list-item ' + config['classRow'] + '">\
                {display}\
            </div></tpl>',
            {compiled: true}
        ),
    });
    modExtraLayout.combo.Default.superclass.constructor.call(this, config);

    //
    var combo = this;

    //
    combo.on('render', function () {
        combo.getStore().on('beforeload', function (store, data) {
            if (!data.params[combo.valueField]) {
                data.params[combo.valueField] = combo.getValue();
            }
        });
    }, combo);

    // Обновляем список при открытии
    combo.on('expand', function () {
        combo.getStore().load();
    }, combo);
};
Ext.extend(modExtraLayout.combo.Default, MODx.combo.ComboBox);
Ext.reg('mel-combo-default', modExtraLayout.combo.Default);