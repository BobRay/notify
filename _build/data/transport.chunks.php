<?php

$chunks = array();

$chunks[0] = $modx->newObject('modChunk');
$chunks[0] ->fromArray(array(
    'id' => 0,
    'name' => 'NfTweetTplExisting',
    'description' => 'Notify Tweet Tpl for existing resource',
    'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/nftweettplexisting.chunk.html'),
    'properties' => '',
),'',true,true);

$chunks[1] = $modx->newObject('modChunk');
$chunks[1] ->fromArray(array(
    'id' => 1,
    'name' => 'NfTweetTplNew',
    'description' => 'Notify Tweet Tpl for new resource',
    'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/nftweettplnew.chunk.html'),
    'properties' => '',
),'',true,true);

$chunks[2] = $modx->newObject('modChunk');
$chunks[2] ->fromArray(array(
    'id' => 2,
    'name' => 'NfTweetTplCustom',
    'description' => 'Custom Notify Tweet Tpl',
    'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/nftweettplcustom.chunk.html'),
    'properties' => '',
),'',true,true);


$chunks[3] = $modx->newObject('modChunk');
$chunks[3] ->fromArray(array(
    'id' => 3,
    'name' => 'NfEmailSubjectTpl',
    'description' => 'Subject for Notify Email to subscribers',
    'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/nfemailsubjecttpl.chunk.html'),
    'properties' => '',
),'',true,true);

$chunks[4] = $modx->newObject('modChunk');
$chunks[4] ->fromArray(array(
    'id' => 4,
    'name' => 'NfSubscriberEmailTplExisting',
    'description' => 'HTML for Notify existing resource subscriber email',
    'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/nfsubscriberemailtplexisting.chunk.html'),
    'properties' => '',
),'',true,true);

$chunks[5] = $modx->newObject('modChunk');
$chunks[5] ->fromArray(array(
    'id' => 5,
    'name' => 'NfSubscriberEmailTplNew',
    'description' => 'HTML for Notify new resource subscriber email',
    'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/nfsubscriberemailtplnew.chunk.html'),
    'properties' => '',
),'',true,true);

$chunks[6] = $modx->newObject('modChunk');
$chunks[6] ->fromArray(array(
    'id' => 6,
    'name' => 'NfSubscriberEmailTplCustom',
    'description' => 'HTML for Notify custom subscriber email',
    'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/nfsubscriberemailtplcustom.chunk.html'),
    'properties' => '',
),'',true,true);



$chunks[7] = $modx->newObject('modChunk');
$chunks[7] ->fromArray(array(
    'id' => 7,
    'name' => 'NfSubscriberEmail-fancyTpl',
    'description' => 'HTML for Fancy Notify subscriber email',
    'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/nfsubscriberemail-fancytpl.chunk.html'),
    'properties' => '',
),'',true,true);

$chunks[8] = $modx->newObject('modChunk');
$chunks[8] ->fromArray(array(
    'id' => 8,
    'name' => 'NfNotifyFormTpl',
    'description' => 'Form Tpl for Notify Extra',
    'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/nfnotifyformtpl.chunk.html'),
    'properties' => '',
),'',true,true);

$chunks[9] = $modx->newObject('modChunk');
$chunks[9]->fromArray(array(
    'id' => 9,
    'name' => 'NfUnsubscribeTpl',
    'description' => 'Unsubscribe link chunk for Notify Extra',
    'snippet' => file_get_contents($sources['source_core'] . '/elements/chunks/nfunsubscribetpl.chunk.html'),
    'properties' => '',
), '', true, true);


return $chunks;