<?php
/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {

    /**
     * Get the actual date for the database
     * @return string Actual date for the DB
     */
    protected function getActualDateForDB()
    {
        return date(DATE_ISO8601);
    }

    /**
     * Get a new CryptoWrapper
     * @return CryptoWrapper The new CryptoWrapper
     */
    protected function getCryptoWrapper()
    {
        App::uses('CryptoWrapper', 'Vendor');
        return new CryptoWrapper();
    }

    /**Extracts the $fieldName from $entryName contained in a db array (from find all)
     * @param $dbArray array Array from db
     * @param $entryName string name of the Entry
     * @param $fieldName string name of the field to be extracted
     * @param bool $castToInt cast extracted field to int
     * @param bool $assoc Should it be a assoc array
     * @param null|string $idField name of the key field for the assoc array
     * @return array Array with extracted values
     */
    protected function extractField($dbArray, $entryName, $fieldName, $castToInt = true, $assoc = false, $idField = null)
    {
        $assoc = $assoc && isset($idField);
        $res = array();
        foreach ($dbArray as $entry) {
            if ($castToInt) {
                if ($assoc) {
                    $assocID = $entry[$entryName][$idField];
                    $res[$assocID] = (int)$entry[$entryName][$fieldName];
                } else {
                    $res[] = (int)$entry[$entryName][$fieldName];
                }
            } else {
                if ($assoc) {
                    $assocID = $entry[$entryName][$idField];
                    $res[$assocID] = $entry[$entryName][$fieldName];
                } else {
                    $res[] = $entry[$entryName][$fieldName];
                }
            }
        }
        return $res;
    }

    public function setEditFlag()
    {
        Cache::write('edited', true, 'short');
    }

    public function setEditFlagTags()
    {
        Cache::write('edited_tag', true, 'short');
    }

    public function clearEditFlag()
    {
        Cache::delete('edited', 'short');
    }

    public function clearEditFlagTags()
    {
        Cache::delete('edited_tag', 'short');
    }
}
