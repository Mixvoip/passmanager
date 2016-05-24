<?php

/**
 * Created by IntelliJ IDEA.
 * User: chris
 * Date: 20.04.16
 * Time: 13:26
 */
class Tag extends AppModel
{
    public $hasAndBelongsToMany = array(
        'User' => array(
            'className' => 'User',
            'joinTable' => 'user_has_tags',
            'foreignKey' => 'tag_id',
            'associationForeignKey' => 'user_id'
        )
    );

    /*public $hasMany = array(
        'Password' => array(
            'className' => 'Password',
            'foreignKey' => 'tag_id'
        )
    );*/

    /** Get the Tags for the User. If strict Tagging is disabled also get the Child tags
     * @param $userID int|string ID of the User to get the tags
     * @return array|null Array with the Tags of the User
     */
    public function getTagsForUser($userID)
    {
        if (Configure::read('StrictTagging.enable')) {
            return $this->getTagAssociatedUser($userID);
        } else {
            $allowedTags = $this->getTagIDsForAccess($userID);
            //Build the condition array for the DB
            $conditions = '';
            foreach ($allowedTags as $tagID) {
                $conditions .= 'Tag.id = ' . $tagID . ' OR ';
            }
            $conditions = substr($conditions, 0, -3);
            return $this->find('all', array(
                'conditions' => array($conditions)
            ));
        }
    }

//    /** Get the tag for a Password
//     * @param $passwordID int|string The ID of the password from which you want the info
//     * @return array|null Tag for the password
//     */
//    public function getTagForPassword($passwordID)
//    {
//        $tag = $this->find('first', array(
//            'fields' => array('Tag.id', 'Tag.name', 'Tag.parenttag_id'),
//            'conditions' => array(
//                array('Password.id' => $passwordID)
//            ),
//            'joins' => array(
//                array(
//                    'table' => 'passwords',
//                    'alias' => 'Password',
//                    'type' => 'INNER',
//                    'conditions' => array(
//                        'Password.tag_id = Tag.id'
//                    )
//                )
//            )
//        ));
//        return $tag;
//    }

    /**Create new Tag
     * @param $parentID int|null The id of the parent tag if null then this is a new root tag
     * @param $name string Name of the Tag
     * @throws Exception
     */
    public function createTag($parentID, $name)
    {
        $newTag = array(
            'id' => null,
            'name' => $name,
            'parenttag_id' => $parentID
        );
        $this->create();
        $this->save($newTag);
        $this->clear();
        parent::setEditFlagTags();
        parent::setEditFlag();
    }

    /**Updates a Tag
     * @param $tagID int|string ID Of the tag to be updated
     * @param $tagName string New tag name
     * @param $parentTag int|string ID of the Parent
     * @throws Exception
     */
    public function editTag($tagID, $tagName, $parentTag)
    {
        if ($tagID == $parentTag) $parentTag = Configure::read('RootTag');
        $updateTag = array(
            'name' => "'" . $tagName . "'",
            'parenttag_id' => $parentTag
        );
        //Update DB
        $this->updateAll($updateTag, array(
            'Tag.id' => $tagID
        ));
        $this->clear();
        parent::setEditFlag();
        parent::setEditFlagTags();
    }

    /**Delete a Tag and replaces it with $replaceTagID in User and Password
     * @param $tagID int|string Tag to be removed
     * @param $replaceTagID int|string ID of the replace tag
     */
    function deleteTag($tagID, $replaceTagID)
    {
        //Replace Tag's
        App::import('Model', 'UserHasTag');
        $UserHasTag = new UserHasTag();
        if (!isset($replaceTagID)) $replaceTagID = Configure::read('RootTag'); //Set replace tag to RootTag
        $UserHasTag->updateAll(array(
            'UserHasTag.tag_id' => $replaceTagID
        ), array(
            'UserHasTag.tag_id' => $tagID
        ));

        //Delete the Tag
        $this->delete($tagID, false);
        parent::setEditFlag();
        parent::setEditFlagTags();
    }

