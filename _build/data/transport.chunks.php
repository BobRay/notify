<?php

$chunks = array();

$chunks[1] = $modx->newObject('modChunk');
$chunks[1] ->fromArray(array(
    'id' => 1,
    'name' => 'NfTweetTpl',
    'description' => 'Text for Notify Tweet',
    'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/nftweettpl.chunk.html'),
    'properties' => '',
),'',true,true);
$chunks[2] = $modx->newObject('modChunk');
$chunks[2] ->fromArray(array(
    'id' => 2,
    'name' => 'NfEmailSubjectTpl',
    'description' => 'Subject for Notify Email to subscribers',
    'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/nfemailsubjecttpl.chunk.html'),
    'properties' => '',
),'',true,true);
$chunks[3] = $modx->newObject('modChunk');
$chunks[3] ->fromArray(array(
    'id' => 3,
    'name' => 'NfSubscriberEmailTpl',
    'description' => 'HTML for Notify subscriber email',
    'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/nfsubscriberemailtpl.chunk.html'),
    'properties' => '',
),'',true,true);

$chunks[4] = $modx->newObject('modChunk');
$chunks[4] ->fromArray(array(
    'id' => 4,
    'name' => 'NfSubscriberEmail-fancyTpl',
    'description' => 'HTML for Fancy Notify subscriber email',
    'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/nfsubscriberemail-fancytpl.chunk.html'),
    'properties' => '',
),'',true,true);

$chunks[5] = $modx->newObject('modChunk');
$chunks[5] ->fromArray(array(
    'id' => 5,
    'name' => 'NfNotifyFormTpl',
    'description' => 'Form Tpl for Notify Extra',
    'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/nfnotifyformtpl.chunk.html'),
    'properties' => '',
),'',true,true);

return $chunks;