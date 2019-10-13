/**
 *
 * @param value
 * @param props
 * @param row
 * @returns {*}
 * @constructor
 */
modExtraLayout.renderer.Actions = function (value, props, row) {
    var res = [];
    var cls, icon, title, action, item;
    if (typeof(value) === 'object') {
        for (var i in value) {
            if (!value.hasOwnProperty(i)) {
                continue;
            }
            var a = value[i];
            if (!a['button']) {
                continue;
            }

            icon = a['icon'] ? a['icon'] : '';
            if (typeof(a['cls']) === 'object') {
                if (typeof(a['cls']['button']) !== 'undefined') {
                    icon += ' ' + a['cls']['button'];
                }
            } else {
                cls = a['cls'] ? a['cls'] : '';
            }
            action = a['action'] ? a['action'] : '';
            title = a['title'] ? a['title'] : '';

            item = String.format(
                '<li class="{0}"><button class="btn btn-default {1}" action="{2}" title="{3}"></button></li>',
                cls, icon, action, title
            );

            res.push(item);
        }
    }

    return String.format(
        '<ul class="mel-grid-col__actions">{0}</ul>',
        res.join('')
    );
};

/**
 *
 * @param string
 * @returns {string}
 * @constructor
 */
modExtraLayout.renderer.DateTime = function (string) {
    if (string && string != '0000-00-00 00:00:00' && string != '-1-11-30 00:00:00' && string != 0) {
        var date = /^[-0-9]+$/.test(string)
            ? new Date(string * 1000)
            : new Date(string.replace(/(\d+)-(\d+)-(\d+)/, '$2/$3/$1'));
        var format = MODx.config['mel_backend_datetime_format'];
        if (!format) {
            format = '%d.%m.%Y <span class="action-gray">%H:%M</span>';
        }
        return strftime(format, date);
    }
    return '';
};

/**
 *
 * @param val
 * @returns {*}
 * @constructor
 */
modExtraLayout.renderer.Boolean = function (val) {
    return String.format(
        '<div class="mel-grid-col__boolean {0}">{1}</div>',
        val ? 'green' : 'red',
        _(val ? 'yes' : 'no')
    );
};


modExtraLayout.renderer.CustomField = function (val, props, row) {
    var rec = row['json'];
    return String.format(
        '<div class="mel-grid-col__customfield">{0}</div>',
        rec['customfield']
    );
};

modExtraLayout.renderer.Group = function (val, props, row) {
    var rec = row['json'];
    return String.format(
        '<div class="mel-grid-col__group mel-grid-col__{0}">{1}</div>',
        rec['group'] || '',
        rec['group'] ? _('mel_group_' + rec['group']) : ''
    );
};