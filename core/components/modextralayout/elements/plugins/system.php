<?php
/** @var modX $modx */
/** @var modExtraLayout $mel */
$mel = $modx->getService('modextralayout', 'modExtraLayout',
    $modx->getOption('mel_core_path', null, MODX_CORE_PATH . 'components/modextralayout/') . 'model/modextralayout/');
$className = 'mel' . ucfirst($modx->event->name);
$modx->loadClass('melPlugin', $mel->config['pluginsPath'], true, true);
$modx->loadClass($className, $mel->config['pluginsPath'], true, true);
if (class_exists($className)) {
    $handler = new $className($modx, $scriptProperties);
    $handler->run();
} else {
    // Удаляем событие у плагина, если такого класса не существует
    $event = $modx->getObject('modPluginEvent', array(
        'pluginid' => $modx->event->plugin->get('id'),
        'event' => $modx->event->name,
    ));
    if ($event instanceof modPluginEvent) {
        $event->remove();
    }
}
return;