    /**Inject the Tag Information's into the Password for the Display
     * @param $folderData array Data of the folder fetched from the db
     * @param $userID int|string ID of the user requesting the data
     * @return array Data to be displayed, and fav data
     */
    public function injectTagInformation(&$folderData, $userID)
    {
        $this->recursive = -1;
        //Get the master PasswordHasTag array
        $passwordHasTagMaster = $this->getPasswordHasTagsMasterArray();

        //Get the master array for the favorites
        App::import('Model', 'Favorite');
        $Favorite = new Favorite();
        $favoritesMasterArray = $Favorite->getMasterArrayForUser($userID);
        $favData = array();
        
        //Get allowed Tags for user
        $allowedTags = array_unique($this->getTagIDsForAccess($userID));
        //Build the condition array for the DB
        $conditions = '';
        foreach ($allowedTags as $tagID) {
            $conditions .= 'Tag.id = ' . $tagID . ' OR ';
        }
        $conditions = substr($conditions, 0, -3);
        $tags = $this->getTagSimple(array($conditions));

        foreach ($folderData as &$folder) {
            foreach ($folder['Password'] as $index => &$password) {
                $pwTagIDs = $this->getTagIDsForPassword($password['id'], $passwordHasTagMaster);
                $tagName = '[';
                $keepPassword = false;
                foreach ($pwTagIDs as $pwTagID) {
                    if (in_array($pwTagID, $allowedTags)) {
                        $keepPassword = $keepPassword || true;
                        $tagName .= $tags[$pwTagID]['name'] . ' ; ';
                    } else {
                        $keepPassword = $keepPassword || false;
                    }
                }
                $password['tag_name'] = substr($tagName, 0, -3) . ']';
                if ($Favorite->isFavorite($password['id'], $userID, $favoritesMasterArray)) {
                    $password['favorite'] = 1;
                    $password['folder_name'] = $folder['Folder']['name'];
                    $favData[] = $password;
                }
                if (!$keepPassword) unset($folder['Password'][$index]);
            }
        }
        return array('all' => $folderData, 'fav' => $favData);
    }


    public function getTagIDsForPassword($passwordID, $passwordHasTagMaster = null)
    {
        if (!isset($passwordHasTagMaster)) {
            App::import('Model', 'PasswordHasTag');
            $PasswordHasTag = new PasswordHasTag();
            $tags = $PasswordHasTag->find('all', array(
                'conditions' => array('PasswordHasTag.password_id' => $passwordID)
            ));
            return $this->extractField($tags, 'PasswordHasTag', 'tag_id', true);
        } else {
            $tmp = $this->extractField($passwordHasTagMaster, 'PasswordHasTag', 'tag_id', true, true, 'password_id');
            foreach ($tmp as $pwid => $val) {
                if ($pwid != $passwordID) {
                    unset($tmp[$pwid]);
                }
            }
            return $tmp;
        }
    }

    /**Get a array with the ID's of the Tags which the user have access
     * @param $userID int|string Id of the User
     * @return array Array with the ID of the Tags allowed
     */
    public function getTagIDsForAccess($userID)
    {
        $allowedTags = array();
        $tags = $this->getTagAssociatedUser($userID);
        if (Configure::read('StrictTagging.enable')) {
            foreach ($tags as $tag) {
                $allowedTags[] = (int)$tag['Tag']['id'];
            }
        } else {
            $masterArray = $this->getTagSimple(array());
            foreach ($tags as $tag) {
                $allowedTags[] = (int)$tag['Tag']['id'];
                $allowedTags = array_merge($allowedTags, $this->getChildTags((int)$tag['Tag']['id'], $masterArray));
            }
        }
        return $allowedTags;
    }

