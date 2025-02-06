<?php

$mtime = microtime();
$mtime = explode(' ', $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

header('Content-Type:text/html;charset=utf-8');

require_once 'build.config.php';
// Refresh model
if (file_exists('build.model.php')) {
    require_once 'build.model.php';
}

// define sources
$root = dirname(dirname(__FILE__)) . '/';
$sources = [
    'root' => $root,
    'build' => $root . '_build/',
    'data' => $root . '_build/data/',
    'resolvers' => $root . '_build/resolvers/',
    'chunks' => $root . 'core/components/' . PKG_NAME_LOWER . '/elements/chunks/',
    'snippets' => $root . 'core/components/' . PKG_NAME_LOWER . '/elements/snippets/',
    'plugins' => $root . 'core/components/' . PKG_NAME_LOWER . '/elements/plugins/',
    'lexicon' => $root . 'core/components/' . PKG_NAME_LOWER . '/lexicon/',
    'docs' => $root . 'core/components/' . PKG_NAME_LOWER . '/docs/',
    'pages' => $root . 'core/components/' . PKG_NAME_LOWER . '/elements/pages/',
    'source_assets' => $root . 'assets/components/' . PKG_NAME_LOWER,
    'source_core' => $root . 'core/components/' . PKG_NAME_LOWER,
];
unset($root);

require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
require_once $sources['build'] . '/includes/functions.php';

$loglevel = modX::LOG_LEVEL_INFO;
if ($loglevel_key = strtoupper(@$_GET['loglevel']?:'')) {
    $loglevel = defined('modX::LOG_LEVEL_' . $loglevel_key)
        ? constant('modX::LOG_LEVEL_' . $loglevel_key)
        : $loglevel;
}

$modx = new modX();
$modx->initialize('mgr');
$modx->setLogLevel($loglevel);
$modx->setLogTarget('ECHO');
$modx->getService('error', 'error.modError');
$modx->loadClass('transport.modPackageBuilder', '', false, true);
if (!XPDO_CLI_MODE && @$_GET['html'] !== '0') {
    echo '<pre>';
}

//
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME_LOWER, PKG_VERSION, PKG_RELEASE);

//
if (PKG_ENCRYPT) {
    $username = '';
    $api_key = '';
    if ($packageWithProvider = $modx->getObject('transport.modTransportPackage', [
        'package_name' => PKG_NAME,
        'provider:!=' => 0,
    ])) {
        /** @var modTransportProvider $provider */
        if ($provider = $packageWithProvider->getOne('Provider')) {
            $username = $provider->username;
            $api_key = $provider->api_key;
        }
    }
    unset($packageWithProvider, $provider);

    $client = $modx->getService('rest.modRestCurlClient');
    $result = $client->request('https://modstore.pro/extras/package/', 'encode', 'POST', [
        'package' => PKG_NAME,
        'http_host' => $modx->getOption('http_host'),
        'username' => $username,
        'api_key' => $api_key,
        'version' => PKG_VERSION . '-' . PKG_RELEASE,
        'vehicle_version' => '2.0.0'
    ], ['contentType' => 'application/xml']);
    $data = new SimpleXMLElement($result);
    if (!empty($data->key)) {
        $modx->log(modX::LOG_LEVEL_INFO, 'Key: ' . $data->key);
    } elseif (!empty($data->message)) {
        $modx->log(modX::LOG_LEVEL_INFO, 'Error: ' . $data->message);
    }

    define('PKG_ENCODE_KEY', $data->key);

    $builder->package->put([
        'source' => $sources['source_core'] . '/model/encryptedvehicle.class.php',
        'target' => "return MODX_CORE_PATH . 'components/" . PKG_NAME_LOWER . "/model/';",
    ], [
        'vehicle_class' => 'xPDOFileVehicle',
        xPDOTransport::UNINSTALL_FILES => false,
    ]);

    $builder->putVehicle($builder->createVehicle([
        'source' => $sources['resolvers'] . 'resolve.encryption.php',
    ], ['vehicle_class' => 'xPDOScriptVehicle']));

    $modx->loadClass('transport.xPDOObjectVehicle', XPDO_CORE_PATH, true, true);
    require_once $sources['source_core'] . '/model/encryptedvehicle.class.php';
}

