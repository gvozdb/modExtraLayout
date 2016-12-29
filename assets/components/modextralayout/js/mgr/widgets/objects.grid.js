modExtraLayout.grid.Objects = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'mel-grid-objects';
    }
    config['actionPrefix'] = 'mgr/object/';
    Ext.applyIf(config, {
        baseParams: {
            action: config['actionPrefix'] + 'getlist',
            sort: 'id',
            dir: 'DESC',
        },
        multi_select: true,
        // pageSize: Math.round(MODx.config['default_per_page'] / 2),
    });
    modExtraLayout.grid.Objects.superclass.constructor.call(this, config);
};
Ext.extend(modExtraLayout.grid.Objects, modExtraLayout.grid.Default, {
    getFields: function (config) {
        return [
            'id',
            'parent_formatted',
            'group',
            'name',
            'description',
            'active',
            'actions',
        ];
    },

    getColumns: function (config) {
        return [{
            header: _('mel_grid_id'),
            dataIndex: 'id',
            width: 70,
            sortable: true,
            fixed: true,
            resizable: false,
        }, {
            header: _('mel_grid_parent'),
            dataIndex: 'parent_formatted',
            width: 150,
            sortable: false,
        }, {
            header: _('mel_grid_group'),
            dataIndex: 'group',
            width: 100,
            sortable: true,
        }, {
            header: _('mel_grid_name'),
            dataIndex: 'name',
            width: 200,
            sortable: true,
        }, {
            header: _('mel_grid_description'),
            dataIndex: 'description',
            width: 400,
            sortable: false,
        }, {
            header: _('mel_grid_active'),
            dataIndex: 'active',
            width: 70,
            sortable: true,
            fixed: true,
            resizable: false,
            renderer: modExtraLayout.utils.renderBoolean,
        }, {
            header: _('mel_grid_actions'),
            dataIndex: 'actions',
            id: 'actions',
            width: 200,
            sortable: false,
            fixed: true,
            resizable: false,
            renderer: modExtraLayout.utils.renderActions,
        }];
    },

    getTopBar: function (config) {
        return [{
            text: '<i class="icon icon-plus"></i>&nbsp;' + _('mel_button_create'),
            cls: 'primary-button',
            handler: this.createObject,
            scope: this,
        }, '->', {
            xtype: 'mel-combo-group',
            id: config.id + '-group',
            filterName: 'group',
            emptyText: _('mel_grid_group') + '...',
            width: 150,
            filter: true,
            listeners: {
                select: {fn: this._doFilter, scope: this},
            },
        }, this.getSearchField(config)];
    },

    getListeners: function (config) {
        return {
            rowDblClick: function (grid, rowIndex, e) {
                var row = grid.store.getAt(rowIndex);
                this.updateObject(grid, e, row);
            },
        };
    },

    createObject: function (btn, e) {
        var w = MODx.load({
            xtype: 'mel-window-object-create',
            id: Ext.id(),
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    },
                    scope: this
                },
                failure: {fn: this._listenerHandler, scope: this},
            },
        });
        w.reset();
        w.setValues({
            active: true,
        });
        w.show(e.target);
    },

    updateObject: function (btn, e, row, activeTab) {
        if (typeof(row) != 'undefined') {
            this.menu.record = row.data;
        } else if (!this.menu.record) {
            return false;
        }
        var id = this.menu.record.id;

        if (typeof(activeTab) == 'undefined') {
            activeTab = 0;
        }

        MODx.Ajax.request({
            url: this.config['url'],
            params: {
                action: this['actionPrefix'] + 'get',
                id: id,
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var w = MODx.load({
                            xtype: 'mel-window-object-update',
                            id: Ext.id(),
                            record: r,
                            activeTab: activeTab,
                            listeners: {
                                success: {
                                    fn: function () {
                                        this.refresh();
                                    },
                                    scope: this
                                },
                                failure: {fn: this._listenerHandler, scope: this},
                            },
                        });
                        w.reset();
                        w.setValues(r.object);
                        w.show(e.target);
                    }, scope: this
                },
                failure: {fn: this._listenerHandler, scope: this},
            }
        });
    },

    enableObject: function () {
        this.loadMask.show();
        return this._doAction('enable');
    },

    disableObject: function () {
        this.loadMask.show();
        return this._doAction('disable');
    },

    removeObject: function () {
        return this._doAction('remove', null, true);
    },
});
Ext.reg('mel-grid-objects', modExtraLayout.grid.Objects);