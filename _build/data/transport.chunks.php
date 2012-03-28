<?php

$chunks = array();

$chunks[1] = $modx->newObject('modChunk');
$chunks[1] ->fromArray(array(
    'id' => 1,
    'name' => 'NfTweet',
    'description' => 'Text for Notify Tweet',
    'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/nftweet.chunk.html'),
    'properties' => '',
),'',true,true);
$chunks[2] = $modx->newObject('modChunk');
$chunks[2] ->fromArray(array(
    'id' => 2,
    'name' => 'NfEmailSubject',
    'description' => 'Subject for Notify Email to subscribers',
    'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/nfemailsubject.chunk.html'),
    'properties' => '',
),'',true,true);
$chunks[3] = $modx->newObject('modChunk');
$chunks[3] ->fromArray(array(
    'id' => 3,
    'name' => 'NfSubscriberEmail',
    'description' => 'HTML for Notify subscriber email',
    'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/nfsubscriberemail.chunk.html'),
    'properties' => '',
),'',true,true);
return $chunks;