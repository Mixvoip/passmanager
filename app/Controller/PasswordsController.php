<?php

/**
 * Created by IntelliJ IDEA.
 * User: chris
 * Date: 12.04.16
 * Time: 09:28
 * Controller used to manage the Passwords
 */
App::uses('AjaxController', 'Controller');

/**
 * @property Password Password
 * @property Tag Tag
 * @property PasswordHasTag PasswordHasTag
 * @property Favorite Favorite
 */
class PasswordsController extends AjaxController
{
    /**
     * Get the add popup for the Passwords
     */
    public function getAddPopup()
    {
        $this->autoLayout = false;
        $tags = $this->Tag->getTagsForUser($_SESSION['user_id']);
        $this->set('userTagList', $this->getTagArrayForPassword($_SESSION['user_id'], true));
        $this->set('tags', $tags);
        $this->set('defaultTag', $tags[0]);
        $this->render('/Elements/addPasswordPopup');
    }

    /**Handle Add request
     * @return \Cake\Network\Response|null Redirect
     */
    public function add()
    {
        if (parent::checkIfRequestIsAllowed(parent::ADD_PASSWORD_ACTION,
            parent::transformPostDataToObject('folderID', 'task'))
        ) {
            //Filter Input:
            $folderID = filter_input(INPUT_POST, 'folderID', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
            $userPassword = filter_input(INPUT_POST, 'userPassword', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            $name = filter_input(INPUT_POST, 'passwordName', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            $password = filter_input(INPUT_POST, 'passwordUserpass', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            $username = filter_input(INPUT_POST, 'passwordUsername', FILTER_SANITIZE_STRING);
            $description = filter_input(INPUT_POST, 'passwordDescription', FILTER_SANITIZE_STRING);
            $siteUrl = filter_input(INPUT_POST, 'passwordSiteURL', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            $userID = $_SESSION['user_id'];
            $tags = (isset($_POST['tags']) ? $_POST['tags'] : array());
            if ($this->User->checkPassword($userPassword, $userID) && isset($folderID, $name, $password) && count($tags) > 0) {
                $id = $this->Password->createPassword($userID, $userPassword, $folderID, $username, $password, $name,
                    $description, $siteUrl);
                App::import('Model', 'PasswordHasTag');
                $PasswordHasTag = new PasswordHasTag();
                $PasswordHasTag->addLink($id, $tags);
                //Not used at the moment. Gives back the content of the modified folder
                /*$newPasswords = $this->Password->find('all',array(
                   'conditions'=>array(
                       array('Password.folder_id'=>$folderID)
                   )
               ));
               $newPw_render = array();
               foreach($newPasswords as $val){
                   $newPw_render[] = $val['Password'];
               }
               $this->set('entrys',$newPw_render);
               $this->render('/Elements/password_entry');
               */
            } else {
                $this->response->statusCode(400);
                //$this->response->type('text');
                $this->response->body('{"message":"Your Password was Incorrect"}');
            }
        }
    }

    /**
     * Get the Popup with the Values for the Password edit
     */
    public function getEditPopup()
    {
        $user_data = $this->request->input('json_decode');
        if ($this->checkIfRequestIsAllowed(AjaxController::POPUP_EDIT_PASSWORD_ACTION, $user_data)) {
            $pw = $this->Password->find('first', array(
                'conditions' => array('Password.id' => $user_data->id)
            ));
            $tags = $this->Tag->getTagsForUser($_SESSION['user_id']);
            $selectedTags = $this->getTagArrayForPassword($_SESSION['user_id'], $user_data->id);
            $this->set('userTagList', $selectedTags);
            $this->set('entry', $pw['Password']);
            $this->set('tags', $tags);
            $this->render('/Elements/editPasswordPopup');
        }
    }

    /**Perform the Edit of a password
     * @return \Cake\Network\Response|null Redirect
     */
    public function edit()
    {
        if (parent::checkIfRequestIsAllowed(parent::EDIT_PASSWORD_ACTION,
            parent::transformPostDataToObject('passwordID', 'task'))
        ) {
            //filter input:
            $folderID = filter_input(INPUT_POST, 'folderID', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
            $passwordID = filter_input(INPUT_POST, 'passwordID', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
            $userPassword = filter_input(INPUT_POST, 'userPassword', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            $name = filter_input(INPUT_POST, 'passwordName', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            $password = filter_input(INPUT_POST, 'passwordUserpass', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            $username = filter_input(INPUT_POST, 'passwordUsername', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            $description = filter_input(INPUT_POST, 'passwordDescription', FILTER_SANITIZE_STRING);
            $siteUrl = filter_input(INPUT_POST, 'passwordSiteURL', FILTER_SANITIZE_STRING);
            $tags = (isset($_POST['tags']) ? $_POST['tags'] : array());
            if (isset($folderID, $passwordID, $name) && count($tags) > 0) {
                if ($this->Password->canUserEditPassword($_SESSION['user_id'], $passwordID)) {
                    $result = $this->Password->editPassword($_SESSION['user_id'], $userPassword,
                        $passwordID, $folderID, $username, $password,
                        $name, $description, $siteUrl);
                    App::import('Model', 'PasswordHasTag');
                    $PasswordHasTag = new PasswordHasTag();
                    $PasswordHasTag->updateTags($passwordID, $tags);
                    $msg = 'Password Information';
                    if ($result['Username']) {
                        $msg .= ' + Username';
                    }
                    if ($result['Password']) {
                        $msg .= ' + Password';
                    }
                    $msg .= ' Updated';
                    $this->Flash->success($msg);
                } else {
                    $this->Flash->error('You are not supposed to edit this Password');
                }
            } else {
                $this->Flash->error('No Name or Tags where given');
            }
        }
        return $this->redirect(
            array('controller' => 'Passmanager', 'action' => 'index')
        );
    }

    /**
     * Delete a Password
     */
    public function delete()
    {
        $this->autoRender = false;
        $this->response->type('json');

        $user_data = $this->request->input('json_decode');
        if (parent::checkIfRequestIsAllowed(parent::DELETE_PASSWORD_ACTION, $user_data)) {
            $this->Password->deletePassword($user_data->id);
        }
    }

    /**
     * Decode Password
     */
    public function show()
    {
        if (parent::checkIfRequestIsAllowed(parent::SHOW_PASSWORD_ACTION,
            parent::transformPostDataToObject('passwordID', 'task'))
        ) {
            //Filter Input:
            $passwordID = filter_input(INPUT_POST, 'passwordID', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
            $userPassword = filter_input(INPUT_POST, 'yourPassword', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            $userID = $_SESSION['user_id'];

            if ($this->User->checkPassword($userPassword, $userID) && isset($passwordID, $userPassword)) {
                $ret = $this->Password->showPassword($userID, $userPassword, $passwordID);
                $ret['error'] = 0;
                $this->response->type('json');
                $this->response->body(json_encode($ret));
            } else {
                $this->response->type('json');
                $this->response->body(json_encode(array('error' => 1, 'msg' => 'Wrong Password')));
            }
        }
    }

    /**
     * Favorite the Password
     */
    public function favorite()
    {
        //$this->autoRender = false;
        $this->response->type('json');

        $user_data = $this->request->input('json_decode');
        if ($this->checkIfRequestIsAllowed(AjaxController::FAVORITE_PASSWORD_ACTION, $user_data)) {
            $pw = $this->Password->find('first', array(
                'conditions' => array('Password.id' => $user_data->id)
            ));

            //if ($this->doesTheUserCanEditTheRecord($pw['Password']['user_id'])) { //Not needed anymore
            $newFav = $this->Password->toggleFavorite($pw['Password']['id'], $_SESSION['user_id']);
            $this->response->statusCode(200);
            $this->response->body("{\"message\":\"Favorite Toggled\",\"code\":200,\"newFav\":\"$newFav\"}");
            //}
        }
    }

    /**Get a list with all the tags and mark with subscribed if the password is subscribed to it
     * @param $userID int|string The Id of the User
     * @param $passwordID int|string The id of the Password
     * @return array Tag array with subscribed tags marked
     */
    private function getTagArrayForPassword($userID, $passwordID)
    {
        App::import('Model', 'PasswordHasTag');
        $PasswordHasTag = new PasswordHasTag();
        $tagLinks = $PasswordHasTag->find('all', array(
            'conditions' => array('PasswordHasTag.password_id' => $passwordID)
        ));
        $tags = $this->Tag->getTagsForUser($userID);
        $subscribedTags = array();
        $tagList = array();
        foreach ($tagLinks as $tagLink) {
            $subscribedTags[] = $tagLink['PasswordHasTag']['tag_id'];
        }
        foreach ($tags as &$tag) {
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