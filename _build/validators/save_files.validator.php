<?php
/**
 * Validator for Notify extra
 *
 * Copyright 2013-2015 by Bob Ray <http://bobsguides.com>
 * Created on 11-30-2014
 *
 * Notify is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * Notify is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Notify; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 * @package notify
 * @subpackage build
 */

/* @var $object xPDOObject */
/* @var $modx modX */
/* @var array $options */

if ($object->xpdo) {
    $modx =& $object->xpdo;
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
            /* return false if conditions are not met */

            /* [[+code]] */
            break;
        case xPDOTransport::ACTION_UPGRADE:
            /* copy existing Tpl chunks to 'My' . Chunkname
               if it doesn't exist already */
            $chunks = array(
                'NfTweetTplExisting' ,
                'NfTweetTplNew',
                'NfTweetTplCustom',
                'NfEmailSubjectTpl',
                'NfSubscriberEmailTplExisting',
                'NfSubscriberEmailTplNew',
                'NfSubscriberEmailTplCustom',
                'NfNotifyFormTpl',
                'NfUnsubscribeTpl',
            );

            foreach($chunks as $chunkName) {
                $count = $modx->getCount('modChunk', array('name' => 'My' . $chunkName));
                if ( ((int) $count) === 0) {
                    /* 'My' chunk doesn't exist - create it */
                    $oldChunk = $modx->getObject('modChunk', array('name' => $chunkName));
                    if ($oldChunk) {
                        $modx->log(modX::LOG_LEVEL_INFO,'Copying ' . $chunkName . ' to ' . 'My' . $chunkName );
                        $newChunk = $modx->newObject('modChunk');
                        $fields = $oldChunk->toArray();
                        $fields['name'] = 'My'. $fields['name'];
                        unset($fields['id']);
                        $newChunk->fromArray($fields);
                        $newChunk->save();
                    }
                }
            }


            break;

        case xPDOTransport::ACTION_UNINSTALL:
            break;
    }
}

return true;