modExtraLayout.grid.Default = function (config) {
    config = config || {};

    if (typeof(config['multi_select']) != 'undefined' && config['multi_select'] == true) {
        config['sm'] = new Ext.grid.CheckboxSelectionModel();
    }
    config['cls'] = (config['cls'] || 'main-wrapper') + ' mel-grid';

    Ext.applyIf(config, {
        url: modExtraLayout.config['connector_url'],
        baseParams: {},
        fields: this.getFields(config),
        columns: this.getColumns(config),
        tbar: this.getTopBar(config),
        listeners: this.getListeners(config),
        viewConfig: {
            forceFit: true,
            enableRowBody: true,
            autoFill: true,
            showPreview: true,
            scrollOffset: 0,
            getRowClass: function (rec) {
                var cls = [];
                if (rec.data['active'] != undefined && rec.data['active'] == 0) {
                    cls.push('mel-grid__row_disabled');
                }
                return cls.join(' ');
            },
        },
        paging: true,
        remoteSort: true,
        autoHeight: true,
    });
    modExtraLayout.grid.Default.superclass.constructor.call(this, config);

    //
    if (config['enableDragDrop'] && config['ddAction']) {
        this.on('render', function (grid) {
            grid._initDD(config);
        });
    }

    // Clear selection on grid refresh
    this.store.on('load', function () {
        if (this._getSelectedIds().length) {
            this.getSelectionModel().clearSelections();
        }
    }, this);
};
Ext.extend(modExtraLayout.grid.Default, MODx.grid.Grid, {
    getFields: function (config) {
        return [
            'id', 'actions',
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
            header: _('mel_grid_actions'),
            dataIndex: 'actions',
            id: 'actions',
            width: 100,
            sortable: false,
            fixed: true,
            resizable: false,
            renderer: modExtraLayout.utils.renderActions,
        }];
    },

    getTopBar: function (config) {
        return ['->', this.getSearchField(config)];
    },

    getSearchField: function (config, width) {
        return {
            xtype: 'mel-field-search',
            id: config.id + '__tbar-search',
            filterName: 'query',
            width: width || 250,
            listeners: {
                search: {fn: this._doFilter, scope: this},
                clear: {
                    fn: function (field) {
                        field.setValue('');
                        this._doFilter(field);
                    }, scope: this
                },
            }
        };
    },

    getListeners: function () {
        return {};
    },

    getMenu: function (grid, rowIndex) {
        var ids = this._getSelectedIds();
        var row = grid.getStore().getAt(rowIndex);
        var menu = modExtraLayout.utils.getMenu(row.data['actions'], this, ids);

        this.addContextMenuItem(menu);
    },

    onClick: function (e) {
        var elem = e.getTarget();
        if (elem.nodeName == 'BUTTON') {
            var row = this.getSelectionModel().getSelected();
            if (typeof(row) != 'undefined') {
                var action = elem.getAttribute('action');
                if (action == 'showMenu') {
                    var ri = this.getStore().find('id', row.id);
                    return this._showMenu(this, ri, e);
                } else if (typeof this[action] === 'function') {
                    this.menu.record = row.data;
                    return this[action](this, e);
                }
            }
        } else if (elem.nodeName == 'A' && elem.href.match(/(\?|\&)a=resource/)) {
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

    refresh: function () {
        this.getStore().reload();
        if (this.config['enableDragDrop'] == true) {
            this.getSelectionModel().clearSelections(true);
        }
    },

    _doFilter: function (field) {
        var name = field['filterName'] || field['name'];
        this.getStore().baseParams[name] = field.getValue();
        this.getBottomToolbar().changePage(1);
    },

    _doAction: function (action, action_prefix, confirm, check_id) {
        if (typeof(action) == 'undefined') {
            return false;
        }
        if (typeof(action_prefix) == 'undefined' || !action_prefix) {
            action_prefix = this['actionPrefix'];
        }
        if (typeof(confirm) == 'undefined') {
            confirm = false;
        }
        if (typeof(check_id) == 'undefined') {
            check_id = true;
        }
        var ids = this._getSelectedIds();
        if (check_id && !ids.length) {
            this.refresh();
            return false;
        }

        var params = {
            url: this.config['url'],
            params: {
                action: action_prefix + action,
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var grid = this;
                        this._listenerHandler(r, function () {
                            grid.refresh();
                        });
                    }, scope: this
                },
                failure: {
                    fn: function (r) {
                        var grid = this;
                        this._listenerHandler(r, function () {
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

    _getSelectedIds: function () {
        var ids = [];
        var selected = this.getSelectionModel().getSelections();

        for (var i in selected) {
            if (!selected.hasOwnProperty(i)) {
                continue;
            }
            ids.push(selected[i]['id']);
        }

        return ids;
    },

    _initDD: function (config) {
        var grid = this;
        var el = grid.getEl();

        new Ext.dd.DropTarget(el, {
            ddGroup: grid.ddGroup,
            notifyDrop: function (dd, e, data) {
                var store = grid.getStore();
                var target = store.getAt(dd.getDragData(e).rowIndex).id;
                var sources = [];
                if (data.selections.length < 1 || data.selections[0].id == target) {
                    return false;
                }
                for (var i in data.selections) {
                    if (!data.selections.hasOwnProperty(i)) {
                        continue;
                    }
                    var row = data.selections[i];
                    sources.push(row.id);
                }

                el.mask(_('loading'), 'x-mask-loading');
                MODx.Ajax.request({
                    url: config.url,
                    params: {
                        action: config.ddAction,
                        sources: Ext.util.JSON.encode(sources),
                        target: target,
                    },
                    listeners: {
                        success: {
                            fn: function () {
                                el.unmask();
                                grid.refresh();
                                if (typeof(grid.reloadTree) == 'function') {
                                    sources.push(target);
                                    grid.reloadTree(sources);
                                }
                            }, scope: grid
                        },
                        failure: {
                            fn: function () {
                                el.unmask();
                            }, scope: grid
                        },
                    }
                });
            },
        });
    },

    _loadStore: function () {
        this.store = new Ext.data.JsonStore({
            url: this.config.url,
            baseParams: this.config.baseParams || {action: this.config.action || 'getList'},
            fields: this.config.fields,
            root: 'results',
            totalProperty: 'total',
            remoteSort: this.config.remoteSort || false,
            storeId: this.config.storeId || Ext.id(),
            autoDestroy: true,
            listeners: {
                load: function (store, rows, data) {
                    store.sortInfo = {
                        field: data.params['sort'] || 'id',
                        direction: data.params['dir'] || 'ASC',
                    };
                    Ext.getCmp('modx-content').doLayout();
                }
            }
        });
    },

    _listenerHandler: function (resp, callback) {
        if (typeof(callback) == 'function') {
            callback(resp);
        }

        var success = false;
        var message = '';
        if (typeof(resp['message']) != 'undefined') {
            success = resp['success'];
            message = resp['message'];
        } else if (typeof(resp.a.result['message']) != 'undefined') {
            success = resp.a.result['success'];
            message = resp.a.result['message'];
        }

        if (message) {
            MODx.msg.alert(_(success ? 'success' : 'failure'), message);
        }
    },
});
Ext.reg('mel-grid-default', modExtraLayout.grid.Default);