//
$builder->registerNamespace(PKG_NAME_LOWER, false, true, PKG_NAMESPACE_PATH);
$modx->log(modX::LOG_LEVEL_INFO, 'Created Transport Package and Namespace.');

// load system settings
if (defined('BUILD_SETTING_UPDATE')) {
    $settings = include $sources['data'] . 'transport.settings.php';
    if (!is_array($settings)) {
        $modx->log(modX::LOG_LEVEL_ERROR, 'Could not package in settings.');
    } else {
        $attributes = [
            xPDOTransport::UNIQUE_KEY => 'key',
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => BUILD_SETTING_UPDATE,
        ];
        foreach ($settings as $setting) {
            $vehicle = $builder->createVehicle($setting, $attributes);
            $builder->putVehicle($vehicle);
        }
        $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($settings) . ' System Settings.');
    }
    unset($settings, $setting, $attributes);
}

// load plugins events
if (defined('BUILD_EVENT_UPDATE')) {
    $events = include $sources['data'] . 'transport.events.php';
    if (!is_array($events)) {
        $modx->log(modX::LOG_LEVEL_ERROR, 'Could not package in events.');
    } else {
        $attributes = [
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => BUILD_EVENT_UPDATE,
        ];
        foreach ($events as $event) {
            $vehicle = $builder->createVehicle($event, $attributes);
            $builder->putVehicle($vehicle);
        }
        $modx->log(xPDO::LOG_LEVEL_INFO, 'Packaged in ' . count($events) . ' Plugins events.');
    }
    unset ($events, $event, $attributes);
}

// package in default access policy
if (defined('BUILD_POLICY_UPDATE')) {
    $attributes = [
        xPDOTransport::PRESERVE_KEYS => false,
        xPDOTransport::UNIQUE_KEY => ['name'],
        xPDOTransport::UPDATE_OBJECT => BUILD_POLICY_UPDATE,
    ];
    $policies = include $sources['data'] . 'transport.policies.php';
    if (!is_array($policies)) {
        $modx->log(modX::LOG_LEVEL_FATAL, 'Adding policies failed.');
    }
    foreach ($policies as $policy) {
        $vehicle = $builder->createVehicle($policy, $attributes);
        $builder->putVehicle($vehicle);
    }
    $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($policies) . ' Access Policies.');
    flush();
    unset($policies, $policy, $attributes);
}

// package in default access policy templates
if (defined('BUILD_POLICY_TEMPLATE_UPDATE')) {
    $templates = include dirname(__FILE__) . '/data/transport.policytemplates.php';
    $attributes = [
        xPDOTransport::PRESERVE_KEYS => false,
        xPDOTransport::UNIQUE_KEY => ['name'],
        xPDOTransport::UPDATE_OBJECT => BUILD_POLICY_TEMPLATE_UPDATE,
        xPDOTransport::RELATED_OBJECTS => true,
        xPDOTransport::RELATED_OBJECT_ATTRIBUTES => [
            'Permissions' => [
                xPDOTransport::PRESERVE_KEYS => false,
                xPDOTransport::UPDATE_OBJECT => BUILD_PERMISSION_UPDATE,
                xPDOTransport::UNIQUE_KEY => ['template', 'name'],
            ],
        ],
    ];
    if (is_array($templates)) {
        foreach ($templates as $template) {
            $vehicle = $builder->createVehicle($template, $attributes);
            $builder->putVehicle($vehicle);
        }
        $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($templates) . ' Access Policy Templates.');
        flush();
    } else {
        $modx->log(modX::LOG_LEVEL_ERROR, 'Could not package in Access Policy Templates.');
    }
    unset ($templates, $template, $attributes);
}

