<?php
/**
 * Created by IntelliJ IDEA.
 * User: chris
 * Date: 05.04.16
 * Time: 10:22
 */


App::uses('AjaxController', 'Controller');

/**Controller to manage Requests dealing with users
 * @property User User The model of the User
 * @property Folder Folder The model of the Folder
 * @property UsersHasFolder UsersHasFolder The model of the UsersHasFolder
 * @property UserHasTag UserHasTag The model of the UserHasTag
 * @property Tag Tag The model of the Tag
 */
class UsersController extends AjaxController
{
    /**
     * Changes the password of the actual user (encrypts his passwords and shared keys with the new password)
     */
    public function changePassword()
    {
        //Long pending action incomming
        ini_set('max_execution_time', 300);
        $userID = $_SESSION['user_id'];
        $_POST['user_id'] = $userID; //To make this work with my function
        if (parent::checkIfRequestIsAllowed(parent::EDIT_PASSWORD_USER_ACTION,
            parent::transformPostDataToObject('user_id', 'task'))
        ) {
            //Filter Input
            $userPass = filter_input(INPUT_POST, 'oldPassword', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            $newPass = filter_input(INPUT_POST, 'newPassword', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            $confirmPass = filter_input(INPUT_POST, 'confirmPassword', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            if (isset($newPass, $userPass, $confirmPass)) {
                if ($this->User->checkPassword($userPass, $userID)) {
                    if (strlen($newPass) >= Configure::read('MinPasswordLength')) {
                        if ($newPass == $confirmPass) {
                            $this->User->changePassword($userID, $newPass);
                            //Re encrypt all the Passwords for that user
                            $res = $this->Folder->reEncryptFolder($userPass, $newPass, $userID);
                            if ($res['error'] == true) {
                                $this->Flash->error($res['msg']);
                            } else {
                                //log the user out
                                $this->logout();

                                $this->Flash->success('Password changed and other Passwords has been encrypted');

                                //redirect to login
                                return $this->redirect(
                                    array('controller' => 'App', 'action' => 'index')
                                );
                            }
                        } else {
                            $this->Flash->error('Passwords don\'t Match');
                        }
                    } else {
                        $this->Flash->error('Password must be at least ' . Configure::read('MinPasswordLength') . ' characters long');
                    }
                } else {
                    $this->Flash->error('Your Password is wrong');
                }
            } else {
                $this->Flash->error('Not all Passwords where set');
            }
        }
        return $this->redirect(
            array('controller' => 'Passmanager', 'action' => 'index')
        );
    }

    /**
     * Get the edit user popup for the admins
     */
    public function getEditPopup()
    {
        $user_data = $this->request->input('json_decode');
        if (parent::checkIfRequestIsAllowedAdmin(parent::POPUP_EDIT_USER_ACTION, $user_data)) {
            //Get user Info
            $user = $this->User->find('first', array(
                'conditions' => array('User.id' => $user_data->id)
            ));
            $this->set('entry', $user['User']);
            //get Tags for user
            $this->set('userTagList', $this->getTagArrayForUser($user_data->id));
            $this->render('/Elements/editUserPopup');
        }
    }

    /**
     * Edit a user and send the new user table as response
     */
    public function edit()
    {
        $this->autoLayout = true;

        if (parent::checkIfRequestIsAllowedAdmin(parent::EDIT_USER_ACTION,
            parent::transformPostDataToObject('user_id', 'task'))
        ) {
            //filter input:
            $username = filter_input(INPUT_POST, 'edit_user_name', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            $mail = filter_input(INPUT_POST, 'edit_user_mail', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            $password = filter_input(INPUT_POST, 'edit_user_password', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            $accessLevel = filter_input(INPUT_POST, 'edit_user_accessLevel', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
            $userID = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
            $tags = (isset($_POST['tags']) ? $_POST['tags'] : array());
            if (isset($userID, $username) && (!Configure::read('Recovery.Enable') || isset($password))) {
                //Set access level to 0 if not vaild
                if (!isset($accessLevel)) $accessLevel = 0;
                $this->User->editUser($userID, $username, $password, $mail, $accessLevel);
                $this->UserHasTag->updateTags($userID, $tags);
                $this->Session->write('User.email', $mail);
                $this->sendUserTable();
            } else {
                $$this->response->statusCode(400);
                $this->response->body('{"message":"Not all required Fields where filled"}');
            }
        }
    }

    /**
     * Create a new user and send the new user table as response
     */
    public function create()
    {
        $userID = $_SESSION['user_id'];
        $_POST['user_id'] = $userID; //To make this work with my function
        if (parent::checkIfRequestIsAllowedAdmin(parent::CREATE_USER_ACTION,
            parent::transformPostDataToObject('user_id', 'task'))
        ) {
            //Filter the input
            $username = filter_input(INPUT_POST, 'create_user_username', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            $password = filter_input(INPUT_POST, 'create_user_password', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            $yourPassword = filter_input(INPUT_POST, 'create_user_yourPassword', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            $mail = filter_input(INPUT_POST, 'create_user_mail', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            $accessLevel = filter_input(INPUT_POST, 'create_user_accessLevel', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
            $tagID = filter_input(INPUT_POST, 'tagID', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
            if (isset($username, $password, $yourPassword)) {
                if ($this->User->checkPassword($yourPassword, $userID)) {
                    if (!isset($accessLevel)) $accessLevel = 0;
                    try {
                        $newUserID = $this->User->createUser($username, $password, $mail, false, $accessLevel);
                        //Add User to all Folders
                        App::import('Model', 'UsersHasFolder');
                        $UsersHasFolder = new UsersHasFolder();
                        $UsersHasFolder->addUserToAllFolders($newUserID, $password, $userID, $yourPassword);
                        if (isset($tagID) && $tagID > 0) {
                            //Add Tag to user
                            $this->UserHasTag->updateTags($newUserID, array($tagID));
                        }
                        $this->sendUserTable();
                    } catch (Exception $ex) {
                        $this->response->statusCode(400);
                        $this->response->body('{"message":"User Already exits"}');
                    }
                } else {
                    $this->response->statusCode(400);
                    $this->response->body('{"message":"Your Password is wrong"}');
                }
            } else {
                $this->response->statusCode(400);
                $this->response->body('{"message":"Not all required Fields where filled"}');
            }
        }
    }

    /**
     * Toggle the block of the user and send the new user table as response
     */
    public function toggleUserBlock()
    {
        $user_data = $this->request->input('json_decode');
        if (parent::checkIfRequestIsAllowedAdmin(parent::TOGGLE_BLOCK_USER_ACTION, $user_data)
        ) {
            $this->User->toggleBlock($user_data->id);
            $this->sendUserTable();
        }
    }

    /**
     * Delete the user and send the new user table as response
     */
    public function delete()
    {
        $user_data = $this->request->input('json_decode');
        if (parent::checkIfRequestIsAllowedAdmin(parent::DELETE_USER_ACTION, $user_data)
        ) {
            $this->User->deleteUser($user_data->id);
            $this->sendUserTable();
        }
    }

    /**
     * Send the User table to the Client as response
     */
    private function sendUserTable()
    {
        //Resend user table
        $this->autoLayout = false;
        $users = $this->User->find('all');
        $this->set('users', $users);
        $this->render('/Elements/users_table');
    }

    /**Get a list with all the tags and mark with subscribed if the user is subscribed to it
     * @param $userID int|string The Id of the User
     * @return array Tag array with subscribed tags marked
     */
    private function getTagArrayForUser($userID)
    {
        $tagLinks = $this->UserHasTag->find('all', array(
            'conditions' => array('UserHasTag.user_id' => $userID)
        ));
        $tags = $this->Tag->find('all');
        $subscribedTags = array();
        $tagList = array();
        foreach ($tagLinks as $tagLink) {
            $subscribedTags[] = $tagLink['UserHasTag']['tag_id'];
        }
        foreach ($tags as $tag) {
            if (in_array($tag['Tag']['id'], $subscribedTags)) {
                $tag['Tag']['subscribed'] = true;
            } else {
                $tag['Tag']['subscribed'] = false;
            }
            $tagList[] = $tag['Tag'];
        }
        return $tagList;
    }

}