<?php

/**
 * Created by IntelliJ IDEA.
 * User: chris
 * Date: 05.04.16
 * Time: 11:33
 */
class UsersHasFolder extends AppModel
{

    public $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id'
        ),
        'Folder' => array(
            'className' => 'Folder',
            'foreignKey' => 'folder_id'
        )
    );

    /**Connect a User with a password
     * @param $linkEntry array Entry to be saved in the DB
     * @return int|string ID of the new Record
     * @throws Exception
     */
    public function connectUserFolder($linkEntry)
    {
        $this->create();
        $this->save($linkEntry);
        $id = $this->getLastInsertID();
        $this->clear();
        parent::setEditFlag();
        return $id;
    }

    public function addUserToAllFolders($newUserID, $newUserPassword, $creatorUserID, $creatorPassword)
    {
        //Get all the Folder
        $links = $this->find('all', array(
            'conditions' => array(
                'UsersHasFolder.user_id' => $creatorUserID
            )
        ));
        $crypto_creator = parent::getCryptoWrapper();
        $crypto_creator->setKey($creatorPassword);
        $crypto_newUser = new CryptoWrapper();
        $crypto_newUser->setKey($newUserPassword);
        foreach ($links as $link) {
            $sharedKey = $crypto_creator->decrypt($link['UsersHasFolder']['shared_key_for_User']);
            $suk = $crypto_newUser->encrypt($sharedKey);
            $entry = array(
                'id' => null,
                'user_id' => $newUserID,
                'folder_id' => $link['UsersHasFolder']['folder_id'],
                'shared_key_for_User' => $suk
            );
            $this->connectUserFolder($entry);
        }
        parent::setEditFlag();
    }

    /**ReEncrypt the SharedKey for User and update the DB
     * @param $usersHasFolderID string|int ID of the UsersHasFolder record
     * @param $oldSharedKeyForUser string Old SharedKey for User
     * @param $oldPassword string The old User Password
     * @param $newPassword string The new User Password
     * @throws Exception
     */
    public function reEncryptSharedKey($usersHasFolderID, $oldSharedKeyForUser, $oldPassword, $newPassword)
    {
        $crypto_old = parent::getCryptoWrapper();
        $crypto_old->setKey($oldPassword);

        $sharedKey = $crypto_old->decrypt($oldSharedKeyForUser);

        $crypto_new = new CryptoWrapper();
        $crypto_new->setKey($newPassword);

        $newSku = $crypto_new->encrypt($sharedKey);
        $this->updateUserSharedKey($usersHasFolderID, $newSku);
    }

    /**Update the User SharedKey in the DB
     * @param $usersHasFolderID string|int ID of the UsersHasFolder record
     * @param $sharedKeyForUser string New SharedKey for User
     * @throws Exception
     */
    public function updateUserSharedKey($usersHasFolderID, $sharedKeyForUser)
    {
        $update = array(
            'shared_key_for_User' => $sharedKeyForUser
        );

        //Update DB
        $this->read(null, $usersHasFolderID);
        $this->set($update);
        $this->save();
        $this->clear();
        parent::setEditFlag();
    }
}