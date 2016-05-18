<?php

/**
 * Created by IntelliJ IDEA.
 * User: chris
 * Date: 07.04.16
 * Time: 11:13
 * Controller to manager the Ajax requests
 * No index function for auto redirect
 */

App::uses('AppController', 'Controller');

class AjaxController extends AppController
{

    //Actions
    //Password Actions
    const FAVORITE_PASSWORD_ACTION = 'favoritePassword';
    const ADD_PASSWORD_ACTION = 'addPassword';
    const POPUP_EDIT_PASSWORD_ACTION = 'getPasswordPopup';
    const EDIT_PASSWORD_ACTION = 'editPassword';
    const DELETE_PASSWORD_ACTION = 'deletePassword';
    const SHOW_PASSWORD_ACTION = 'showPassword';

    //Folder actions
    const POPUP_EDIT_FOLDER_ACTION = 'getFolderPopup';
    const EDIT_FOLDER_ACTION = 'editFolder';
    const DELETE_FOLDER_ACTION = 'deleteFolder';
    const POPUP_SHARE_FOLDER_ACTION = 'getFolderSharePopup';
    const SHARE_FOLDER_ACTION = 'shareFolder';

    //User Actions
    const EDIT_PASSWORD_USER_ACTION = 'userEditPassword';
    const CREATE_USER_ACTION = 'createUser';
    const EDIT_USER_ACTION = 'editUser';
    const POPUP_EDIT_USER_ACTION = 'getEditUserPopup';
    const TOGGLE_BLOCK_USER_ACTION = 'toggleBlock';
    const DELETE_USER_ACTION = 'deleteUser';

    //Tag Actions
    const CREATE_TAG_ACTION = 'createTag';
    const POPUP_SHOW_TAG_ACTION = 'getShowTagAction';
    const REMOVE_USER_FROM_TAG_ACTION = 'removeUserFromTag';
    const REFRESH_TAG_ACTION = 'refreshTags';
    const POPUP_DELETE_TAG_ACTION = 'getDeletePopup';
    const POPUP_EDIT_TAG_ACTION = 'getEditPopup';
    const DELETE_TAG_ACTION = 'DeleteTag';
    const EDIT_TAG_ACTION = 'EditTag';

    //Invite Actions
    const POPUP_INVITE_ACTION = 'popupInvite';
    const ACCEPT_INVITE_ACTION = 'acceptInvite';

    private $loggedIN = false;

    public function beforeFilter()
    {
        $this->loggedIN = parent::checkLogin(false);
        $this->autoRender = false;
        $this->autoLayout = false;
        $this->layout = null;
    }

    /** Check if the User has the Right to perform the given action
     * @param $action String Task that the User try's to execute
     * @param $user_data Object Data received from client
     * @return bool The user has the right to perform the action
     */
    protected function checkIfRequestIsAllowed($action, $user_data)
    {
        if ($this->loggedIN) {
            if (isset($user_data, $user_data->task) && $user_data->task === $action) {
                if (isset($user_data->id)) {
                    //Check via session if the user is still logged in and if he can edit that record
                    if (isset($_SESSION['user_id'])) {
                        return true; //You are the chosen one
                    } else {
                        //Send wrong Authentication
                        $this->response->statusCode(401);
                        $this->response->body('{"message":"Wrong Authentication. Please use session"}');
                    }
                } else {
                    //Send missing id
                    $this->response->statusCode(400);
                    $this->response->body('{"message":"Missing ID"}');
                }
                $this->response->body();
            } else {
                //send wrong request
                $this->response->statusCode(400);
                $this->response->body('{"message":"Invalid Task"}');
            }
        } else {
            $this->response->statusCode(401);
            $this->response->body('{"message":"Please Login"}');
        }
        return false; //You are not valid
    }

    /**Check if the user can edit the record
     * @param $recordOwner int record owner
     * @return bool He can or not
     */
    protected function doesTheUserCanEditTheRecord($recordOwner)
    {
        if ($recordOwner == $_SESSION['user_id']) {
            return true; //You are the chosen one
        } else {
            //Send no permission
            $this->response->statusCode(403);
            $this->response->body('{"message":"You are not Authorized to do this"}');
        }
        return false;
    }

    /**
     * Transforms post data to object to use it with checkIfRequestIsAllowed
     * @param $idFieldName string name of the id field
     * @param $taskFieldName string Name of the task field
     * @return object Transformed $_post
     */
    protected function transformPostDataToObject($idFieldName, $taskFieldName)
    {
        if (isset($_POST[$idFieldName], $_POST[$taskFieldName])) {
            $obj = new stdClass();
            $obj->id = $_POST[$idFieldName];
            $obj->task = $_POST[$taskFieldName];
            return $obj;
        } else {
            return null;
        }
    }

    /**Check if user is allowed and admin to perform an action
     * @param $action string Action to be performed
     * @param $user_data Object Data from User
     * @return bool Is the user ok?
     */
    protected function checkIfRequestIsAllowedAdmin($action, $user_data)
    {
        if ($this->checkIfRequestIsAllowed($action, $user_data)) {
            if ($this->isActualUserAdmin()) {
                return true;
            } else {
                $this->response->statusCode(403);
                $this->response->body('{"message":"You are not Authorized to do this"}');
            }
        } else {
            return false;
        }
    }
}