<?php

/**
 * Created by IntelliJ IDEA.
 * User: chris
 * Date: 05.04.16
 * Time: 11:33
 */
class Password extends AppModel
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
//    , 'Tag' => array(
//            'className' => 'Tag',
//            'foreignKey' => 'tag_id'
//        )
    );

    /**
     * Toggle the favorite of this Password
     * @param $passwordID int|string The id of the record
     * @param $userID int|string The id of the user
     * @return string New fav Value
     */
    public function toggleFavorite($passwordID, $userID)
    {
        App::import('Model', 'Favorite');
        $Favorite = new Favorite();
        if ($Favorite->addFavorite($passwordID, $userID)) {
            return 1;
        } else {
            $Favorite->deleteFavorite($passwordID, $userID);
            return 0;
        }
    }

    /**Get the Favorites fo a user
     * @param $userID int|string The Id of the User
     * @return array|null The favorites of the user
     */
    public function getFavoritePasswords($userID)
    {
        $this->recursive = -1;
        return $this->find('all', array(
            'conditions' => array('Favorite.user_id' => $userID),
            'joins' => array(
                array(
                    'table' => 'favorites',
                    'alias' => 'Favorite',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Favorite.password_id = Password.id'
                    )
                )
            ),
            'order' => 'Password.site_name'
        ));
    }

    public function deletePassword($passwordID)
    {
        $this->delete($passwordID, false);
        App::import('Model', 'Favorite');
        App::import('Model', 'PasswordHasTag');
        $Favorite = new Favorite();
        $Favorite->deleteAll(array('Favorite.password_id' => $passwordID), false);
        $PasswordHasTag = new PasswordHasTag();
        $PasswordHasTag->deleteAll(array('PasswordHasTag.password_id' => $passwordID), false);
        parent::setEditFlag();
    }

    /** Create a new Password
     * @param $userID int|string The ID of the User who created the Password entry
     * @param $userPassword string The Password entry of the User
     * @param $folderID int|string The ID of the Folder in which the Password entry is created
     * @param $username string The Username of the Password entry. (will be encrypted)
     * @param $password string The Password of the Password entry. (will be encrypted)
     * @param $name string The Name of the Password entry.
     * @param $description string The Description of the Password entry.
     * @param $siteUrl string|null The Url of the Password entry.
     * @return int Password id of the new password
     * @throws Exception
     */
    public function createPassword($userID, $userPassword, $folderID, $username, $password, $name, $description, $siteUrl)
    {
        $dbValues = $this->encryptPassword($userPassword, $password, $username, $userID, $folderID);
        $newPassword = array(
            'id' => null,
            'user_id' => $userID,
            'folder_id' => $folderID,
            'username' => $dbValues['username'],
            'password' => $dbValues['password'],
            'site_name' => $name,
            'site_description' => $description,
            'site_url' => $siteUrl,
            'position' => 0,
            'favorite' => 0,
            'create_time' => parent::getActualDateForDB(),
            'update_time' => '',
            'user_recovery' => $dbValues['user_recovery'],
            'password_recovery' => $dbValues['password_recovery']
        );
        $this->create();
        $this->save($newPassword);
        $id = $this->getLastInsertID();
        $this->clear();
        parent::setEditFlag();
        return $id;
    }

    /**Decrypt the Password with the users Key and send it back as assoc array
     * @param $userID int|string The ID of the User who wants to see the password
     * @param $userPassword string The Password of the User
     * @param $passwordID int|string The ID of the Password to be shown
     * @return array Array with the username and password in cleartext. Array values: 'username' 'password'
     */
    public function showPassword($userID, $userPassword, $passwordID)
    {
        return $this->decryptPassword($userID, $userPassword, $passwordID);
    }

    /**Check if the User can edit the Password
     * @param $userID int|string ID of the User
     * @param $passwordID int|string ID of the Password
     * @return bool User can edit?
     */
    public function canUserEditPassword($userID, $passwordID)
    {
        $pw = $this->find('first', array(
            'conditions' => array('Password.id' => $passwordID)
        ));
        $folderID = $pw['Folder']['id'];
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

    /**Update a Password Entry in the DB. When $password is null it's not updated same for $username
     * @param $userID int|string The ID of the User who created the Password entry
     * @param $userPassword string The Password entry of the User
     * @param $passwordID int|string The ID of the Password entry to be updated
     * @param $folderID int|string The ID of the Folder in which the Password entry is created
     * @param $username string|null The Username of the Password entry. (will be encrypted)
     * @param $password string|null The Password of the Password entry. (will be encrypted)
     * @param $name string The Name of the Password entry.
     * @param $description string The Description of the Password entry.
     * @param $siteUrl string|null The Url of the Password entry.
     * @return array Array with saying what has been updated array('Info'=>true,'Username'=>false,'Password'=>false);
     * @throws Exception
     */
    public function editPassword($userID, $userPassword, $passwordID, $folderID, $username, $password, $name, $description, $siteUrl)
    {
        $returnFeedback = array('Info' => true, 'Username' => false, 'Password' => false);
        $update = array(
            'site_name' => $name,
            'site_description' => $description,
            'site_url' => $siteUrl,
            'update_time' => parent::getActualDateForDB()
        );
        //Change Username AND/OR Password
        if (isset($userPassword) && (isset($username) || isset($password))) {
            $dbValues = $this->encryptPassword($userPassword, $password, $username, $userID, $folderID);
            if (isset($username)) {
                $update['username'] = $dbValues['username'];
                $update['user_recovery'] = $dbValues['user_recovery'];
                $returnFeedback['Username'] = true;
            }
            if (isset($password)) {
                $update['password'] = $dbValues['password'];
                $update['password_recovery'] = $dbValues['password_recovery'];
                $returnFeedback['Password'] = true;
            }
        }
        //Update DB
        $this->read(null, $passwordID);
        $this->set($update);
        $this->save();
        $this->clear();
        parent::setEditFlag();
        return $returnFeedback;
    }

    /**ReEncrypt the Password and Username with a new Password
     * @param $passwordID
     * @param $oldPassword
     * @param $newPassword
     * @throws Exception
     */
    public function reEncryptPassword($passwordID, $oldPassword, $newPassword)
    {

        $pw = $this->find('first', array(
            'conditions' => array('Password.id' => $passwordID)
        ));

        App::uses('CryptoWrapper', 'Vendor');
        $crypto_old = new CryptoWrapper();
        $crypto_old->setKey($oldPassword);

        $clear_username = $crypto_old->decrypt($pw['Password']['username']);
        $clear_password = $crypto_old->decrypt($pw['Password']['password']);

        $crypto_new = new CryptoWrapper();
        $crypto_new->setKey($newPassword);

        $enc_username = $crypto_new->encrypt($clear_username);
        $enc_password = $crypto_new->encrypt($clear_password);

        $updatePw = array(
            'username' => $enc_username,
            'password' => $enc_password,
            'update_time' => parent::getActualDateForDB()
        );
        //Update DB
        $this->read(null, $passwordID);
        $this->set($updatePw);
        $this->save();
        $this->clear();
    }

    /** Encrypt a Password an a Username (also with the recovery when enabled)
     * We use the Users Password or if the Folder is shared the Shared Key for that Folder.
     * To get the Shared Key we decrypt the users_has_folders.shared_key_for_User using the Users Password.
     * @param $userPassword string The Password of the User who wants to encrypt
     * @param $password string The Password to be encrypted
     * @param $username string The Username to be encrypted
     * @param $userID string|int The ID of the User who wants to encrypt
     * @param $folderID string|int The ID of the Folder in which the Password is/should be contained
     * @return array Assoc array with the values. Format 'username' => $enc_username,'password' => $enc_password,'user_recovery' => $recoveryUsername,'password_recovery' => $recoveryPassword
     */
    private function encryptPassword($userPassword, $password, $username, $userID, $folderID)
    {
        //Get the containg folder:
        $folder = $this->Folder->find('first', array(
            'conditions' => array('Folder.id' => $folderID)
        ));

        //Create encrypted keys
        $crypto_user = parent::getCryptoWrapper();
        $crypto_user->setKey($userPassword);
        if (isset($folder['Folder']['shared']) && $folder['Folder']['shared'] == 1) {

            $crypto_folder = $this->getFolderCrypto($crypto_user, $userID, $folderID);

            //Encrypt the Password and the Username using the Shared Key
            $enc_username = $crypto_folder->encrypt($username);
            $enc_password = $crypto_folder->encrypt($password);
        } else {
            $enc_username = $crypto_user->encrypt($username);
            $enc_password = $crypto_user->encrypt($password);
        }

        //Create recovery
        $recoveryUsername = null;
        $recoveryPassword = null;
        if (Configure::read('Recovery.Enable')) {
            $key = Configure::read('Recovery.Key');
            if (isset($key)) {
                $crypto_recovery = parent::getCryptoWrapper();
                $crypto_recovery->setKey($key);
                $recoveryPassword = $crypto_recovery->encryptRecovery($password);
                $recoveryUsername = $crypto_recovery->encryptRecovery($username);
            }
        }

        return array(
            'username' => $enc_username,
            'password' => $enc_password,
            'user_recovery' => $recoveryUsername,
            'password_recovery' => $recoveryPassword
        );
    }

    /**Decrypt the Password
     * @param $userID int|string ID of the User who wants to decrypt
     * @param $userPassword string Password of the User who wants to decrypt
     * @param $passwordID int|string ID Of the Password to be decrypted
     * @return array Array with the username and password in cleartext. Array values: 'username' 'password'
     */
    private function decryptPassword($userID, $userPassword, $passwordID)
    {
        $passwordRecord = $this->find('first', array(
            'conditions' => array('Password.id' => $passwordID)
        ));

        $clear_username = "";
        $clear_password = "";

        $crypto_user = parent::getCryptoWrapper();
        $crypto_user->setKey($userPassword);
        if (isset($passwordRecord['Folder']['shared']) && $passwordRecord['Folder']['shared'] == 1) {

            $crypto_folder = $this->getFolderCrypto($crypto_user, $userID, $passwordRecord['Folder']['id']);

            //Encrypt the Password and the Username using the Shared Key
            $clear_username = $crypto_folder->decrypt($passwordRecord['Password']['username']);
            $clear_password = $crypto_folder->decrypt($passwordRecord['Password']['password']);
        } else {
            $clear_username = $crypto_user->decrypt($passwordRecord['Password']['username']);
            $clear_password = $crypto_user->decrypt($passwordRecord['Password']['password']);
        }

        return array(
            'username' => trim($clear_username),
            'password' => trim($clear_password)
        );
    }

    /** [Helper] Get the crypto wrapper for the folder
     * @param $crypto_user CryptoWrapper The CryptoWrapper from the user
     * @param $userID int|string The ID of the User
     * @param $folderID int|string The ID of the Folder
     * @return CryptoWrapper The CryptoWrapper for the Folder
     */
    private function getFolderCrypto($crypto_user, $userID, $folderID)
    {
        App::import('Model', 'UsersHasFolder');
        $UsersHasFolder = new UsersHasFolder();
        $folderLink = $UsersHasFolder->find('first', array(
            'conditions' => array(
                array('UsersHasFolder.user_id' => $userID),
                'AND' => array('UsersHasFolder.folder_id' => $folderID)
            )
        ));

        //Decrypt the shared key
        $shared_key = $crypto_user->decrypt($folderLink['UsersHasFolder']['shared_key_for_User']);

        //Use the shared_key as key for the decryption of the password and folder
        $crypto_folder = parent::getCryptoWrapper();
        $crypto_folder->setKey($shared_key);
        return $crypto_folder;
    }
}