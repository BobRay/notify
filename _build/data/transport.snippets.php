<?php
/**
 * Notify transport snippets
 * Copyright 2011 Your Name <you@yourdomain.com>
 * @author Your Name <you@yourdomain.com>
 * 1/1/11
 *
 * Notify is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * Notify is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Notify; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package notify
 */
/**
 * Description:  Array of snippet objects for Notify package
 * @package notify
 * @subpackage build
 */

/* @var $modx modX */

if (! function_exists('getSnippetContent')) {
    function getSnippetContent($filename) {
        $o = file_get_contents($filename);
        $o = str_replace('<?php','',$o);
        $o = str_replace('?>','',$o);
        $o = trim($o);
        return $o;
    }
}
$snippets = array();

$snippets[1]= $modx->newObject('modSnippet');
$snippets[1]->fromArray(array(
    'id' => 1,
    'name' => 'Notify',
    'description' => 'Send site updates to Subscribers and Twitter',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/notify.snippet.php'),
),'',true,true);

$properties = include $sources['data'].'properties/properties.notifyproperties.php';
$snippets[1]->setProperties($properties);
unset($properties);

$snippets[2]= $modx->newObject('modSnippet');
$snippets[2]->fromArray(array(
    'id' => 2,
    'name' => 'RemoveNotify',
    'description' => 'Removes all parts of Notify package except the files in core/components/notify/  and this snippet',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/removenotify.snippet.php'),
),'',true,true);

return $snippets;