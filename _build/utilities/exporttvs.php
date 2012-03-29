<?php
/**
 * ExportTvs
 *
 * Copyright 2011 Bob Ray
 *
 * @author Bob Ray
 * 3/27/12
 *
 * ExportTvs is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * ExportTvs is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * ExportTvs; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package exporttvs
 */
/**
 * MODx ExportTvs Snippet
 *
 * Description Extracts TVs from MODX install to build files
 *
 * @package exporttvs
 *
 */
/* @var $category string */

/* Usage
 *
 * Create a snippet called ExportTvs, paste the code or
 * use this for the snippet code:
 *     return include 'path/to/this/file';
 *
 * Put a tag for the snippet on a page and preview the page
 *
 * Minimal snippet call [[!ExportTvs? &category=`MyCategory`]]
 *
 * Optional properties:
 *

 * &transportPath (directory for transport.tvs.php file)
 * defaults to assets/mycomponents/{categoryLower}/_build/data/
 *
 * &debug (if &debug=`1`, displays debug info - no files are written)
*/

$props =& $scriptProperties;
/* @var $modx modX */
$debug = $modx->getOption('debug', $props, null);

$category = $modx->getOption('category', $props, null);
/* @var $categoryObj modCategory */
$categoryObj = $modx->getObject('modCategory', array('category' => $category));
if (!$categoryObj) {
    return 'Could not find category: ' . $category;
}
$categoryId = $categoryObj->get('id');
$categoryLower = strtolower($category);
if ($debug) {
    echo '<br />Category: ' . $category;
}
$c = $modx->newQuery('modTemplateVar');

$c->where(array('category'=>$categoryId));
    $c->sortby('rank', 'ASC');

$tvs = $modx->getCollection('modTemplateVar', $c);
if (empty($tvs)) {
    return 'No TVs found in category: ' . $category;
}

$transportPath = $modx->getOption('transportPath', $props, null);
$transportPath = empty ($transportPath) ? $modx->getOption('assets_path') . 'mycomponents/' . $categoryLower . '/_build/data/' : $transportPath;

if (!is_dir($transportPath)) {
    return 'Bad transportPath: ' . $transportPath;
}

$transportFile = $transportPath . 'transport.tvs.php';
$transportFp = fopen($transportFile, 'w');
if (!$transportFp) {
    return 'Could not open transport file: ' . $transportFile;
}
if ($debug) {
    echo '<br />TransportFile: ' . $transportFile;
}
echo '<br /><br />Processing<br />';

$i = 1;
fwrite($transportFp, "<?php\n\n");
fwrite($transportFp, "\$templateVariables = array();\n\n");
foreach ($tvs as $tv) {
    /* @var $tv modTemplateVar */

    echo '<br /><br />TV: ' . $tv->get('name');

    if (! $debug) {
        fwrite($transportFp, '$templateVariables[' . $i . '] = $modx->newObject(' . "'modTemplateVar');" . "\n");
        fwrite($transportFp, '$templateVariables[' . $i . '] ->fromArray(array(' . "\n");
        fwrite($transportFp, "    'id' => " . $i . ",\n");
        fwrite($transportFp, "    'type' => '" . $tv->get('type') . "',\n");
        fwrite($transportFp, "    'name' => '" . $tv->get('name') . "',\n");
        fwrite($transportFp, "    'caption' => '" . $tv->get('caption') . "',\n");
        fwrite($transportFp, "    'description' => '" . $tv->get('description') . "',\n");
        fwrite($transportFp, "    'elements' => '" . $tv->get('elements') . "',\n");
        fwrite($transportFp, "    'rank' => '" . $tv->get('rank') . "',\n");
        fwrite($transportFp, "    'display' => '" . $tv->get('display') . "',\n");
        fwrite($transportFp, "    'default_text' => '" . $tv->get('default_text') . "',\n");

        $properties = $tv->get('properties');
        $properties = empty($properties) ? '' : $modx->toJSON($properties);
        fwrite($transportFp, "    'properties' => '" . $properties . "',\n");

        $properties = $tv->get('input_properties');
        $properties = empty($properties) ? '' : $modx->toJSON($properties);
        fwrite($transportFp, "    'input_properties' => '" . $properties . "',\n");

        $properties = $tv->get('output_properties');
        $properties = empty($properties) ? '' : $modx->toJSON($properties);
        fwrite($transportFp, "    'output_properties' => '" . $properties . "',\n");


        fwrite($transportFp, "),'',true,true);\n");
    }


    $i++;
}


if (! $debug) {
    fwrite($transportFp, 'return $templateVariables;');
    fclose($transportFp);
}return '<br /><br />Finished';
