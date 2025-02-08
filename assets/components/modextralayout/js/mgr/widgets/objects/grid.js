modExtraLayout.grid.Objects = function (config) {
    config = config || {};
    if (!config['id']) {
        config['id'] = 'mel-grid-objects';
    }
    config['actionPrefix'] = 'mgr/objects/';
    Ext.applyIf(config, {
        baseParams: {
            action: config['actionPrefix'] + 'getlist',
            sort: 'idx',
            dir: 'DESC',
        },
        multi_select: true,
        // pageSize: Math.round(MODx.config['default_per_page'] / 2),
        enableDragDrop: true,
        ddGroup: config['id'],
        ddAction: config['actionPrefix'] + 'sort',
    });
    modExtraLayout.grid.Objects.superclass.constructor.call(this, config);
};
Ext.extend(modExtraLayout.grid.Objects, modExtraLayout.grid.Default, {
    getFields: function (config) {
        return [
            'id',
            'idx',
            'parent_formatted',
            'group',
            'name',
            'description',
            'createdon',
            'active',
            'actions',
        ];
    },

    getColumns: function (config) {
        return [{
            header: _('mel_grid_id'),
            dataIndex: 'id',
            width: 50,
            sortable: true,
            fixed: true,
            resizable: false,
        }, {
            header: _('mel_grid_idx'),
            dataIndex: 'idx',
            width: 50,
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
            renderer: modExtraLayout.renderer['Group'],
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
            header: _('mel_grid_createdon'),
            dataIndex: 'createdon',
            width: 130,
            sortable: true,
            fixed: true,
            resizable: false,
            hidden: false,
            renderer: modExtraLayout.renderer['DateTime'],
        }, {
            header: _('mel_grid_active'),
            dataIndex: 'active',
            width: 60,
            sortable: true,
            fixed: true,
            resizable: false,
            renderer: modExtraLayout.renderer['Boolean'],
        }, {
            header: _('mel_grid_actions'),
            dataIndex: 'actions',
            id: 'actions',
            width: 130,
            sortable: false,
            fixed: true,
            resizable: false,
            renderer: modExtraLayout.renderer['Actions'],
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
            id: config['id'] + '-group',
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
        const w = MODx.load({
            xtype: 'mel-window-object-create',
            id: Ext.id(),
            listeners: {
                success: {fn: this._listenerRefresh, scope: this},
                // hide: {fn: this._listenerRefresh, scope: this},
                failure: {fn: this._listenerHandler, scope: this},
            },
        });
        w.reset();
        w.setValues({
            source: MODx.config['mel_file_source'] || MODx.config['default_media_source'],
            active: true,
        });
        w.show(e.target);
    },

    updateObject: function (btn, e, row, activeTab) {
        if (typeof(row) !== 'undefined') {
            this.menu.record = row.data;
        } else if (!this.menu.record) {
            return false;
        }
        const id = this.menu.record.id;

        if (typeof(activeTab) === 'undefined') {
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
                        const values = r['object'];
                        ['createdon', 'updatedon'].forEach(function (k) {
                            if (values[k]) {
                                values[k] = '' + values[k];
                            }
                        });

                        const w = MODx.load({
                            xtype: 'mel-window-object-update',
                            id: Ext.id(),
                            record: r,
                            activeTab: activeTab,
                            listeners: {
                                success: {fn: this._listenerRefresh, scope: this},
                                // hide: {fn: this._listenerRefresh, scope: this},
                                failure: {fn: this._listenerHandler, scope: this},
                            },
                        });
                        w.reset();
                        w.setValues(values);
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