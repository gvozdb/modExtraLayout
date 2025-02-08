/**
 * @param config
 * @constructor
 */
modExtraLayout.gridlocal.Files = function (config) {
    config = config || {};
    if (!config['id']) {
        config['id'] = 'mel-gridlocal-files';
    }
    Ext.applyIf(config, {
        tbarCls: 'mel-grid-toptbar_field',
        style: {marginTop: '0px', padding: 0},
    });
    modExtraLayout.gridlocal.Files.superclass.constructor.call(this, config);
};
Ext.extend(modExtraLayout.gridlocal.Files, modExtraLayout.gridlocal.Default, {
    getFields: function (config) {
        return [
            'file',
            'actions',
        ];
    },

    getColumns: function (config) {
        return [{
            header: _('mel_grid_file'),
            dataIndex: 'file',
            width: 175,
        }, {
            header: _('mel_grid_actions'),
            dataIndex: 'actions',
            id: 'actions',
            width: 100,
            sortable: false,
            fixed: true,
            resizable: false,
            renderer: modExtraLayout.renderer['Actions'],
        }];
    },

    addObject: function(data, btn, e) {
        data = typeof(data) === 'object' ? data : {}
        if (!data.file) {
            return false;
        }
        const store = this.getStore();
        if (store.findExact('file', data.file) !== -1) {
            MODx.msg.alert(_('error'), _('mel_err_unique_files'));
            return false;
        }
        modExtraLayout.gridlocal.Files.superclass.addObject.bind(this)(data, btn, e);
    },

    getTopBar: function (config) {
        return [
            {
                xtype: 'modx-combo-browser',
                name: 'filebrowser',
                id: config.id + '-filebrowser',
                emptyText: _('mel_field_file_select'),
                anchor: '100%',
                hideFiles: true,
                source: config['source'] || MODx.config['mel_file_source'] || MODx.config['default_media_source'],
                hideSourceCombo: true,
                listeners: {
                    select: {
                        fn: function(file) {
                            const $grid = this.scope
                            $grid.addObject({
                                file: file.pathRelative,
                                urlpath: file.pathRelative,
                                filepath: file.pathname,
                            })
                            this.setValue('')
                        },
                    },
                },
            }
        ];
    },
});
Ext.reg('mel-gridlocal-files', modExtraLayout.gridlocal.Files);
