<?php
/** @var modX $modx */
/** @var array $sources */
$menus = [];
$tmp = [
    'modextralayout' => [
        'description' => PKG_NAME_SHORT . '_menu_desc',
        'action' => 'home',
        // 'icon' => '<i class="icon icon-large icon-modx"></i>',
    ],
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