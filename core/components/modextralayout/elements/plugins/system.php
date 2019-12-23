<?php
/** @var modX $modx */
/** @var modExtraLayout $mel */
/** @var array $scriptProperties */
if (!$mel = $modx->getService('modextralayout', 'modExtraLayout',
    $modx->getOption('mel_core_path', null, MODX_CORE_PATH . 'components/modextralayout/') . 'model/modextralayout/')) {
    return;
}
$modx->loadClass('melPlugin', $mel->config['pluginsPath'], true, true);

//
$exists_single = false;
$className = 'mel' . $modx->event->name;
if (file_exists($mel->config['pluginsPath'] . strtolower($className) . '.class.php')) {
    $modx->loadClass($className, $mel->config['pluginsPath'], true, true);
    if ($exists_single = class_exists($className)) {
        /** @var melPlugin $handlerSingle */
        $handlerSingle = new $className($mel, $scriptProperties);
        $handlerSingle->run();
        unset($handlerSingle);
    }
}
unset($className);

//
$classes = [];
$exists_group = false;
foreach (glob($mel->config['pluginsPath'] . 'melplugin*.class.php') as $filepath) {
    $filename = pathinfo($filepath, PATHINFO_BASENAME);
    if (in_array($filename, ['melplugin.class.php'])) {
        continue;
    }
    $className = preg_replace('/\.class\.php$/i', '', $filename);
    $modx->loadClass($className, $mel->config['pluginsPath'], true, true);
    if (class_exists($className)) {
        /** @var melPlugin $handlerGroup */
        $handlerGroup = new $className($mel, $scriptProperties);
        if (method_exists($handlerGroup, $modx->event->name)) {
            // $handlerGroup->{$modx->event->name}();

            $priority = @$handlerGroup->priority ?: 0;
            $classes[$priority] = isset($classes[$priority]) ? $classes[$priority] : [];
            $classes[$priority][] = $handlerGroup;
            $exists_group = true;
        }
        unset($handlerGroup);
    }
}
ksort($classes);
foreach ($classes as $list) {
    foreach ($list as $class) {
        $class->{$modx->event->name}();
    }
}
unset($classes, $list, $class);

// //
// $modx->loadClass('melPluginGroup', $mel->config['pluginsPath'], true, true);
// if (class_exists('melPluginGroup')) {
//     /** @var melPluginGroup $handlerGroup */
//     $handlerGroup = new melPluginGroup($mel, $scriptProperties);
//     if ($exists_group = method_exists($handlerGroup, $modx->event->name)) {
//         $handlerGroup->{$modx->event->name}();
//     }
//     unset($handlerGroup);
// }

// Удаляем событие у плагина, если такого класса не существует
if ($exists_group === false && $exists_single === false) {
    if ($event = $modx->getObject('modPluginEvent', array(
        'pluginid' => $modx->event->plugin->get('id'),
        'event' => $modx->event->name,
    ))) {
        $event->remove();
    }
}
return;