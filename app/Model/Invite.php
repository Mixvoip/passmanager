<?php

/**
 * Created by IntelliJ IDEA.
 * User: chris
 * Date: 05.04.16
 * Time: 11:33
 */
class Invite extends AppModel
{
    /**Invite a user to the Folder
     * @param $userID array The id of the users to be invited
     * @param $folderID int|string The id of the folder to be invited
     * @param $password string Password of the user performing the invite
     * @return null|String The key for the Invite (null if no invite has been created)
     * @throws Exception
     */
    public function inviteUsersToFolder($userID, $folderID, $password)
    {
        if (count($userID) > 0) {
            App::import('Model', 'UsersHasFolder');
            App::import('Model', 'Invite');
            $UsersHasFolder = new UsersHasFolder();
            $Invite = new Invite();

            $links = $UsersHasFolder->find('all', array(
                'conditions' => array(
                    'UsersHasFolder.folder_id' => $folderID
                )
            ));

            $invites = $Invite->find('all', array(
                'conditions' => array(
                    'Invite.folder_id' => $folderID
                )
            ));

            $linkUserIDs = parent::extractField($links, 'UsersHasFolder', 'user_id', true);
            $inviteGuestUserIDs = parent::extractField($invites, 'Invite', 'user_id', true);

            $crypto_user = parent::getCryptoWrapper();
            $crypto_link = parent::getCryptoWrapper();

            $inviteKey = $crypto_link->genPassword();

            $crypto_user->setKey($password);
            $crypto_link->setKey($inviteKey);

            $link = $UsersHasFolder->find('first', array(
                'conditions' => array(
                    'UsersHasFolder.user_id' => $_SESSION['user_id'],
                    'UsersHasFolder.folder_id' => $folderID
                )
            ));

            $sharedKey = $crypto_user->decrypt($link['UsersHasFolder']['shared_key_for_User']);
            $invitePassword = $crypto_link->encrypt($sharedKey);

            foreach ($userID as $id) {
                if (!(in_array($id, $linkUserIDs) || in_array($id, $inviteGuestUserIDs))) {
                    $inviteEntry = array(
                        'id' => null,
                        'owner_user_id' => $_SESSION['user_id'],
                        'guest_user_id' => $id,
                        'folder_id' => $folderID,
                        'folder_password' => $invitePassword,
                        'invite_key' => '',
                        'create_time' => parent::getActualDateForDB()
                    );

                    $this->create();
                    $this->save($inviteEntry);
                    $this->clear();
                }
            }
            return $inviteKey;
        } else {
            return null;
        }
    }

    /**Invite all users to the folder
     * @param $createUserID int|string User ID performing the Invite
     * @param $folderID int|string The id of the folder to be invited
     * @param $folderPassword string password of the Folder
     * @throws Exception
     */
    public function inviteAllUsersToFolder($createUserID, $folderID, $folderPassword)
    {

        App::import('Model', 'User');
        $User = new User();
        $users = $User->find('all', array(
            'conditions' => array('User.id != ' . $createUserID)
        ));

        foreach ($users as $user) {
            $inviteEntry = array(
                'id' => null,
                'owner_user_id' => $_SESSION['user_id'],
                'guest_user_id' => $user['User']['id'],
                'folder_id' => $folderID,
                'folder_password' => $folderPassword,
                'invite_key' => '',
                'create_time' => parent::getActualDateForDB()
            );
            $this->create();
            $this->save($inviteEntry);
            $this->clear();
        }
    }

    /**Accept the Invite
     * @param $password string Password of the User accepting all the Invites
     * @param $userID int|string ID of the User accepting the Invites
     * @param $linkKey string key of the link
     * @param $folderID int id of the folder
     * @return boolean Success
     */
    public function acceptInvite($password, $userID, $linkKey, $folderID)
    {
        App::import('Model', 'UsersHasFolder');
        $UsersHasFolder = new UsersHasFolder();

        $crypto = parent::getCryptoWrapper();
        $crypto->setKey($password);
        $crypto_invite = parent::getCryptoWrapper();
        $crypto_invite->setKey($linkKey);

        $invite = $this->find('first', array(
            'conditions' => array(array('Invite.guest_user_id' => $userID), array('Invite.folder_id' => $folderID))
        ));

        if (count($invite) == 1) {
            $shared_key = $crypto_invite->decrypt($invite['Invite']['folder_password']);

            $suk = $crypto->encrypt($shared_key);
            $linkEntry = array(
                'id' => null,
                'user_id' => $userID,
                'folder_id' => $invite['Invite']['folder_id'],
                'shared_key_for_User' => $suk
            );
            $UsersHasFolder->connectUserFolder($linkEntry);
            $this->deleteAll(array(
                'Invite.guest_user_id' => $userID,
                'Invite.folder_id' => $folderID
            ), false);
            return true;
        } else {
            return false;
        }
    }

    /**Accept all invites
     * @param $password string Password of the User accepting all the Invites
     * @param $userID int|string ID of the User accepting the Invites
     * @param $linkKey string key of the link
     * @param $ids int array with the invite id's
     */
    private function acceptAllInvites($password, $userID, $linkKey, $id)
    {
        App::import('Model', 'UsersHasFolder');
        $UsersHasFolder = new UsersHasFolder();

        $crypto = parent::getCryptoWrapper();
        $crypto->setKey($password);

        $invites = $this->find('all', array(
            'conditions' => array('Invite.guest_user_id' => $userID)
        ));

        foreach ($invites as $invite) {

            $suk = $crypto->encrypt($invite['Invite']['folder_password']);

            $linkEntry = array(
                'id' => null,
                'user_id' => $userID,
                'folder_id' => $invite['Invite']['folder_id'],
                'shared_key_for_User' => $suk
            );

            $UsersHasFolder->connectUserFolder($linkEntry);
        }
        //Delete all invites from that user
        $this->deleteAll(array(
            'Invite.guest_user_id' => $userID
        ), false);
    }

    /** Reject a Invite
     * @param $userID int|string The id of the user who wants to reject the invite
     * @param $folderID int|string The id of the folder for the Invite
     */
    public function rejectInvite($userID, $folderID)
    {
        $this->deleteAll(array(
            'Invite.guest_user_id' => $userID,
            'Invite.folder_id' => $folderID
        ), false);
    }

    /**
     * Check if the invite is not expired
     */
    public function checkInviteTimeOut()
    {
        $timeout = Configure::read('InviteTimeOut');
        if (isset($timeout)) {
            $delIds = array();
            $invites = $this->find('all');
            foreach ($invites as $invite) {
                $inviteTime = strtotime($invite['Invite']['create_time']);
                if ($inviteTime + $timeout < time()) {
                    $delIds[] = (int)$invite['Invite']['id'];
                }
            }
            //Delete all the expired invites
            foreach ($delIds as $delId) {
                $this->delete($delId, false);
            }
        }
    }
}