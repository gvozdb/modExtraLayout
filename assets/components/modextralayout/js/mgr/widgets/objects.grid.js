modExtraLayout.grid.Objects = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'mel-grid-objects';
    }
    Ext.applyIf(config, {
        baseParams: {
            action: 'mgr/object/getlist',
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
            renderer: modExtraLayout.utils.renderBoolean,
            sortable: true,
            fixed: true,
            resizable: false,
            width: 70,
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
        }, '->', this.getSearchField(config)];
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
                failure: {fn: this._failureHandler, scope: this},
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
                action: 'mgr/object/get',
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
                                failure: {fn: this._failureHandler, scope: this},
                            },
                        });
                        w.reset();
                        w.setValues(r.object);
                        w.show(e.target);
                    }, scope: this
                },
                failure: {fn: this._failureHandler, scope: this},
            }
        });
    },

    actionObject: function (action, confirm, checkIds) {
        if (typeof(action) == 'undefined') {
            return false;
        }
        if (typeof(confirm) == 'undefined') {
            confirm = false;
        }
        if (typeof(checkIds) == 'undefined') {
            checkIds = true;
        }
        var ids = this._getSelectedIds();
        if (checkIds && !ids.length) {
            this.refresh();
            return false;
        }

        var params = {
            url: this.config['url'],
            params: {
                action: 'mgr/object/' + action,
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var grid = this;
                        this._failureHandler(r, function () {
                            grid.refresh();
                        });
                    }, scope: this
                },
                failure: {
                    fn: function (r) {
                        var grid = this;
                        this._failureHandler(r, function () {
                            grid.refresh();
                        });
                    }, scope: this
                },
            },
        };

        if (confirm) {
            MODx.msg.confirm(Ext.apply({}, params, {
                title: ids.length > 1
                    ? _('mel_button_' + action + '_multiple')
                    : _('mel_button_' + action),
                text: _('mel_confirm_' + action),
            }));
        } else {
            MODx.Ajax.request(params);
        }

        return true;
    },

    enableObject: function () {
        this.loadMask.show();
        return this.actionObject('enable');
    },

    disableObject: function () {
        this.loadMask.show();
        return this.actionObject('disable');
    },

    removeObject: function () {
        return this.actionObject('remove', true);
    },
});
Ext.reg('mel-grid-objects', modExtraLayout.grid.Objects);