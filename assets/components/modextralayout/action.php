<?php

/** @var modX $modx */
/** @var modExtraLayout $mel */

// Подключаем MODX
if (!isset($modx)) {
    define('MODX_API_MODE', true);
    require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php';
    $modx->getService('error', 'error.modError');
    $modx->getRequest();
    $modx->setLogLevel(modX::LOG_LEVEL_ERROR);
    $modx->setLogTarget('FILE');
    $modx->error->message = null;
    $modx->lexicon->load('default');
}
$ctx = !empty($_REQUEST['ctx']) ? $_REQUEST['ctx'] : $modx->context->get('key');
if ($ctx != $modx->context->get('key')) {
    $modx->switchContext($ctx);
}

// Подключаем класс modExtraLayout
if (!$mel = $modx->getService('modextralayout', 'modExtraLayout', MODX_CORE_PATH . 'components/modextralayout/model/modextralayout/')) {
    exit($modx->toJSON(array('success' => false, 'message' => 'Class modExtraLayout not found')));
}
$mel->initialize($ctx, array('jsonResponse' => true));

//
if (empty($_REQUEST['action'])) {
    exit($mel->tools->failure('Access denied'));
}

switch ($_REQUEST['action']) {
    /**
     *
     */
    // case 'object/vote':
    //     $object = (int)$_REQUEST['object'];
    //     $value = (int)$_REQUEST['value'];
    //     if (empty($object) || empty($value)) {
    //         $response = $nkn->tools->failure('nkn_err_ns');
    //         break;
    //     }
    //
    //     $response = $nkn->tools->runProcessor('mgr/vote/doit', array(
    //         'class' => 'nknObject',
    //         'object' => $object,
    //         'value' => $value,
    //     ));
    //     if ($error = $nkn->tools->formatProcessorErrors($response)) {
    //         $modx->log(modX::LOG_LEVEL_ERROR, '[Nekino] Ошибка при попытке проголосовать: ' . print_r($error, 1));
    //         $response = $nkn->tools->failure($error, $_REQUEST);
    //         break;
    //     } else {
    //         $response = $nkn->tools->success('', $response->getObject());
    //     }
    //     break;

    default:
        $response = $mel->tools->failure('Access denied');
}

@session_write_close();
exit($response);