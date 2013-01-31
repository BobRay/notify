<?php
/**
 * Notify transport plugins
 * Copyright 2012-2013 Bob Ray <http://bobsguides/com>
 * @author Bob Ray <http://bobsguides/com>
 * 3/27/12
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
 * Description:  Array of plugin objects for Notify package
 * @package notify
 * @subpackage build
 */
 /* @var $modx modX */
if (! function_exists('getPluginContent')) {
    function getpluginContent($filename) {
        $o = file_get_contents($filename);
        $o = str_replace('<?php','',$o);
        $o = str_replace('?>','',$o);
        $o = trim($o);
        return $o;
    }
}
$plugins = array();

$plugins[0]= $modx->newObject('modPlugin');

$plugins[0]->set('id',1);
$plugins[0]->set('name', 'Notify');
$plugins[0]->set('description', 'Plugin for Notify extra.');
    $plugins[0]->set('plugincode', getPluginContent($sources['source_core'].'/elements/plugins/notify.plugin.php'));
$plugins[0]->set('category',0);

$events = include $sources['events'].'events.notify.php';
if (is_array($events) && !empty($events)) {
    $plugins[0]->addMany($events);
    $modx->log(xPDO::LOG_LEVEL_INFO,'Packaged in '.count($events).' Plugin Events for Notify Plugin.'); flush();
} else {
    $modx->log(xPDO::LOG_LEVEL_ERROR,'Could not find plugin events for Notify Plugin!');
}
unset($events);

return $plugins;