<?php
/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx = &$transport->xpdo;

    $settings = array(
        'mel_core_path' => (MODX_BASE_PATH . 'modExtraLayout/core/components/modextralayout/'),
        'mel_assets_url' => (MODX_BASE_URL . 'modExtraLayout/assets/components/modextralayout/'),
        'mel_assets_path' => (MODX_BASE_PATH . 'modExtraLayout/assets/components/modextralayout/'),
    );

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            if (PKG_DEV_MODE) {
                foreach ($settings as $k => $v) {
                    if (!$modx->getCount('modSystemSetting', ['key' => $k])) {
                        /** @var modSystemSetting $setting */
                        $setting = $modx->newObject('modSystemSetting');
                        $setting->fromArray([
                            'namespace' => 'modextralayout',
                            'area' => 'mel_dev',
                            'xtype' => 'textfield',
                            'key' => $k,
                            'value' => $v,
                        ], '', true, true);
                        $setting->save();
                        $modx->log(modX::LOG_LEVEL_INFO, 'Added setting <b>' . $k . '</b> = ' . $v);
                    }
                }
            } else {
                if ($modx->removeCollection('modSystemSetting', ['key:IN' => array_keys($settings)])) {
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