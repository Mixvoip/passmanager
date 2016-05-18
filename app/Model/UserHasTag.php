<?php

/**
 * Created by IntelliJ IDEA.
 * User: chris
 * Date: 20.04.16
 * Time: 13:26
 */
class UserHasTag extends AppModel
{

    /**Update the links between the user an the tags
     * @param $userID int|string The id of the user
     * @param $tags array Array with the new tags
     * @throws Exception
     */
    public function updateTags($userID, $tags)
    {
        //hack Review code to be more efficient
        //remove all data
        $this->deleteAll(array(
            'UserHasTag.user_id' => $userID
        ));
        //Insert new links
        foreach ($tags as $tag) {
            $newEntry = array(
                'id' => null,
                'user_id' => $userID,
                'tag_id' => $tag
            );
            $this->create();
            $this->save($newEntry);
            $this->clear();
            parent::setEditFlag();
        }
    }

    /**Get the Users in a Tag
     * @param $tagID int|string ID of the Tag
     * @return array|null Users in the tag
     */
    public function getUsersForTag($tagID)
    {
        return $this->find('all', array(
                'fields' => array(
                    'User.id',
                    'User.username'
                ),
                'conditions' => array(
                    'UserHasTag.tag_id' => $tagID
                ),
                'joins' => array(
                    array(
                        'table' => 'users',
                        'alias' => 'User',
                        'type' => 'INNER',
                        'conditions' => array(
                            'UserHasTag.user_id = User.id'
                        )
                    )
                ))
        );
    }

    /**Remove the User form Tag
     * @param $userID int|string UserID to be Removed
     * @param $tagID int|string TagID to be Removed
     */
    public function removeUserFromTag($userID, $tagID)
    {
        $this->deleteAll(array(
            'UserHasTag.user_id' => $userID,
            'UserHasTag.tag_id' => $tagID
        ), false);
        parent::setEditFlag();
    }
}