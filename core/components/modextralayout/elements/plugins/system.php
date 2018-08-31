<?php
/** @var modX $modx */
/** @var modExtraLayout $mel */
/** @var array $scriptProperties */

if (!$mel = $modx->getService('modextralayout', 'modExtraLayout',
    $modx->getOption('mel_core_path', null, MODX_CORE_PATH . 'components/modextralayout/') . 'model/modextralayout/')) {
    return;
}

$className = 'mel' . $modx->event->name;
$modx->loadClass('melPlugin', $mel->config['pluginsPath'], true, true);
$modx->loadClass($className, $mel->config['pluginsPath'], true, true);
/** @var melPlugin $handler */
if (class_exists($className)) {
    $handler = new $className($mel, $scriptProperties);
    $handler->run();
} else {
    // Удаляем событие у плагина, если такого класса не существует
    if ($event = $modx->getObject('modPluginEvent', array(
        'pluginid' => $modx->event->plugin->get('id'),
        'event' => $modx->event->name,
    ))) {
        $event->remove();
    }
}
return;