<?php

if ($object->xpdo) {
    /** @var modX $modx */
    $modx = &$object->xpdo;

    /** @var array $options */
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $tmp = explode('/', MODX_ASSETS_URL);
            $assets = $tmp[count($tmp) - 2];
            $dir = 'mel'; // Like as: /assets/mel/

            $properties = [
                'name' => 'modExtraLayout',
                'description' => 'Default media source for other files from modExtraLayout package',
                'class_key' => 'sources.modFileMediaSource',
                'properties' => [
                    'basePath' => [
                        'name' => 'basePath',
                        'desc' => 'prop_file.basePath_desc',
                        'type' => 'textfield',
                        'lexicon' => 'core:source',
                        'value' => $assets . '/' . $dir . '/',
                    ],
                    'baseUrl' => [
                        'name' => 'baseUrl',
                        'desc' => 'prop_file.baseUrl_desc',
                        'type' => 'textfield',
                        'lexicon' => 'core:source',
                        'value' => $assets . '/' . $dir . '/',
                    ],
                    'allowedFileTypes' => [
                        'name' => 'allowedFileTypes',
                        'desc' => 'mel_file_source_allowedFileTypes_desc',
                        'type' => 'textfield',
                        'lexicon' => 'modextralayout:setting',
                        'value' => 'jpg,jpeg,png,gif,mp4,zip,rar,bz2,gz,tar,csv,xls,xlsx,doc,docx,ppt,pptx,odt,pdf,txt,scs',
                    ],
                ],
                'is_stream' => 1,
            ];

            /** @var $source modMediaSource */
            if (!$source = $modx->getObject('sources.modMediaSource', ['name' => $properties['name']])) {
                $source = $modx->newObject('sources.modMediaSource', $properties);
            } else {
                $default = $source->get('properties');
                foreach ($properties['properties'] as $k => $v) {
                    if (!array_key_exists($k, $default)) {
                        $default[$k] = $v;
                    }
                }
                $source->set('properties', $default);
            }
            $source->save();

            foreach (['mel_file_source'] as $setting_name) {
                if ($setting = $modx->getObject('modSystemSetting', ['key' => $setting_name])) {
                    if (!$setting->get('value')) {
                        $setting->set('value', $source->get('id'));
                        $setting->save();
                    }
                    unset($setting);
                }
            }

            @mkdir(MODX_ASSETS_PATH . $dir . '/', 0755, true);
            break;

        case xPDOTransport::ACTION_UNINSTALL:
            break;
    }
}

return true;
