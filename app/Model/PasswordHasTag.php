<?php

/**
 * Created by IntelliJ IDEA.
 * User: chris
 * Date: 20.04.16
 * Time: 13:26
 */
class PasswordHasTag extends AppModel
{
    /**Update the links between the Password an the tags
     * @param $passwordID int|string The id of the user
     * @param $tags array Array with the new tags
     * @throws Exception
     */
    public function updateTags($passwordID, $tags)
    {
        //hack Review code to be more efficient
        //remove all data
        $this->deleteAll(array(
            'PasswordHasTag.password_id' => $passwordID
        ));
        //Insert new links
        foreach ($tags as $tag) {
            $newEntry = array(
                'id' => null,
                'password_id' => $passwordID,
                'tag_id' => $tag
            );
            $this->create();
            $this->save($newEntry);
            $this->clear();
            parent::setEditFlag();
            parent::setEditFlagTags();
        }
    }

    public function addLink($passwordID, $tags)
    {
        $this->updateTags($passwordID, $tags);
        parent::setEditFlag();
        parent::setEditFlagTags();
    }
}