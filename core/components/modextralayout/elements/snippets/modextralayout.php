<?php
/** @var modX $modx */
/** @var modExtraLayout $mel */
/** @var array $scriptProperties */
$sp = &$scriptProperties;
if (!$mel = $modx->getService('modextralayout', 'modExtraLayout',
    $modx->getOption('mel_core_path', null, MODX_CORE_PATH . 'components/modextralayout/') . 'model/modextralayout/', $sp)
) {
    return 'Could not load modExtraLayout class!';
}
$mel->initialize($modx->context->key);

// Do your snippet code here. This demo grabs 5 items from our custom table.
$tpl = $modx->getOption('tpl', $sp, 'Item');
$sortby = $modx->getOption('sortby', $sp, 'name');
$sortdir = $modx->getOption('sortbir', $sp, 'ASC');
$limit = $modx->getOption('limit', $sp, 5);
$outputSeparator = $modx->getOption('outputSeparator', $sp, "\n");
$toPlaceholder = $modx->getOption('toPlaceholder', $sp, false);

// Build query
$c = $modx->newQuery('melObject');
$c->sortby($sortby, $sortdir);
$c->limit($limit);
$items = $modx->getIterator('melObject', $c);

// Iterate through items
$list = [];
/** @var melObject $item */
foreach ($items as $item) {
    $list[] = $mel->tools->getChunk($tpl, $item->toArray());
}

// Output
$output = implode($outputSeparator, $list);
if (!empty($toPlaceholder)) {
    // If using a placeholder, output nothing and set output to specified placeholder
    $modx->setPlaceholder($toPlaceholder, $output);

    return '';
}
// By default just return output
return $output;