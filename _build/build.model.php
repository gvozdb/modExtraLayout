<?php

if (!defined('MODX_BASE_PATH')) {
    require 'build.config.php';
}

// define sources
$root = dirname(dirname(__FILE__)) . '/';
$sources = [
    'root' => $root,
    'build' => $root . '_build/',
    'source_core' => $root . 'core/components/' . PKG_NAME_LOWER,
    'model' => $root . 'core/components/' . PKG_NAME_LOWER . '/model/',
    'schema' => $root . 'core/components/' . PKG_NAME_LOWER . '/model/schema/',
    'xml' => $root . 'core/components/' . PKG_NAME_LOWER . '/model/schema/' . PKG_NAME_LOWER . '.mysql.schema.xml',
];
unset($root);

/** @noinspection PhpIncludeInspection */
require MODX_CORE_PATH . 'model/modx/modx.class.php';
/** @noinspection PhpIncludeInspection */
require $sources['build'] . '/includes/functions.php';

$loglevel = modX::LOG_LEVEL_INFO;
if ($loglevel_key = strtoupper(@$_GET['loglevel']?:'')) {
    $loglevel = defined('modX::LOG_LEVEL_' . $loglevel_key)
        ? constant('modX::LOG_LEVEL_' . $loglevel_key)
        : $loglevel;
}

$modx = new modX();
$modx->initialize('mgr');
$modx->getService('error', 'error.modError');
$modx->setLogLevel($loglevel);
$modx->setLogTarget('ECHO');
$modx->loadClass('transport.modPackageBuilder', '', false, true);
if (!XPDO_CLI_MODE && @$_GET['html'] !== '0') {
    echo '<pre>';
}

/** @var xPDOManager $manager */
$manager = $modx->getManager();
/** @var xPDOGenerator $generator */
$generator = $manager->getGenerator();

// Remove old model
rrmdir($sources['model'] . PKG_NAME_LOWER . '/mysql');

// Generate a new one
$generator->parseSchema($sources['xml'], $sources['model']);

$modx->log(modX::LOG_LEVEL_INFO, 'Model generated.');
if (!XPDO_CLI_MODE && @$_GET['html'] !== '0') {
    echo '</pre>';
}