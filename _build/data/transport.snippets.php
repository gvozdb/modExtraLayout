<?php
/** @var modX $modx */
/** @var array $sources */
$snippets = array();
$tmp = array(
    'modExtraLayout' => array(
        'file' => 'modextralayout',
        'description' => '',
    ),
);

foreach ($tmp as $k => $v) {
    $static_file = 'core/components/' . PKG_NAME_LOWER . '/elements/snippets/' . $v['file'] . '.php';
    if (PKG_DEV_MODE) {
        $static_file = PKG_NAME . '/' . $static_file;
    }

    /** @var modSnippet $snippet */
    $snippet = $modx->newObject('modSnippet');
    $snippet->fromArray(array(
        'id' => 0,
        'name' => $k,
        'description' => @$v['description'],
        'snippet' => getSnippetContent($sources['source_core'] . '/elements/snippets/' . $v['file'] . '.php'),
        'source' => 1,
        'static' => (PKG_DEV_MODE || BUILD_SNIPPET_STATIC),
        'static_file' => $static_file,
    ), '', true, true);
    /** @noinspection PhpIncludeInspection */
    $properties = include $sources['build'] . 'properties/' . $v['file'] . '.php';
    $snippet->setProperties($properties);
    unset($static_file);

    $snippets[] = $snippet;
}
unset($tmp, $properties);

return $snippets;