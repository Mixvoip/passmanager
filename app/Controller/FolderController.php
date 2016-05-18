<?php

/**
 * Created by IntelliJ IDEA.
 * User: chris
 * Date: 07.04.16
 * Time: 11:13
 * Controller to manager the Ajax requests with folders
 */

App::uses('AjaxController', 'Controller');

/**
 * @property Folder Folder
 * @property Image Image
 * @property Invite Invite
 */
class FolderController extends AjaxController
{
    /** Create a folder
     * @return \Cake\Network\Response|null Redirect
     */
    public function create()
    {
        $this->autoRender = false;
        $this->autoLayout = false;
        $this->layout = null;
        //$this->response->type('text');

        //filter input
        $folderName = filter_input(INPUT_POST, 'folderName', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
        $userPassword = filter_input(INPUT_POST, 'userPassword', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
        $folderDescription = filter_input(INPUT_POST, 'folderDescription', FILTER_SANITIZE_STRING);
        $privateFolder = isset($_POST['privateFolder']);
        $shareWithAll = isset($_POST['shareWithAll']);
        $userID = $_SESSION['user_id'];
        if (isset($folderName, $userPassword)) {
            //Check if he has entered the right password
            if ($this->User->checkPassword($userPassword, $userID)) {
                $linkEntry = $this->Folder->createFolder($userID, $folderName, $folderDescription, $privateFolder, $userPassword, $shareWithAll);
                $share = (!$privateFolder) && $shareWithAll;
                if ($share) {
                    $linkKey = $linkEntry['linkKey'];
                    unset($linkEntry['linkKey']);
                    //set up link
                    $link = base64_encode($linkKey);
                } else {
                    $link = '';
                }

                //Create fully new Instance of UsersHasFolder
                App::import('Model', 'UsersHasFolder');
                $UsersHasFolder = new UsersHasFolder();
                $UsersHasFolder->connectUserFolder($linkEntry);
                
                $this->response->statusCode(200);
                $this->response->body('{"message":"Your Folder has been created","shared":"' . $share . '","key":"' . $link . '","folderID":"' . $linkEntry['folder_id'] . " [$folderName]" . '"}');
            } else {
                $this->response->statusCode(400);
                $this->response->body('{"message":"You entered the wrong password"}');
            }
        } else {
            $this->response->statusCode(400);
            $this->response->body('{"message":"No Folder or Password was given"}');
        }
    }

    /**
     * Get the Popup with the Values for the Folder edit
     */
    public function getEditPopup()
    {
        $user_data = $this->request->input('json_decode');
        if ($this->checkIfRequestIsAllowed(AjaxController::POPUP_EDIT_FOLDER_ACTION, $user_data)) {
            $folder = $this->Folder->find('first', array(
                'conditions' => array('Folder.id' => $user_data->id)
            ));
            $images = $this->Image->find('all', array(
                'conditions' => array("Image.user_id = {$_SESSION['user_id']} OR Image.public = 1") //hack
            ));
            if (count($images) == 0) $images = null;
            $this->set('entry', $folder['Folder']);
            $this->set('images', $images);
            $this->render('/Elements/editFolderPopup');
        }
    }

    /**
     * Get the Popup for the Folder share
     */
    public function getSharePopup()
    {

        $user_data = $this->request->input('json_decode');
        if ($this->checkIfRequestIsAllowed(AjaxController::POPUP_SHARE_FOLDER_ACTION, $user_data)) {
            $folder = $this->Folder->find('first', array(
                'conditions' => array('Folder.id' => $user_data->id)
            ));
            if (isset($folder['Folder']['shared']) && $folder['Folder']['shared'] == 1) {
                //Get all user who are not already shared with the folder
                //Todo outsource logic to USER getNotSharedUsers(folderID)
                $users = $this->User->getNotSharedUsers($folder['Folder']['id']);
                $this->set('entry', $folder['Folder']);
                $this->set('users', $users);
                $this->render('/Elements/shareFolderPopup');
            } else {
                $this->response->statusCode(400);
                $this->response->body('{"message":"This Folder can\'t be shared"}');
            }
        }
    }

    /**
     * Share a Folder
     */
    public function share()
    {
        if (parent::checkIfRequestIsAllowed(parent::SHARE_FOLDER_ACTION,
            parent::transformPostDataToObject('folderID', 'task'))
        ) {
            //Filter input
            $folderID = filter_input(INPUT_POST, 'folderID', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
            $password = filter_input(INPUT_POST, 'userPassword', FILTER_SANITIZE_STRING, FILTER_NULL_ON_FAILURE);
            $userID = isset($_POST['userID[]']) ? $_POST['userID[]'] : null;
            if (isset($userID, $folderID, $password)) {
                if ($this->User->checkPassword($password, $_SESSION['user_id'])) {
                    App::import('Model', 'Invite');
                    $Invite = new Invite();
                    $key = $Invite->inviteUsersToFolder($userID, $folderID, $password);
                    if (isset($key)) {
                        $this->response->statusCode(200);
                        $this->response->body('{"message":"Your Folder has been created","shared":"' . 1 . '","key":"' . $key . '","folderID":"' . $folderID . '"}');
                    } else {
                        $this->response->statusCode(400);
                        $this->response->body('{"message":"Your Password is wrong"}');
                    }
                } else {
                    $this->response->statusCode(400);
                    $this->response->body('{"message":"Your Password is wrong"}');
                }
            } else {
                $this->response->statusCode(400);
                $this->response->body('{"message":"Error during transmission"}');
            }
        }
    }

    /**Edit a Folder
     * @return \Cake\Network\Response|null
     */
    public function edit()
    {
        if (parent::checkIfRequestIsAllowed(parent::EDIT_FOLDER_ACTION,
            parent::transformPostDataToObject('folderID', 'task'))
        ) {
            //filter input:
            $folderID = filter_input(INPUT_POST, 'folderID', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
            $name = filter_input(INPUT_POST, 'folderName', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            $imageID = filter_input(INPUT_POST, 'imageID', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
            $description = filter_input(INPUT_POST, 'folderDescription', FILTER_SANITIZE_STRING);
            if (isset($folderID, $name)) {
                if ($this->Folder->userCanEditFolder($_SESSION['user_id'], $folderID)) {
                    //todo when image is implemented remove || true
                    if ($imageID < 0 || true) $imageID = null;
                    $this->Folder->editFolder($folderID, $imageID, $name, $description);
                    $this->Flash->success('Folder Updated');
                } else {
                    $this->Flash->error('You can not edit this Folder');
                }
            } else {
                $this->Flash->error('No Name was given');
            }
        }
        return $this->redirect(
            array('controller' => 'Passmanager', 'action' => 'index')
        );
    }

    /**
     * Delete a folder
     */
    public function delete()
    {
        $this->autoRender = false;
        $this->response->type('json');

        $user_data = $this->request->input('json_decode');
        if (parent::checkIfRequestIsAllowed(parent::DELETE_FOLDER_ACTION, $user_data)) {
            $this->response->statusCode(200);
            $this->Folder->deleteFolder($user_data->id, $_SESSION['user_id']);
            /*$folder = $this->Folder->find('first', array(
                'conditions' => array('Folder.id' => $user_data->id)
            ));
            if ($this->Folder->userCanEditFolder($_SESSION['user_id'], $user_data->id)) {
                if (count($folder['Password']) == 0) {
                    $this->UsersHasFolder->deleteAll(
                        array('UsersHasFolder.folder_id' => $user_data->id),
                        false);
                    $this->Folder->delete($user_data->id, false);
                } else {
                    $this->Flash->error("Folder not Empty");
                }
            }*/
        } else {
            $this->Flash->error("You can not Delete this Folder");
        }
    }

}