    /**Get all the Tag's directly subscribed by the user
     * @param $userID int|string UserID
     * @return array|null all the tags
     */
    private function getTagAssociatedUser($userID)
    {
        $tags = $this->find('all', array(
            'fields' => array('Tag.id', 'Tag.name', 'Tag.parenttag_id'),
            'conditions' => array(
                array('UserHasTag.user_id' => $userID)
            ),
            'joins' => array(
                array(
                    'table' => 'user_has_tags',
                    'alias' => 'UserHasTag',
                    'type' => 'INNER',
                    'conditions' => array(
                        'UserHasTag.tag_id = Tag.id'
                    )
                )
            )
        ));
        return $tags;
    }

    /**Get all the Parent id's for a tag
     * @param $tagID int|string Start id the tag
     * @param $result array Result of the previous call. (If you call it use array() or set nothing)
     * @return array Array with all the Parent Tag Id's
     */
    private function getParentTags($tagID, $result = array())
    {
        if (isset($tagID)) {
            $tag = $this->find('first', array(
                'conditions' => array('Tag.id' => $tagID)
            ));
            $result[] = (int)$tagID;
            return $this->getParentTags($tag['Tag']['parenttag_id'], $result);
        } else {
            //We are at the Top
            return $result;
        }

    }

    /**Get all the Parent id's for a tag
     * @param $tagID int|string Start id the tag
     * @param $masterArray array containing all the Tags
     * @param $result array Result of the previous call. (If you call it use array() or set nothing)
     * @return array Array with all the Parent Tag Id's
     */
    public function getChildTags($tagID, $masterArray, $result = array())
    {
        $tagIDs = array_keys(array_column($masterArray, 'parenttag_id', 'id'), $tagID);
        foreach ($tagIDs as $tagID) {
            $result[] = $tagID;
            $result = $this->getChildTags($tagID, $masterArray, $result);
        }
        return $result;
    }

    /**Extract the Tag in a assoc array [tag_id]=>array('id'=>[tag_id],
     *                                                  'name'=>[tag_name],
     *                                                  'parenttag_id'=>[id of the parent])
     * @param $conditions array Conditions to query the database
     * @return array Extracted tags
     */
    public function getTagSimple($conditions)
    {
        $tags = $this->find('all', array(
            'conditions' => array($conditions)
        ));
        return $this->simplifyTags($tags);
    }

    /**Simplify the tags from a db request
     * @param $TagDBArray array result from db
     * @return array Extracted tags
     */
    private function simplifyTags($TagDBArray)
    {
        $res = array();
        foreach ($TagDBArray as $tag) {
            $res[$tag['Tag']['id']] = $tag['Tag'];
        }
        return $res;
    }

    /**
     * Get all the Passwords has Tag in array using the cache
     */
    private function getPasswordHasTagsMasterArray()
    {
        $result = Cache::read('newest_passwordHasTags', 'short');
        $edited = Cache::read('edited_tag', 'short');
        if (!$result || (isset($edited) && $edited)) {
            App::import('Model', 'PasswordHasTag');
            $PasswordHasTag = new PasswordHasTag();
            $result = $PasswordHasTag->find('all');
            Cache::write('newest_passwordHasTags', $result, 'short');
            parent::clearEditFlagTags();
        }
        return $result;
    }

    /**Get the numbers of passwords for all the tags
     * @return array Array in format array(<tag_id>=><password_count>,...)
     */
    public function getPasswordTagCountArray()
    {
        App::import('Model', 'PasswordHasTag');
        $PasswordHasTag = new PasswordHasTag();
        $dbArray = $PasswordHasTag->find('all', array(
            'fields' => array('PasswordHasTag.tag_id ', 'COUNT(PasswordHasTag.id) as pws'),
            'group' => array('PasswordHasTag.tag_id')
        ));

        //Rebuild array in format <tag_id>=><password_count>
        $res = array();
        foreach ($dbArray as $entry) {
            $res[$entry['PasswordHasTag']['tag_id']] = $entry[0]['pws'];
        }
        return $res;
    }
}