// Load menus
if (defined('BUILD_MENU_UPDATE')) {
    $menus = include $sources['data'] . 'transport.menu.php';
    $attributes = [
        xPDOTransport::PRESERVE_KEYS => true,
        xPDOTransport::UPDATE_OBJECT => BUILD_MENU_UPDATE,
        xPDOTransport::UNIQUE_KEY => 'text',
        xPDOTransport::RELATED_OBJECTS => true,
    ];
    if (is_array($menus)) {
        foreach ($menus as $menu) {
            $vehicle = $builder->createVehicle($menu, $attributes);
            $builder->putVehicle($vehicle);
            /** @var modMenu $menu */
            $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in menu "' . $menu->get('text') . '".');
        }
    } else {
        $modx->log(modX::LOG_LEVEL_ERROR, 'Could not package in menu.');
    }
    unset($vehicle, $menus, $menu, $attributes);
}

// create category
$modx->log(xPDO::LOG_LEVEL_INFO, 'Created category.');
/** @var modCategory $category */
$category = $modx->newObject('modCategory');
$category->set('category', PKG_NAME);
// create category vehicle
$attr = [
    xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
];
if (PKG_ENCRYPT) {
    $attr['vehicle_class'] = 'encryptedVehicle';
    $attr[xPDOTransport::ABORT_INSTALL_ON_VEHICLE_FAIL] = true;
}

// add snippets
if (defined('BUILD_SNIPPET_UPDATE')) {
    $attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['Snippets'] = [
        xPDOTransport::PRESERVE_KEYS => false,
        xPDOTransport::UPDATE_OBJECT => BUILD_SNIPPET_UPDATE,
        xPDOTransport::UNIQUE_KEY => 'name',
    ];
    $snippets = include $sources['data'] . 'transport.snippets.php';
    if (!is_array($snippets)) {
        $modx->log(modX::LOG_LEVEL_ERROR, 'Could not package in snippets.');
    } else {
        $category->addMany($snippets);
        $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($snippets) . ' snippets.');
    }
}

// add chunks
if (defined('BUILD_CHUNK_UPDATE')) {
    $attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['Chunks'] = [
        xPDOTransport::PRESERVE_KEYS => false,
        xPDOTransport::UPDATE_OBJECT => BUILD_CHUNK_UPDATE,
        xPDOTransport::UNIQUE_KEY => 'name',
    ];
    $chunks = include $sources['data'] . 'transport.chunks.php';
    if (!is_array($chunks)) {
        $modx->log(modX::LOG_LEVEL_ERROR, 'Could not package in chunks.');
    } else {
        $category->addMany($chunks);
        $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($chunks) . ' chunks.');
    }
}

// add plugins
if (defined('BUILD_PLUGIN_UPDATE')) {
    $attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['Plugins'] = [
        xPDOTransport::PRESERVE_KEYS => false,
        xPDOTransport::UPDATE_OBJECT => BUILD_PLUGIN_UPDATE,
        xPDOTransport::UNIQUE_KEY => 'name',
    ];
    $attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['PluginEvents'] = [
        xPDOTransport::PRESERVE_KEYS => true,
        xPDOTransport::UPDATE_OBJECT => BUILD_PLUGIN_UPDATE,
        xPDOTransport::UNIQUE_KEY => ['pluginid', 'event'],
    ];
    $plugins = include $sources['data'] . 'transport.plugins.php';
    if (!is_array($plugins)) {
        $modx->log(modX::LOG_LEVEL_ERROR, 'Could not package in plugins.');
    } else {
        $category->addMany($plugins);
        $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($plugins) . ' plugins.');
    }
}

$vehicle = $builder->createVehicle($category, $attr);

/** @var array $BUILD_RESOLVERS_BEFORE */
foreach ($BUILD_RESOLVERS_BEFORE as $resolver) {
    if ($vehicle->resolve('php', ['source' => $sources['resolvers'] . 'resolve.' . $resolver . '.php'])) {
        $modx->log(modX::LOG_LEVEL_INFO, 'Added resolver "' . $resolver . '" to category.');
    } else {
        $modx->log(modX::LOG_LEVEL_INFO, 'Could not add resolver "' . $resolver . '" to category.');
    }
}

