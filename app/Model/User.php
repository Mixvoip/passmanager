<?php

/**
 * Created by IntelliJ IDEA.
 * User: chris
 * Date: 05.04.16
 * Time: 10:20
 */
class User extends AppModel
{

    public $hasMany = array(
        'Password' => array(
            'className' => 'Password',
            'foreignKey' => 'user_id'
        ),
        'Note' => array(
            'className' => 'Note',
            'foreignKey' => 'user_id'
        )
    );

    public $hasAndBelongsToMany = array(
        'Folder' => array(
            'className' => 'Folder',
            'joinTable' => 'users_has_folders',
            'foreignKey' => 'user_id',
            'associationForeignKey' => 'folder_id'
        ),
        /*'User' => array(
            'className' => 'Tag',
            'joinTable' => 'user_has_tags',
            'foreignKey' => 'user_id',
            'associationForeignKey' => 'tag_id'
        )*/
    );

    /** Checkes if a user can login with a password
     * @param $username string Username of the user
     * @param $password string Un-hashed Password
     * @return array Result array
     *         Format on error: array('success'=>false ,'error'=>'<error_message>');
     *         Format on success: array('success'=>true, 'user_id'=> <the id of the user>,
     *                                  'access_level'=> <access level of the User>,
     *                                  'email'=> <email of the user>);
     */
    public function login($username, $password)
    {
        //Query Database
        $user = $this->find("first", array(
            'conditions' => array('User.username' => $username)
        ));

        //Check for valid user an password:
        if (isset($user['User'])) {
            if ($user['User']['password'] === $this->getCryptoWrapper()->hashPassword($password, $user['User']['salt'])) {
                if ($user['User']['blocked'] == 0) {
                    //Set last login:
                    $this->id = $user['User']['id'];
                    $this->saveField('lastlogin', parent::getActualDateForDB());
                    $this->clear();
                    return array('success' => true, 'user_id' => $user['User']['id'],
                        'access_level' => $user['User']['access_level'],
                        'email' => $user['User']['email']);
                } else {
                    return array('success' => false, 'error' => 'User is blocked. Please contact yor admin');
                }
            } else {
                return array('success' => false, 'error' => 'Wrong Password');
            }
        } else {
            return array('success' => false, 'error' => 'Wrong User');
        }
    }

    /** Create a new user and add it to the Database
     * @param String $username The username of the new user
     * @param String $password The password of the user
     * @param string $email The email of the user
     * @param Boolean $blockUser Is the User blocked
     * @param int $access_level The access level of the user
     * @return int The ID of the new User
     * @throws Exception
     */
    public function createUser($username, $password, $email = '', $blockUser = false, $access_level = 0)
    {
        $crypto = parent::getCryptoWrapper();
        $salt = $crypto->genSalt();
        $passwordHash = $crypto->hashPassword($password, $salt);
        $newUser = array(
            'id' => null,
            'username' => $username,
            'email' => $email,
            'access_level' => $access_level,
            'create_time' => parent::getActualDateForDB(),
            'update_time' => '',
            'lastlogin' => '',
            'blocked' => (int)$blockUser,
            'salt' => $salt,
            'password' => $passwordHash
        );
        $this->create();
        $this->save($newUser);
        $id = $this->getLastInsertID();
        $this->clear();
        return $id;
    }

    /**Edit a User. If Recovery is enabled reencrypt the Passwords of the User
     * @param $userID int|string ID of the User
     * @param $username string new Username of the User
     * @param $password string new Password of the User works only if recovery is enabled
     * @param $mail string New mail of the User
     * @param $accessLevel int|string New Access Level of the user
     * @throws Exception
     */
    public function editUser($userID, $username, $password, $mail, $accessLevel)
    {
        if (isset($password) && Configure::read('Recovery.Enable')) {
            $this->changePassword($userID, $password);
            App::import('Model', 'Folder');
            $Folder = new Folder();
            $Folder->reEncryptFolder(null, $password, $userID);
        }
        $updateUser = array(
            'User.email' => "'" . $mail . "'",
            'User.access_level' => $accessLevel,
            'User.update_time' => "'" . parent::getActualDateForDB() . "'"
        );
        if (isset($username)) {
            $updateUser['User.username'] = "'" . $username . "'";
        }
        $this->updateAll($updateUser, array(
            'User.id' => $userID
        ));
        //$this->save();
        //$this->commit();
        //$this->clear();
    }

