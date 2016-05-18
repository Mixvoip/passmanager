<?php
App::uses('AjaxController', 'Controller');

/**
 * @property Folder Folder
 */
class ShareController extends AjaxController
{
    /**
     * Get the invite popup
     */
    public function getAcceptInvitesPopup()
    {
        $user_data = $this->request->input('json_decode');
        if (parent::checkIfRequestIsAllowed(parent::POPUP_INVITE_ACTION, $user_data)) {
            $folders = $this->Folder->find('all', array(
                    'fields' => array('Folder.id',
                        'Folder.name',
                    ),
                    'conditions' => array('Invite.guest_user_id' => $_SESSION['user_id']),
                    'joins' => array(
                        array(
                            'table' => 'invites',
                            'alias' => 'Invite',
                            'type' => 'INNER',
                            'conditions' => array(
                                'Invite.folder_id = Folder.id'
                            )
                        )
                    ),
                )
            );
            if (count($folders) > 0) {
                $this->set('folders', $folders);
                $this->render('/Elements/acceptInvite');
            } else {
                $this->response->statusCode(404);
            }
        }
    }

    /**
     * Accept a invite
     */
    public function accept()
    {
        if (parent::checkIfRequestIsAllowedAdmin(parent::ACCEPT_INVITE_ACTION,
            parent::transformPostDataToObject('folderID', 'task'))
        ) {
            //Filter input
            $userID = $_SESSION['user_id'];
            $userPassword = filter_input(INPUT_POST, 'user_pass', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            $folderID = filter_input(INPUT_POST, 'folderID', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
            $key = filter_input(INPUT_POST, 'invite_key', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            if (isset($userPassword, $folderID, $key)) {
                if ($this->User->checkPassword($userPassword, $userID)) {
                    App::import('Model', 'Invite');
                    $Invite = new Invite();
                    $success = $Invite->acceptInvite($userPassword, $userID, base64_decode($key), $folderID);
                    if ($success) {
                        $this->response->statusCode(204); // No Content
                        //$this->response->body("Invite accepted");
                    } else {
                        $this->response->statusCode(400);
                        $this->response->body("The Invite is not valid");
                    }
                } else {
                    $this->response->statusCode(400);
                    $this->response->body('{"message":"Your Password is wrong"}');
                }
            } else {
                $$this->response->statusCode(400);
                $this->response->body('{"message":"Not all required Fields where filled"}');
            }
        }
    }

    /**
     * Reject a invite
     */
    public function reject()
    {
        if (parent::checkIfRequestIsAllowedAdmin(parent::ACCEPT_INVITE_ACTION,
            parent::transformPostDataToObject('folderID', 'task'))
        ) {
            //Filter input
            $userID = $_SESSION['user_id'];
            $folderID = filter_input(INPUT_POST, 'folderID', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
            if (isset($folderID)) {
                App::import('Model', 'Invite');
                $Invite = new Invite();
                $Invite->rejectInvite($userID, $folderID);
                $this->response->statusCode(204); // No Content
            } else {
                $$this->response->statusCode(400);
                $this->response->body('{"message":"Not all required Fields where filled"}');
            }
        }
    }
}