// now pack in resolvers
$vehicle->resolve('file', [
    'source' => $sources['source_assets'],
    'target' => "return MODX_ASSETS_PATH . 'components/';",
]);
$vehicle->resolve('file', [
    'source' => $sources['source_core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
]);

/** @var array $BUILD_RESOLVERS_AFTER */
foreach ($BUILD_RESOLVERS_AFTER as $resolver) {
    if ($vehicle->resolve('php', ['source' => $sources['resolvers'] . 'resolve.' . $resolver . '.php'])) {
        $modx->log(modX::LOG_LEVEL_INFO, 'Added resolver "' . $resolver . '" to category.');
    } else {
        $modx->log(modX::LOG_LEVEL_INFO, 'Could not add resolver "' . $resolver . '" to category.');
    }
}

//
flush();
$builder->putVehicle($vehicle);

/** @var array $BUILD_CHUNKS */
// now pack in the license file, readme and setup options
$builder->setPackageAttributes([
    'changelog' => file_get_contents($sources['docs'] . 'changelog.txt'),
    'license' => file_get_contents($sources['docs'] . 'license.txt'),
    'readme' => file_get_contents($sources['docs'] . 'readme.txt'),
    'chunks' => $BUILD_CHUNKS,
    'setup-options' => [
        'source' => $sources['build'] . 'setup.options.php',
    ],
    /*
    'requires' => array(
        'pdotools' => '>=2.5.0-pl',
    ),
    */
]);
$modx->log(modX::LOG_LEVEL_INFO, 'Added package attributes and setup options.');

//
if (PKG_ENCRYPT) {
    $builder->putVehicle($builder->createVehicle([
        'source' => $sources['resolvers'] . 'resolve.encryption.php',
    ], ['vehicle_class' => 'xPDOScriptVehicle']));
}

// zip up package
$modx->log(modX::LOG_LEVEL_INFO, 'Packing up transport package zip...');
$builder->pack();

$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tend = $mtime;
$totalTime = ($tend - $tstart);
$totalTime = sprintf("%2.4f s", $totalTime);

$signature = $builder->getSignature();
if (defined('PKG_AUTO_INSTALL') && PKG_AUTO_INSTALL) {
    $sig = explode('-', $signature);
    $versionSignature = explode('.', $sig[1]);

    /** @var modTransportPackage $package */
    if (!$package = $modx->getObject('transport.modTransportPackage', [
        'signature' => $signature,
    ])) {
        $provider_id = 0;
        if ($packageWithProvider = $modx->getObject('transport.modTransportPackage', [
            'package_name' => PKG_NAME,
            'provider:!=' => 0,
        ])) {
            $provider_id = $packageWithProvider->get('provider');
        }
        unset($packageWithProvider);

        //
        $package = $modx->newObject('transport.modTransportPackage');
        $package->set('signature', $signature);
        $package->fromArray([
            'created' => date('Y-m-d h:i:s'),
            'updated' => null,
            'state' => 1,
            'workspace' => 1,
            'provider' => $provider_id,
            'source' => $signature . '.transport.zip',
            'package_name' => PKG_NAME,
            'version_major' => $versionSignature[0],
            'version_minor' => !empty($versionSignature[1]) ? $versionSignature[1] : 0,
            'version_patch' => !empty($versionSignature[2]) ? $versionSignature[2] : 0,
        ]);
        if (!empty($sig[2])) {
            $r = preg_split('/([0-9]+)/', $sig[2], -1, PREG_SPLIT_DELIM_CAPTURE);
            if (is_array($r) && !empty($r)) {
                $package->set('release', $r[0]);
                $package->set('release_index', (isset($r[1]) ? $r[1] : '0'));
            } else {
                $package->set('release', $sig[2]);
            }
        }
        $package->save();
    }

    if ($package->install()) {
        $modx->runProcessor('system/clearcache');
    }
}
if (!empty($_GET['download'])) {
    echo '<script>document.location.href = "/core/packages/' . $signature . '.transport.zip' . '";</script>';
}

$modx->log(modX::LOG_LEVEL_WARN, "Execution time: {$totalTime}");
if (!XPDO_CLI_MODE && @$_GET['html'] !== '0') {
    echo '</pre>';
}