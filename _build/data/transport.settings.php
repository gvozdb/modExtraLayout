<?php
/** @var modX $modx */
/** @var array $sources */
$settings = [];
$tmp = [
    'main' => [
        'file_source' => [
            'xtype' => 'modx-combo-source',
            'value' => '',
        ],
        // 'some_setting' => [
        //     'xtype' => 'combo-boolean',
        //     'value' => true,
        // ],
    ],
    'backend' => [
        // 'backend_datetime_format' => [
        //     'xtype' => 'textfield',
        //     'value' => '%d.%m.%Y <span class="action-gray">%H:%M</span>',
        // ],
    ],
    'frontend' => [
        'frontend_main_css' => [
            'xtype' => 'textfield',
            'value' => '[[+cssUrl]]web/main.css',
        ],
        'frontend_main_js' => [
            'xtype' => 'textfield',
            'value' => '[[+jsUrl]]web/main.js',
        ],
    ],
];

foreach ($tmp as $area => $rows) {
    foreach ($rows as $k => $v) {
        /** @var modSystemSetting $setting */
        $setting = $modx->newObject('modSystemSetting');
        $setting->fromArray(array_merge([
            'namespace' => PKG_NAME_LOWER,
            'area' => PKG_NAME_SHORT . '_' . $area,
            'key' => PKG_NAME_SHORT . '_' . $k,
        ], $v), '', true, true);

        $settings[] = $setting;
    }
}
unset($tmp, $area, $rows, $k, $v);

return $settings;