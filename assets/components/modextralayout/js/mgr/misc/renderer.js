modExtraLayout.renderer.Boolean = function (val) {
    return val
        ? String.format('<span class="green">{0}</span>', _('yes'))
        : String.format('<span class="red">{0}</span>', _('no'));
};

modExtraLayout.renderer.Actions = function (value, props, row) {
    var res = [];
    var cls, icon, title, action, item;
    if (typeof(value) == 'object') {
        for (var i in value) {
            if (!value.hasOwnProperty(i)) {
                continue;
            }
            var a = value[i];
            if (!a['button']) {
                continue;
            }

            icon = a['icon'] ? a['icon'] : '';
            if (typeof(a['cls']) == 'object') {
                if (typeof(a['cls']['button']) != 'undefined') {
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
        '<ul class="mel-grid__row-actions">{0}</ul>',
        res.join('')
    );
};

modExtraLayout.renderer.GridCustomField = function (val, props, row) {
    var rec = row['json'];
    return String.format(
        '<div class="mel-grid__row-customfield">{0}</div>',
        rec['customfield']
    );
};