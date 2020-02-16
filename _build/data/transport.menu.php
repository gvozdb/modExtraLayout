<?php
/** @var modX $modx */
/** @var array $sources */
$menus = [];
$tmp = [
    PKG_NAME_SHORT . '_menu_home' => [
        'action' => 'home',
        'description' => PKG_NAME_SHORT . '_menu_home_desc',
        // 'icon' => '<i class="icon icon-large icon-modx"></i>',
    ],

    // // Submenu
    // PKG_NAME_SHORT . '_menu_inner' => [
    //     'action' => 'inner',
    //     'parent' => PKG_NAME_SHORT . '_menu_home',
    //     'description' => PKG_NAME_SHORT . '_menu_inner_desc',
    //     // 'icon' => '<i class="icon icon-large icon-modx"></i>',
    // ],
];

foreach ($tmp as $k => $v) {
    /** @var modMenu $menu */
    $menu = $modx->newObject('modMenu');
    $menu->fromArray(array_merge([
        'text' => $k,
        'parent' => 'components',
        'namespace' => PKG_NAME_LOWER,
        'icon' => '',
        'menuindex' => 0,
        'params' => '',
        'handler' => '',
    ], $v), '', true, true);
    $menus[] = $menu;
}
unset($menu, $i);

return $menus;