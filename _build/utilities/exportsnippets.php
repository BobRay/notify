<?php
/**
 * ExportChunks
 *
 * Copyright 2011 Bob Ray
 *
 * @author Bob Ray
 * 3/27/12
 *
 * ExportChunks is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * ExportChunks is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * ExportSnippets; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package exportsnippets
 */
/**
 * MODx ExportSnippets Snippet
 *
 * Description Extracts snippets from MODX install to build files
 *
 * @package exportsnippets
 *
 */
/* @var $category string */

/* Usage
 *
 * Create a snippet called ExportCategorys, paste the code or
 * use this for the snippet code:
 *     return include 'path/to/this/file';
 *
 * Put a tag for the snippet on a page and preview the page
 *
 * Minimal snippet call [[!ExportCategory? &category=`MyCategory`]]
 *
 * Optional properties:
 *
 * &snippetPath  (directory for snippet code files)
 * defaults to assets/mycomponents/{categoryLower}/core/components/{categoryLower}/elements/snippets/
 *
 * &transportPath (directory for transport.snippets.php file)
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
$snippets = $modx->getCollection('modSnippet', array('category' => $categoryId));
if (empty($snippets)) {
    return 'No Snippets found in category: ' . $category;
}
$snippetPath = $modx->getOption('snippetPath', $props, null);
$snippetPath = empty ($snippetPath) ? $modx->getOption('assets_path') . 'mycomponents/' . $categoryLower . '/core/components/' . $categoryLower . '/elements/snippets/' : $snippetPath;

$transportPath = $modx->getOption('transportPath', $props, null);
$transportPath = empty ($transportPath) ? $modx->getOption('assets_path') . 'mycomponents/' . $categoryLower . '/_build/data/' : $transportPath;

if (!is_dir($transportPath)) {
    return 'Bad transportPath: ' . $transportPath;
}

$transportFile = $transportPath . 'transport.snippets.php';
$transportFp = fopen($transportFile, 'w');
if (!$transportFp) {
    return 'Could not open transport file: ' . $transportFile;
}
if ($debug) {
    echo '<br />TransportFile: ' . $transportFile;
}
echo '<br /><br />Processing<br />';

$i = 1;

if (!$debug) {
    fwrite($transportFp, "<?php\n\n");
    fwrite($transportFp, "\$snippets = array();\n\n");
}
foreach ($snippets as $snippet) {
    /* @var $snippet modSnippet */
    $content = $snippet->getContent();
    $content = str_replace('<?php','',$content);
            $content = str_replace('?>','',$content);
            $content = trim($content);
    $fileName = $snippetPath . strtolower($snippet->get('name')) . '.snippet.php';
    echo '<br /><br />Snippet: ' . $snippet->get('name');
    if ($debug) {
        echo '<br />Snippet File: ' . $fileName;
    }

    if (!$debug) {
        $fp = fopen($fileName, 'w');
        if (!$fp) {
            return 'Could not open snippet file: ' . $fileName;
        }
        echo '<br />SnippetFile: ' . $fileName;
        fwrite($fp, $content);
        fclose($fp);


        fwrite($transportFp, '$snippets[' . $i . '] = $modx->newObject(' . "'modSnippet');" . "\n");
        fwrite($transportFp, '$snippets[' . $i . '] ->fromArray(array(' . "\n");
        fwrite($transportFp, "    'id' => " . $i . ",\n");
        fwrite($transportFp, "    'name' => '" . $snippet->get('name') . "',\n");
        fwrite($transportFp, "    'description' => \"" . $snippet->get('description') . "\",\n");
        fwrite($transportFp, "    'snippet' => file_get_contents(\$sources['source_core']." . "'/elements/snippets/" . strtolower($snippet->get('name')) . ".snippet.php'),\n");
        $properties = $snippet->get('properties');
        $properties = empty($properties) ? '' : $modx->toJSON($properties);
        fwrite($transportFp, "    'properties' => '" . $properties . "',\n");
        fwrite($transportFp, "),'',true,true);\n");
    }

    $i++;
}
if (!$debug) {
    fwrite($transportFp, 'return $snippets;');
    fclose($transportFp);
}
return '<br /><br />Finished';
