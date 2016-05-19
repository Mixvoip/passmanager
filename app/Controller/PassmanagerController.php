<?php
/**
 * User: chris
 * Date: 06.04.16
 * Time: 08:45
 */
App::uses('AppController', 'Controller');

/**
 * @property Password Password
 * @property Folder Folder
 * @property User User
 * @property Tag Tag
 * @property Invite Invite
 * @property Favorite Favorite
 */
class PassmanagerController extends AppController {

    public function beforeFilter(){
        parent::checkLogin();
        //GET msg Callback
        $msg = filter_input(INPUT_GET, 'msg', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
        if (isset($msg, $_GET['msgType'])) {
            $msg = strip_tags($msg);
            switch ($_GET['msgType']) {
                case 'success':
                    $this->Flash->success($msg);
                    break;
                case 'warning':
                    $this->Flash->warning($msg);
                    break;
                case 'error':
                    $this->Flash->error($msg);
                    break;
                default:
                    break;
            }
        }
        //Check Invites timeout
        App::import('Model', 'Invite');
        $Invite = new Invite();
        $Invite->checkInviteTimeOut();
    }

    public function index()
    {
        //Set the Title:
        $title = "Passmanager";
        $this->set('title', $title);
        $this->set('administration', $this->isActualUserAdmin());

        $folderData = $this->Folder->getAllFolderForUser($_SESSION['user_id']);

        $data_injected = $this->Tag->injectTagInformation($folderData, $_SESSION['user_id']);

        $data_all = $data_injected['all'];

        $this->set('data_all', $data_all);
        $this->Folder->clear();

        //Get data for fav list

        $this->set('data_fav', $data_injected['fav']);

        //Check if the user has some Invites open
        $invites = $this->Invite->find('count', array(
            'conditions' => array(
                'guest_user_id' => $_SESSION['user_id']
            )
        ));
        $this->set('invites', $invites);
    }

    public function settings()
    {
        //Get the tags for the user
        $userTags = $this->Tag->getTagsForUser($_SESSION['user_id']);
        $title = "Passmanager::Settings";
        $this->set('title', $title);
        $this->set('administration', $this->isActualUserAdmin());
        $this->set('userMail', $this->Session->read('User.email'));
        $this->set('userTags', $userTags);
    }

    public function administration()
    {
        if ($this->isActualUserAdmin()) {
            $title = "Passmanager::Administration";
            $this->set('title', $title);
            $this->set('administration', $this->isActualUserAdmin());
            $allUsers = $this->User->find('all');
            $allTags = $this->Tag->find('all');
            $this->set('allUsers', $allUsers);
            $this->set('allTags', $allTags);
        } else {
            $this->Flash->warning("You have to be admin");
        }
    }

    public function clearCache()
    {
        if ($this->isActualUserAdmin()) {
            clearCache();
            Cache::clear();
            $this->Flash->success("Cache Cleared");

        } else {
            $this->Flash->warning("You have to be admin to perform this Action");
        }
        return $this->redirect(
            array('controller' => 'Passmanager', 'action' => 'index')
        );
    }
    
    public function accept()
    {
        //Set the Title:
        $title = "Accept Invite :: Passmanager";
        $this->set('title', $title);
        if (isset($_GET['key'], $_GET['id'])) {
            if (isset($_POST['submit'])) {
                //Filter
                $linkKey = filter_input(INPUT_GET, 'key', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
                $linkID = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                $filteredPassword = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
                if ($this->User->checkPassword($_SESSION['user_id'], $filteredPassword)) {
                    if (isset($linkKey, $linkID)) {
                        App::import('Model', 'Invite');
                        $Invite = new Invite();
                        $success = $Invite->acceptInvite($filteredPassword, $_SESSION['user_id'], base64_decode($linkKey), $linkID);
                        if ($success) {
                            $this->Flash->success("Invite accepted");
                        } else {
                            $this->Flash->error("The Invite is not valid");
                        }
                    }
                    return $this->redirect(
                        array('controller' => 'Passmanager', 'action' => 'index')
                    );
                } else {
                    $this->Flash->error("Your Password is wrong");
                }
            }
        } else {
            $this->Flash->warning("The Invite url is not valid");
            return $this->redirect(
                array('controller' => 'Passmanager', 'action' => 'index')
            );
        }
    }
}