    /**Change the Password of a user
     * @param $userID int|string The ID of the User
     * @param $newPassword string The new Password for the User
     * @throws Exception
     */
    public function changePassword($userID, $newPassword)
    {
        $crypto = parent::getCryptoWrapper();
        $salt = $crypto->genSalt();
        $password = $crypto->hashPassword($newPassword, $salt);
        $updateUser = array(
            'password' => $password,
            'salt' => $salt,
            'update_time' => parent::getActualDateForDB()
        );
        //Update DB
        $this->read(null, $userID);
        $this->set($updateUser);
        $this->save();
        $this->clear();
    }

    /**Toggles the blocking state of the user
     * @param $userID int|string The ID of the User to change the block
     */
    public function toggleBlock($userID)
    {
        $user = $this->find('first', array(
            'conditions' => array('User.id' => $userID)
        ));
        $updateUser = array(
            'blocked' => ($user['User']['blocked'] == 0 ? 1 : 0),
            'update_time' => parent::getActualDateForDB()
        );
        //Update DB
        $this->read(null, $userID);
        $this->set($updateUser);
        $this->save();
        $this->clear();
    }

    /**Delete the user with all his passwords, Images, Folder (private only)
     * @param int|string $userID The ID of the User to be deleted
     */
    public function deleteUser($userID)
    {
        //Load other Models
        App::import('Model', 'Folder');
        App::import('Model', 'Password');
        App::import('Model', 'UsersHasFolder');
        App::import('Model', 'Image');
        $Folder = new Folder();
        $Password = new Password();
        $UsersHasFolder = new UsersHasFolder();
        $Image = new Image();
        //Find all entry's for that user
        $data = $Folder->find('all', array(
                'conditions' => array('UsersHasFolder.user_id' => $userID),
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
        //Delete all the Private Passwords
        foreach ($data as $entry) {
            if (!isset($entry['Folder']['shared']) || $entry['Folder']['shared'] == 0) {
                //It's a private folder
                foreach ($entry['Password'] as $passwords) {
                    $Password->delete($passwords['id'], false);
                }
                $Folder->delete($entry['Folder']['id'], false);
            }
        }
        //Delete all Links
        $UsersHasFolder->deleteAll(array(
            'UsersHasFolder.user_id' => $userID
        ), false);
        //Delete the Images of the User
        $Image->deleteAllImagesFromUser($userID);
        //Delete the user itself
        $this->delete($userID, false);
    }

    /**Get all the users which are not shared or invited with the folder
     * @param $folderID int|string Id of the folder
     * @return array|null All users are not shared or invited
     */
    public function getNotSharedUsers($folderID)
    {
        return $this->find('all', array(
                //'fields' => array('User.id', 'User.username'),
                'conditions' => array(
                    array('UsersHasFolder.folder_id IS NULL'),
                    array('Invite.folder_id IS NULL')
                ),
                'joins' => array(
                    array(
                        'table' => 'users_has_folders',
                        'alias' => 'UsersHasFolder',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'UsersHasFolder.user_id = User.id',
                            'UsersHasFolder.folder_id = Folder.id'
                        )), array(
                        'table' => 'folders',
                        'alias' => 'Folder',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'UsersHasFolder.folder_id = folder.id',
                            'Folder.id = ' . $folderID
                        )
                    ), array(
                        'table' => 'invites',
                        'alias' => 'Invite',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Invite.guest_user_id = User.id'
                        )
                    )
                )
            )
        );
    }

    /**Check the Password of the User
     * @param $enteredPassword string The User's Password
     * @param $userID int|string The ID of the User
     * @return bool Is the Password correct?
     */
    public function checkPassword($enteredPassword, $userID)
    {
        if (isset($enteredPassword, $userID)) {
            $userFromDB = $this->find("first", array(
                'conditions' => array('User.id' => $userID)
            ));
            return parent::getCryptoWrapper()->hashPassword($enteredPassword, $userFromDB['User']['salt']) === $userFromDB['User']['password'];
        } else {
            return false;
        }

    }
}