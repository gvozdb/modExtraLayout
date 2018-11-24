<?php
/** @var modX $modx */
/** @var array $sources */
$chunks = array();
$tmp = array(
    'tpl.modExtraLayout.row' => array(
        'file' => 'row',
        'description' => '',
    ),
);

// Save chunks for setup options
$BUILD_CHUNKS = array();

foreach ($tmp as $k => $v) {
    $static_file = 'core/components/' . PKG_NAME_LOWER . '/elements/chunks/' . $v['file'] . '.tpl';
    if (PKG_DEV_MODE) {
        $static_file = PKG_NAME . '/' . $static_file;
    }

    /** @var modChunk $chunk */
    $chunk = $modx->newObject('modChunk');
    $chunk->fromArray(array(
        'id' => 0,
        'name' => $k,
        'description' => @$v['description'],
        'snippet' => file_get_contents($sources['source_core'] . '/elements/chunks/' . $v['file'] . '.tpl'),
        'source' => 1,
        'static' => (PKG_DEV_MODE || BUILD_CHUNK_STATIC),
        'static_file' => $static_file,
    ), '', true, true);
    $BUILD_CHUNKS[$k] = file_get_contents($sources['source_core'] . '/elements/chunks/' . $v['file'] . '.tpl');
    unset($static_file);

    $chunks[] = $chunk;
}
unset($tmp);

return $chunks;