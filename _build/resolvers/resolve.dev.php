<?php
/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx = &$transport->xpdo;

    $settings = array(
        PKG_NAME_SHORT . '_core_path' => (MODX_BASE_PATH . PKG_NAME . '/core/components/' . PKG_NAME_LOWER . '/'),
        PKG_NAME_SHORT . '_assets_url' => (MODX_BASE_URL . PKG_NAME . '/assets/components/' . PKG_NAME_LOWER . '/'),
        PKG_NAME_SHORT . '_assets_path' => (MODX_BASE_PATH . PKG_NAME . '/assets/components/' . PKG_NAME_LOWER . '/'),
    );

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            if (PKG_DEV_MODE) {
                foreach ($settings as $k => $v) {
                    if (!$modx->getCount('modSystemSetting', array('key' => $k))) {
                        /** @var modSystemSetting $setting */
                        $setting = $modx->newObject('modSystemSetting');
                        $setting->fromArray(array(
                            'namespace' => PKG_NAME_LOWER,
                            'area' => PKG_NAME_SHORT . '_dev',
                            'xtype' => 'textfield',
                            'key' => $k,
                            'value' => $v,
                        ), '', true, true);
                        $setting->save();
                        $modx->log(modX::LOG_LEVEL_INFO, 'Added setting <b>' . $k . '</b> = ' . $v);
                    }
                }
            } else {
                if ($modx->removeCollection('modSystemSetting', array(
                    'key:IN' => array_keys($settings),
                ))
                ) {
                    $modx->log(modX::LOG_LEVEL_INFO, 'Removed dev settings');
                }
            }
            unset($settings);
            break;

        case xPDOTransport::ACTION_UNINSTALL:
            break;
    }
}

return true;