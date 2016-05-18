<?php

/**
 * Created by IntelliJ IDEA.
 * User: chris
 * Date: 05.04.16
 * Time: 11:33
 */
class Folder extends AppModel
{
    public $hasMany = array(
        'Password' => array(
            'className' => 'Password',
            'foreignKey' => 'folder_id'
        )
    );

    /*
    public $belongsTo = array(
        'Image'=>array(
            'className'=>'Image',
            'foreignKey' => 'image_id',
            'conditions' => array('Folder.image_id != null')
        )
    );*/

    /**Create a new Folder
     * @param $userID int|string The ID of the User who wants to create the Folder
     * @param $folderName string The name of the Folder
     * @param $folderDescription string The description of the Folder
     * @param $privateFolder boolean Is it a private folder
     * @param string $userPW string The password of the user creating the Folder
     * @param $shareWithAll boolean Auto share with all other users
     * @return array Array containing the record for the relation between user and folder
     */
    public function createFolder($userID, $folderName, $folderDescription, $privateFolder, $userPW = "", $shareWithAll)
    {
        parent::setEditFlag();
        if ($privateFolder) {
            return $this->createPrivateFolder($userID, $folderName, $folderDescription);
        } else {
            return $this->createSharedFolder($userID, $folderName, $folderDescription, $userPW, $shareWithAll);
        }
    }

    /**Edit a Folder
     * @param $folderID int|string The ID of the Folder
     * @param $imageID int|string The ID of the Image
     * @param $folderName string The new Name
     * @param $folderDescription string The new Description
     * @throws Exception
     */
    public function editFolder($folderID, $imageID, $folderName, $folderDescription)
    {
        $entry = array(
            'image_id' => $imageID,
            'name' => $folderName,
            'description' => $folderDescription
        );
        //Update DB
        $this->read(null, $folderID);
        $this->set($entry);
        $this->save();
        $this->clear();
        parent::setEditFlag();
    }

    /**Check if the User can edit the Folder
     * @param $userID int|string ID of the User
     * @param $folderID int|string ID of the Folder
     * @return bool User can edit?
     */
    public function userCanEditFolder($userID, $folderID)
    {
        App::import('Model', 'UsersHasFolder');
        $UsersHasFolder = new UsersHasFolder();
        $res = $UsersHasFolder->find('count', array(
            'conditions' => array(
                array('UsersHasFolder.user_id' => $userID),
                'AND' => array('UsersHasFolder.folder_id' => $folderID)
            )
        ));
        return $res > 0;
    }

    /**Remove Image ID from all Images
     * @param $imageID int|string ID of the Image to remove
     */
    public function removeImageFromFolders($imageID)
    {
        $this->updateAll(
            array('image_id' => null),
            array('image_id' => $imageID)
        );
    }

    /**ReEncrypt all Passwords and User SharedKey in the Folders of the User
     * @param $oldPassword string Old UserPassword
     * @param $newPassword string New UserPassword
     * @param $userID int|string ID of the User
     * @return array Result Array
     */
    public function reEncryptFolder($oldPassword, $newPassword, $userID)
    {
        if ($_SESSION['user_id'] == $userID) {
            //The actual logged in user wants to change his password
            App::import('Model', 'UsersHasFolder');
            $UsersHasFolder = new UsersHasFolder();
            $folderLinks = $UsersHasFolder->find('all', array(
                'conditions' => array('UsersHasFolder.user_id' => $userID)
            ));
            foreach ($folderLinks as $folderLink) {
                if (isset($folderLink['UsersHasFolder']['shared_key_for_User'],
                        $folderLink['Folder']['shared']) && $folderLink['Folder']['shared'] == 1
                ) {
                    //Shared Folder
                    $UsersHasFolder->reEncryptSharedKey($folderLink['UsersHasFolder']['id'],
                        $folderLink['UsersHasFolder']['shared_key_for_User'],
                        $oldPassword, $newPassword);
                } else {
                    //Private Folder
                    App::import('Model', 'Password');
                    $Password = new Password();
                    $passwords = $Password->find('all', array(
                        'conditions' => array(
                            array('Password.user_id' => $userID),
                            array('Password.folder_id' => $folderLink['Folder']['id'])
                        )
                    ));
                    foreach ($passwords as $password) {
                        $Password->reEncryptPassword($password['Password']['id'], $oldPassword, $newPassword);
                    }
                }
            }
            return array('error' => false, 'msg' => 'Password updated');
        } else {
            //Some other user wants to change the password but this only works if recovery is enabled
            $recoveryKey = Configure::read('Recovery.Key');
            if (Configure::read('Recovery.Enable') && isset($recoveryKey)) {

            } else {
                return array('error' => true, 'msg' => 'Password can\'t be re encrypted because the recovery is not enabled');
            }
        }
    }

    /**Delete the Link or the Folder it self
     * @param $folderID int|string The id of the folder
     * @param $userID int|string The id of the user
     */
    public function deleteFolder($folderID, $userID)
    {
        App::import('Model', 'UsersHasFolder');
        $UsersHasFolder = new UsersHasFolder();
        $folderLinks = $UsersHasFolder->find('all', array(
            'conditions' => array('UsersHasFolder.folder_id' => $folderID)
        ));

        $UsersHasFolder->deleteAll(
            array('UsersHasFolder.folder_id' => $folderID,
                'UsersHasFolder.user_id' => $userID),
            false);

        if (count($folderLinks) <= 1) {
            //remove also folder and password
            App::import('Model', 'Password');
            App::import('Model', 'Invite');
            $Password = new Password();
            $Invites = new Invite();
            $this->delete($folderID, false);
            $Password->deleteAll(array(
                'Password.folder_id' => $folderID
            ), false);
            //Remove Invites
            $Invites->deleteAll(array(
                'Invite.folder_id' => $folderID
            ), false);
        }
        parent::setEditFlag();
    }

