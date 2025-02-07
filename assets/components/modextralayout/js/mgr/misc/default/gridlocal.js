/**
 * @param config
 * @constructor
 */
modExtraLayout.gridlocal.Default = function(config) {
    config = config || {};

    config['name'] = config['name'] || ''

    config['cls'] = (config['cls'] || 'main-wrapper') + ' mel-grid mel-gridlocal';
    config['tbarCls'] = (config['tbarCls'] || '') + ' mel-grid-toptbar mel-gridlocal-toptbar';
    config['tbarStyle'] = (config['tbarStyle'] || '')

    // this.exp = new Ext.grid.RowExpander({
    //     tpl: new Ext.Template(
    //         '<p class="desc">{user_group_desc}</p>'
    //     ),
    // });

    Ext.applyIf(config, {
        // title: '',
        // header: false,
        // url: modExtraLayout.config['connector_url'],
        // baseParams: {
        //     action: 'Security/Group/GetList',
        // },

        fields: this.getFields(config),
        columns: this.getColumns(config),
        tbar: this.getTopBar(config),
        listeners: this.getListeners(config),

        items: [{
            hidden: true,
            xtype: 'textarea',
            id: config['id'] + '-field',
            name: config['name'],
            width: '100%',
            height: 102,
            anchor: '100%',
            listeners: {
                afterrender: {
                    fn: function (field) {
                        const value = field.value // не .getValue(), потому что массив прогоняется через текстовое поле и приходить в виде "[object Object]"
                        const items = value ? (typeof(value) === 'object' ? value : JSON.parse(value)) : []
                        if (items.length) {
                            const grid = field.ownerCt
                            const store = grid.getStore()

                            store.preventChangeEvent = true
                            items.map(item => {
                                if (!item['actions']) {
                                    item['actions'] = this.getActions(this)
                                }
                                const record = new this.itemRecord(item);
                                store.add(record)
                            })
                            store.preventChangeEvent = false
                        }
                    },
                    scope: this
                },
            }
        }],

        plugins: [
            // this.exp,
            new Ext.ux.dd.GridDragDropRowOrder({
                copy: false,
                scrollable: true,
                targetCfg: {},
                listeners: {
                    afterrowmove: {fn:this.onAfterRowMove, scope:this},
                    beforerowmove: {fn:this.onBeforeRowMove, scope:this},
                }
            }),
        ],

        viewConfig: {
            forceFit: true,
            enableRowBody: true,
            autoFill: true,
            showPreview: true,
            scrollOffset: 0,
            getRowClass: function (rec) {
                var cls = ['mel-grid-row'];
                if (typeof(rec.data['active']) !== 'undefined' && rec.data['active'] == 0) {
                    cls.push('mel-grid-row_disabled');
                }
                return cls.join(' ');
            },
        },
        autoHeight: true,
    });
    modExtraLayout.gridlocal.Default.superclass.constructor.call(this,config);

    //
    // Создаём обёртку для данных, которая необходима для добавления в Grid Store
    this.itemRecord = new Ext.data.Record.create([
        // 'usergroup','name','member','role','rolename','primary_group',
    ]);

    //
    // Добавляем дополнительные события
    this.addEvents(
        'change',
        // 'beforeUpdateRole',
        // 'afterUpdateRole',
        // 'beforeAddGroup',
        // 'afterAddGroup',
        'beforeReorderRow',
        'afterReorderRow'
    );

    //
    // Создаём событие "change" на гриде
    this.store.on('add', function (store, record) {
        if (!store.preventChangeEvent) {
            this.fireEvent('change', this, store.data.items)
        }
    }, this);
    this.store.on('update', function (store, record) {
        this.fireEvent('change', this, store.data.items)
    }, this);
    this.store.on('remove', function (store, record) {
        this.fireEvent('change', this, store.data.items)
    }, this);

    //
    // По изменению данных в гриде перезаписываем данные в скрытом поле
    this.on('change', function (grid, items) {
        const $hidden = this.items.find(v => v.name === this.config.name)
        $hidden.setValue(JSON.stringify(items.map(item => {
            return {
                ...item.data,
                actions: undefined
            }
        })))
    }, this);

    //
    // Корректируем гриду после рендера
    this.on('afterrender', function () {
        this.topToolbar.addClass(this.config.tbarCls)
        this.topToolbar.style = this.config.tbarStyle

        // Скрываем topToolbar, если он пустой
        if (!this.topToolbar.items['length']) {
            this.topToolbar.hide();
        }
    }, this);
};
Ext.extend(modExtraLayout.gridlocal.Default, MODx.grid.LocalGrid, {
    getFields: function (config) {
        return [
            'idx',
            'name',
            'active',
            'actions',
        ];
    },

    getColumns: function (config) {
        return [
            // this.exp,
            {
                header: 'idx',
                dataIndex: 'idx',
                width: 70,
                sortable: true,
                fixed: true,
                resizable: false,
                editor: {xtype: 'numberfield', allowBlank: false, allowNegative: false},
            }, {
                header: _('mel_grid_name'),
                dataIndex: 'name',
                width: 175,
            }, {
                header: _('mel_grid_active'),
                dataIndex: 'active',
                width: 60,
                sortable: true,
                fixed: true,
                resizable: false,
                renderer: modExtraLayout.renderer['Boolean'],
                editor: {xtype: 'xcheckbox'},
            }, {
                header: _('mel_grid_actions'),
                dataIndex: 'actions',
                id: 'actions',
                width: 100,
                sortable: false,
                fixed: true,
                resizable: false,
                renderer: modExtraLayout.renderer['Actions'],
            },
        ];
    },

    getActions: function (config) {
        return [{
            cls: '',
            icon: 'icon icon-trash-o action-red',
            title: _('mel_button_remove'),
            multiple: _('mel_button_remove_multiple'),
            action: this.removeObject,
            button: true,
            menu: true,
        }]
    },

    getTopBar: function (config) {
        return [{
            text: _('mel_button_create'),
            cls: 'primary-button',
            handler: (btn, e) => {
                return this.addObject(
                    {
                        name: Math.floor(Math.random() * Date.now()).toString(36),
                        active: !(Math.round(Math.random())),
                    },
                    btn, e
                )
            },
        }]
    },

    getListeners: function () {
        return {};
    },

    addObject: function(data, btn, e) {
        const store = this.getStore();
        data = typeof(data) === 'object' ? data : {}

        if (!data['actions']) {
            data['actions'] = this.getActions(this)
        }
        data['idx'] = store.getCount();
        data['rank'] = store.getCount();

        const record = new this.itemRecord(data);
        store.add(record);
    },

    removeObject: function(btn, e) {
        return this.remove.bind(this)({
            title: _('mel_button_remove'),
            text: _('mel_confirm_remove'),
        })
    },

    remove: function(config) {
        if (this.destroying) {
            return MODx.grid.LocalGrid.superclass.remove.apply(this, arguments);
        }
        const records = this.getSelectionModel().getSelections();
        if (this.fireEvent('beforeRemoveRow', records)) {
            Ext.Msg.confirm(
                config.title || '',
                config.text || '',
                function (e) {
                    if (e === 'yes') {
                        records.map(r => {
                            this.getStore().remove(r);
                            this.fireEvent('afterRemoveRow', r);
                        })
                    }
                },
                this);
        }
    },

    onBeforeRowMove: function(dt,sri,ri,sels) {
        if (!this.fireEvent('beforeReorderRow', {dt:dt,sri:sri,ri:ri,sels:sels})) {
            return false;
        }
        return true;
    },

    onAfterRowMove: function(dt,sri,ri,sels) {
        var s = this.getStore();
        var sourceRec = s.getAt(sri);
        var belowRec = s.getAt(ri);
        var total = s.getTotalCount();

        sourceRec.set('idx',sri);
        sourceRec.set('rank',sri);
        sourceRec.commit();

        /* get all rows below ri, and up their idx by 1 */
        var brec;
        for (var x=(ri-1); x<total; x++) {
            brec = s.getAt(x);
            if (brec) {
                brec.set('idx', x);
                brec.set('rank', x);
                brec.commit();
            }
        }
        this.fireEvent('afterReorderRow');
        return true;
    },

    onClick: function (e) {
        var elem = e.getTarget();
        if (elem.nodeName === 'BUTTON') {
            var row = this.getSelectionModel().getSelected();
            if (typeof(row) !== 'undefined') {
                let action_index = elem.getAttribute('action-index');
                if (action_index) {
                    let action = row.data.actions[action_index].action
                    if (typeof(action) === 'function') {
                        this.menu.record = row.data;
                        return action.bind(this)(this, e);
                    } else if (typeof(this[action]) === 'function') {
                        this.menu.record = row.data;
                        return this[action].bind(this)(this, e);
                    }
                }
            }
        } else if (elem.nodeName === 'A' && elem.href.match(/(\?|\&)a=resource/)) {
            if (e.button == 1 || (e.button == 0 && e.ctrlKey == true)) {
                // Bypass
            } else if (elem.target && elem.target == '_blank') {
                // Bypass
            } else {
                e.preventDefault();
                MODx.loadPage('', elem.href);
            }
        }
        return this.processEvent('click', e);
    },

    _showMenu: function(g, ri, e) {
        e.stopEvent();
        e.preventDefault();
        var m = this.menu;
        m.recordIndex = ri;
        m.record = this.getStore().getAt(ri).data;
        if (!this.getSelectionModel().isSelected(ri)) {
            this.getSelectionModel().selectRow(ri);
        }
        m.removeAll();
        this.addContextMenuItem(
            modExtraLayout.utils.getMenu(this.getActions(this), this, [ri])
        )
        m.showAt(e.xy);
    },
});
Ext.reg('mel-gridlocal-default', modExtraLayout.gridlocal.Default);
