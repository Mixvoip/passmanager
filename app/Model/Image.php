<?php

/**
 * Created by IntelliJ IDEA.
 * User: chris
 * Date: 14.04.16
 * Time: 12:00
 */
class Image extends AppModel
{

    /*
    public $hasMany = array(
        'Password' => array(
            'className' => 'Password',
            'foreignKey' => 'image_id'
        )
    );
    */

    /**Create a new Image
     * @param $userID int|string The ID of the Owner of the Image
     * @param $serverPath string The Path of the Image
     * @param $name string The name of the Image
     * @param $public boolean Is it a public Image
     * @throws Exception
     */
    public function createImage($userID, $serverPath, $name, $public)
    {
        $newImage = array(
            'id' => null,
            'user_id' => $userID,
            'server_path' => $serverPath,
            'name' => $name,
            'public' => ($public) ? 1 : 0
        );

        $this->create();
        $this->save($newImage);
        $this->clear();
    }

    /**Deletes all the images from the User and removes it form the Folders
     * @param $userID int|string ID of the User to delete it's Images
     */
    public function deleteAllImagesFromUser($userID)
    {
        //todo: Implement the function
        //do it when the image logic is created
    }
}