    /**Returns all the Folder with all Password for the user
     * @param $userID int|string ID of the user
     * @return array|null List of the folder for the user
     */
    public function getAllFolderForUser($userID)
    {
        $firstFolderID = Configure::read('FirstFolderID');
        $result = Cache::read('newest_folders', 'short');
        $edited = Cache::read('edited', 'short');
        if (!$result || (isset($edited) && $edited)) {
            $result = $this->find('all', array(
                    'fields' => array('Folder.id',
                        'Folder.user_id',
                        'Folder.image_id',
                        'Folder.name',
                        'Folder.description',
                        'Folder.shared'
                    ),
                    'conditions' => array(
                        array('UsersHasFolder.user_id' => $userID),
                        array("Folder.id != $firstFolderID")
                    ),
                    'joins' => array(
                        array(
                            'table' => 'users_has_folders',
                            'alias' => 'UsersHasFolder',
                            'type' => 'INNER',
                            'conditions' => array(
                                'UsersHasFolder.folder_id = Folder.id'
                            )
                        )
                    ),
                    'order' => 'LOWER(Folder.name), Folder.name'
                )
            );
            $fistFolder = $this->find('first', array(
                    'fields' => array('Folder.id',
                        'Folder.user_id',
                        'Folder.image_id',
                        'Folder.name',
                        'Folder.description',
                        'Folder.shared'
                    ),
                    'conditions' => array(
                        array('UsersHasFolder.user_id' => $userID),
                        array("Folder.id" => $firstFolderID)
                    ),
                    'joins' => array(
                        array(
                            'table' => 'users_has_folders',
                            'alias' => 'UsersHasFolder',
                            'type' => 'INNER',
                            'conditions' => array(
                                'UsersHasFolder.folder_id = Folder.id'
                            )
                        )
                    )
                )
            );
            if(count($fistFolder)>0){
                array_unshift($result, $fistFolder);
            }
            Cache::write('newest_folders', $result, 'short');
            parent::clearEditFlag();
        }
        return $result;
    }

    /**Create a new Private Folder
     * @param $userID int|string The ID of the User who wants to create the Folder
     * @param $folderName string The name of the Folder
     * @param $folderDescription string The description of the Folder
     * @return array Array containing the record for the relation between user and folder
     * @throws Exception
     */
    private function createPrivateFolder($userID, $folderName, $folderDescription)
    {
        $newFolder = array(
            'id' => null,
            'user_id' => $userID,
            'image_id' => null,
            'name' => $folderName,
            'description' => $folderDescription,
            'position' => 0,
            'shared' => 0,
            'shared_key_recovery' => null
        );
        $this->create();
        $this->save($newFolder);
        $id = $this->getLastInsertID();
        $this->clear();

        $linkEntry = array(
            'id' => null,
            'user_id' => $userID,
            'folder_id' => $id,
            'shared_key_for_User' => null
        );
        return $linkEntry;
    }

    /**Create a public new Folder
     * @param $userID int|string The ID of the User who wants to create the Folder
     * @param $folderName string The name of the Folder
     * @param $folderDescription string The description of the Folder
     * @param string $userPW string The password of the user creating the Folder
     * @param $shareWithAll boolean Auto share with all other users
     * @return array Array containing the record for the relation between user and folder
     * @throws Exception
     */
    private function createSharedFolder($userID, $folderName, $folderDescription, $userPW, $shareWithAll)
    {
        $crypto = parent::getCryptoWrapper();
        $sharedKey = $crypto->genPassword();

        $crypto->setKey($userPW);
        $enc_suk = $crypto->encrypt($sharedKey);

        //Recovery for folder
        $recoveryKey = Configure::read('Recovery.Key');
        $recoverySharedKey = null;
        if (Configure::read('Recovery.Enable') && isset($recoveryKey)) {
            $crypto_recovery = new CryptoWrapper();
            $crypto_recovery->setKey($recoveryKey);
            $recoverySharedKey = $crypto_recovery->encryptRecovery($sharedKey);
        }

        $newFolder = array(
            'id' => null,
            'user_id' => $userID,
            'image_id' => null,
            'name' => $folderName,
            'description' => $folderDescription,
            'position' => 0,
            'shared' => 1,
            'shared_key_recovery' => $recoverySharedKey
        );
        $this->create();
        $this->save($newFolder);
        $id = $this->getLastInsertID();
        $this->clear();

        $linkEntry = array(
            'id' => null,
            'user_id' => $userID,
            'folder_id' => $id,
            'shared_key_for_User' => $enc_suk
        );

        if ($shareWithAll) {
            //Invite User
            $crypto_invite = parent::getCryptoWrapper();
            $linkKey = $crypto_invite->genPassword();
            $crypto_invite->setKey($linkKey);
            $shared_key_invite = $crypto_invite->encrypt($sharedKey);
            App::import('Model', 'Invite');
            $Invite = new Invite();
            $Invite->inviteAllUsersToFolder($_SESSION['user_id'], $id, $shared_key_invite);
            $linkEntry['linkKey'] = $linkKey;
        }
        return $linkEntry;
    }
}
