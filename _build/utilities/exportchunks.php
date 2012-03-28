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
 * ExportChunks; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package exportchunks
 */
/**
 * MODx ExportChunks Snippet
 *
 * Description Extracts chunks from MODX install to build files
  *
 * @package exportchunks
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
  * &chunkPath  (directory for chunk code files)
  * defaults to assets/mycomponents/{categoryLower}/core/components/{categoryLower}/elements/chunks/
  *
  * &transportPath (directory for transport.chunks.php file)
  * defaults to assets/mycomponents/{categoryLower}/_build/data/transport.chunks.php
  *
  * &debug (if &debug=`1`, displays debug info - no files are written)
 */

$props =& $scriptProperties;
    /* @var $modx modX */
    $debug = $modx->getOption('debug', $props, null);

    $category = $modx->getOption('category', $props, null);
    /* @var $categoryObj modCategory */
    $categoryObj = $modx->getObject('modCategory', array('category' => $category));
    if (! $categoryObj) {
       return 'Could not find category: ' . $category;
    }
    $categoryId = $categoryObj->get('id');
    $categoryLower = strtolower($category);
    if ($debug) {
        echo '<br />Category: ' . $category;
    }
    $chunks = $modx->getCollection('modChunk', array('category' => $categoryId));
    if (empty($chunks)) {
       return 'No Chunks found in category: ' . $category;
    }
    $chunkPath = $modx->getOption('chunkPath', $props, null);
    $chunkPath = empty ($chunkPath)? $modx->getOption('assets_path') . 'mycomponents/' . $categoryLower . '/core/components/' . $categoryLower . '/elements/chunks/'  : $chunkPath;

    $transportPath = $modx->getOption('transportPath', $props, null);
    $transportPath = empty ($transportPath)? $modx->getOption('assets_path') . 'mycomponents/' . $categoryLower . '/_build/data/' : $transportPath;

    if (! is_dir($transportPath)) {
       return 'Bad transportPath: ' . $transportPath;
    }

    $transportFile = $transportPath . 'transport.chunks.php';
    $transportFp = fopen($transportFile, 'w');
    if (! $transportFp) {
       return 'Could not open transport file: ' . $transportFile;
    }
    if ($debug) {
        echo '<br />TransportFile: ' . $transportFile;
    }
    echo '<br /><br />Processing<br />';

    $i = 1;
    fwrite($transportFp, "<?php\n\n");
    fwrite($transportFp, "\$chunks = array();\n\n");
    foreach ($chunks as $chunk) {
       /* @var $chunk modChunk */
        $content = $chunk->getContent();
        $fileName = $chunkPath . strtolower($chunk->get('name')) . '.chunk.html';
        echo '<br /><br />Chunk: ' . $chunk->get('name');
        if ($debug) {
            echo '<br />Chunk File: ' . $fileName;
        }

        if (! $debug) {
            $fp = fopen($fileName, 'w');
            if (!$fp) {
                return 'Could not open chunk file: ' . $fileName;
            }
            echo '<br />ChunkFile: ' . $fileName;
            fwrite($fp, $content);
            fclose($fp);
        }

       fwrite($transportFp, '$chunks[' . $i . '] = $modx->newObject(' . "'modChunk');" . "\n");
        fwrite($transportFp,'$chunks[' . $i . '] ->fromArray(array(' . "\n" );
        fwrite($transportFp,"    'id' => " . $i . ",\n");
        fwrite($transportFp, "    'name' => '" . $chunk->get('name') . "',\n");
        fwrite($transportFp, "    'description' => '" . $chunk->get('description') . "',\n");
        fwrite($transportFp, "    'snippet' => file_get_contents(\$sources['source_core']." . "'/elements/chunks/" . strtolower($chunk->get('name')) . ".chunk.html'),\n");

        /* ToDo: write properties array */
        fwrite($transportFp, "    'properties' => '',\n");
        fwrite($transportFp, "),'',true,true);\n");


        $i++;
    }
   fwrite($transportFp, 'return $chunks;');
   fclose($transportFp);
   return '<br /><br />Finished';
/*
$chunks = array();

$chunks[1]= $modx->newObject('modChunk');
$chunks[1]->fromArray(array(
    'id' => 1,
    'name' => 'MyChunk1',
    'description' => 'MyChunk1 for Notify',
    'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/mychunk1.chunk.tpl'),
    'properties' => '',
),'',true,true);*/