<?php

// define package
define('PKG_DEV_MODE', true);
define('PKG_ENCRYPT', false);
define('PKG_NAME', 'modExtraLayout');
define('PKG_NAME_LOWER', strtolower(PKG_NAME));
define('PKG_NAME_SHORT', 'mel');

define('PKG_VERSION', '1.0.0');
define('PKG_RELEASE', 'beta');
define('PKG_AUTO_INSTALL', true);

if (PKG_DEV_MODE) {
    define('PKG_NAMESPACE_PATH', '{base_path}' . PKG_NAME . '/core/components/' . PKG_NAME_LOWER . '/');
} else {
    define('PKG_NAMESPACE_PATH', '{core_path}components/' . PKG_NAME_LOWER . '/');
}

// define paths
if (isset($_SERVER['MODX_BASE_PATH'])) {
    define('MODX_BASE_PATH', $_SERVER['MODX_BASE_PATH']);
} elseif (file_exists(dirname(dirname(dirname(__FILE__))) . '/core')) {
    define('MODX_BASE_PATH', dirname(dirname(dirname(__FILE__))) . '/');
} else {
    define('MODX_BASE_PATH', dirname(dirname(dirname(dirname(__FILE__)))) . '/');
}
define('MODX_CORE_PATH', MODX_BASE_PATH . 'core/');
define('MODX_MANAGER_PATH', MODX_BASE_PATH . 'manager/');
define('MODX_CONNECTORS_PATH', MODX_BASE_PATH . 'connectors/');
define('MODX_ASSETS_PATH', MODX_BASE_PATH . 'assets/');

// define urls
define('MODX_BASE_URL', '/');
define('MODX_CORE_URL', MODX_BASE_URL . 'core/');
define('MODX_MANAGER_URL', MODX_BASE_URL . 'manager/');
define('MODX_CONNECTORS_URL', MODX_BASE_URL . 'connectors/');
define('MODX_ASSETS_URL', MODX_BASE_URL . 'assets/');

// define build options
define('BUILD_MENU_UPDATE', true);
define('BUILD_SETTING_UPDATE', false);
define('BUILD_CHUNK_UPDATE', (PKG_DEV_MODE ?: false));
define('BUILD_SNIPPET_UPDATE', true);
define('BUILD_PLUGIN_UPDATE', true);
// define('BUILD_EVENT_UPDATE', true);
// define('BUILD_POLICY_UPDATE', true);
// define('BUILD_POLICY_TEMPLATE_UPDATE', true);
// define('BUILD_PERMISSION_UPDATE', true);

define('BUILD_CHUNK_STATIC', false);
define('BUILD_SNIPPET_STATIC', false);
define('BUILD_PLUGIN_STATIC', false);

$BUILD_RESOLVERS_BEFORE = [
    // 'setup',
];

$BUILD_RESOLVERS_AFTER = [
    // 'extension',
    'dev',
    'tables',
    'sources',
    'chunks',
];