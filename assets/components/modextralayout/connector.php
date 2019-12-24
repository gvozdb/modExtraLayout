<?php
if (file_exists(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php')) {
    /** @noinspection PhpIncludeInspection */
    require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
} else {
    require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
}
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH . 'index.php';
/** @var modExtraLayout $modExtraLayout */
$modExtraLayout = $modx->getService('modextralayout', 'modExtraLayout',
    $modx->getOption('mel_core_path', null, $modx->getOption('core_path') . 'components/modextralayout/') . 'model/modextralayout/');
$modx->lexicon->load('modextralayout:default');

// handle request
$corePath = $modx->getOption('mel_core_path', null, $modx->getOption('core_path') . 'components/modextralayout/');
$path = $modx->getOption('processorsPath', $modExtraLayout->config, $corePath . 'processors/');
$modx->getRequest();

/** @var modConnectorRequest $request */
$request = $modx->request;
$request->handleRequest([
    'processors_path' => $path,
    'location' => '',
]);