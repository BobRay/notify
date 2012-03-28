<?php
/**
 * Action file for the components menu item of the Notify package
 * @author Bob Ray
 * 2/4/11
 *
 * @package notify
 */

/* This is an example of an action index file. This file executes
 * When you select Notify on the Components menu.
 * This example displays a simple MODx cheat sheet in the
 * manager. In order to reach page 2 (or return to page 1), you'll
 * have to edit the action ID in the URL of the two chunks.
 */
$path = $modx->getOption('assets_url');
$modx->regClientCSS($path . 'components/notify/css/notify.css');
$output = '<div class="notify">';
$output .= '<h2>MODx Cheat Sheet</h2>';
/* get page to show from URL */
switch($_GET['page']) {
    case '2':
        $output .= $modx->getChunk( 'MyChunk2');
        break;
    default: /* default to page 1 */
        $output .= $modx->getChunk( 'MyChunk1');
}
$output .= '</div>';
return $output;
?>
