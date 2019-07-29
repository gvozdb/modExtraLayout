/**
 *
 * @param config
 * @constructor
 */
modExtraLayout.combo.Search = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        xtype: 'twintrigger',
        ctCls: 'x-field-search',
        allowBlank: true,
        msgTarget: 'under',
        emptyText: _('search'),
        name: 'query',
        triggerAction: 'all',
        clearBtnCls: 'x-field-search-clear',
        searchBtnCls: 'x-field-search-go',
        onTrigger1Click: this._triggerSearch,
        onTrigger2Click: this._triggerClear,
    });
    modExtraLayout.combo.Search.superclass.constructor.call(this, config);
    this.on('render', function () {
        this.getEl().addKeyListener(Ext.EventObject.ENTER, function () {
            this._triggerSearch();
        }, this);
        this.positionEl.setStyle('margin-right', '1px');
    });
    this.addEvents('clear', 'search');
};
Ext.extend(modExtraLayout.combo.Search, Ext.form.TwinTriggerField, {
    initComponent: function () {
        Ext.form.TwinTriggerField.superclass.initComponent.call(this);
        this.triggerConfig = {
            tag: 'span',
            cls: 'x-field-search-btns',
            cn: [
                {tag: 'div', cls: 'x-form-trigger ' + this.searchBtnCls},
                {tag: 'div', cls: 'x-form-trigger ' + this.clearBtnCls}
            ]
        };
    },
    _triggerSearch: function () {
        this.fireEvent('search', this);
    },
    _triggerClear: function () {
        this.fireEvent('clear', this);
    },
});
Ext.reg('mel-field-search', modExtraLayout.combo.Search);


/**
 *
 * @param config
 * @constructor
 */
modExtraLayout.combo.DateTime = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        timePosition: 'right',
        allowBlank: true,
        hiddenFormat: 'U', // 'Y-m-d H:i:s',
        dateFormat: MODx.config['manager_date_format'],
        timeFormat: MODx.config['manager_time_format'],
        dateWidth: 120,
        timeWidth: 120,
    });
    modExtraLayout.combo.DateTime.superclass.constructor.call(this, config);
};
Ext.extend(modExtraLayout.combo.DateTime, Ext.ux.form.DateTime);
Ext.reg('mel-datetime', modExtraLayout.combo.DateTime);


/**
 *
 * @param config
 * @constructor
 */
modExtraLayout.combo.Parent = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        name: 'parent',
        fieldLabel: config['name'] || 'parent',
        hiddenName: config['name'] || 'parent',
        displayField: 'pagetitle',
        valueField: 'id',
        fields: ['id', 'pagetitle', 'parents'],
        url: modExtraLayout.config['connector_url'],
        baseParams: {
            action: 'mgr/combo/getresources',
            context_key: config['context_key'] || 'web',
            isfolder: 1,
            parents: 1,
        },
        pageSize: 20,
        typeAhead: false,
        editable: true,
        minChars: 1,
        anchor: '100%',
        listEmptyText: '<div style="padding: 7px;">' + _('mel_combo_list_empty') + '</div>',
        tpl: new Ext.XTemplate('\
            <tpl for="."><div class="x-combo-list-item mel-combo-row">\
                <tpl if="parents">\
                    <div class="parents">\
                        <tpl for="parents">\
                            <nobr><small>{pagetitle} / </small></nobr>\
                        </tpl>\
                    </div>\
                </tpl>\
                <span>\
                    <small>({id})</small> <b>{pagetitle}</b>\
                </span>\
            </div></tpl>',
            {compiled: true}
        ),
    });
    modExtraLayout.combo.Parent.superclass.constructor.call(this, config);

    // Обновляем список при открытии
    this.on('expand', function () {
        this.getStore().load();
    }, this);
};
Ext.extend(modExtraLayout.combo.Parent, MODx.combo.ComboBox);
Ext.reg('mel-combo-parent', modExtraLayout.combo.Parent);


/**
 *
 * @param config
 * @constructor
 */
modExtraLayout.combo.Group = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        name: 'group',
        fieldLabel: config['name'] || 'group',
        hiddenName: config['name'] || 'group',
        displayField: 'display',
        valueField: 'value',
        fields: ['value', 'display'],
        url: modExtraLayout.config['connector_url'],
        baseParams: {
            action: 'mgr/combo/getgroups',
            filter: config['filter'] || 0,
            notempty: config['notempty'] || 1
        },
        pageSize: 20,
        typeAhead: false,
        editable: true,
        anchor: '100%',
        listEmptyText: '<div style="padding: 7px;">' + _('mel_combo_list_empty') + '</div>',
        tpl: new Ext.XTemplate('\
            <tpl for="."><div class="x-combo-list-item mel-combo-row">\
                <span class="mel-combo-row__group mel-combo-row__{value}">\
                    {display}\
                </span>\
            </div></tpl>',
            {compiled: true}
        ),
    });
    modExtraLayout.combo.Group.superclass.constructor.call(this, config);

    // Обновляем список при открытии
    this.on('expand', function () {
        this.getStore().load();
    }, this);
};
Ext.extend(modExtraLayout.combo.Group, MODx.combo.ComboBox);
Ext.reg('mel-combo-group', modExtraLayout.combo.Group);