<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');
App::import('Vendor', 'SecurityHeader');
App::import('Vendor', 'functionOverride');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @property User User
 * @property Tag Tag
 * @package        app.Controller
 * @link        http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    //debug enable DebugKit
    public $components = array("DebugKit.Toolbar", "Flash", "Session");

    //Models to be used
    public $uses = array('Folder', 'User', 'Password', "UsersHasFolder", "Image", "Tag", "UserHasTag", 'Invite');


    public function index()
    {

        //Check if the user is already logged in

        if (isset($_SESSION['user_id'])) {
            //check if user provided key
            if (isset($_GET['key'], $_GET['id'])) {
                return $this->redirect(
                    array('controller' => 'Passmanager', 'action' => "accept?key={$_GET['key']}&id={$_GET['id']}")
                );
            } else {
                return $this->redirect(
                    array('controller' => 'Passmanager', 'action' => 'index')
                );
            }
        }

        //$this->helpers[]='Html';
        //Set the Title:
        $title = "Login :: Passmanager";
        $this->set('title', $title);

        //Check Button press
        if (isset($_POST['login_submit'])) {
            //Filter Input values
            $filteredUsername = filter_input(INPUT_POST, 'login_username', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            $filteredPassword = filter_input(INPUT_POST, 'login_password', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            if (isset($filteredUsername, $filteredPassword)) {
                //Check if user has done the correct login:
                $loginResponse = $this->User->login($filteredUsername, $filteredPassword);
                if ($loginResponse['success']) {
                    //Login was successfull now we create the session here
                    $this->Session->write('user_id', $loginResponse['user_id']);
                    $this->Session->write('User.access_level', $loginResponse['access_level']);
                    $this->Session->write('User.email', $loginResponse['email']);
                    $this->Session->write('User.name', $filteredUsername);

                    //Always Accept all Incites first
                    if (isset($_GET['key'], $_GET['id'])) {
                        //Filter
                        $linkKey = filter_input(INPUT_GET, FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
                        $linkID = filter_input(INPUT_GET, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                        if (isset($linkKey, $linkID)) {
                            App::import('Model', 'Invite');
                            $Invite = new Invite();
                            $Invite->acceptInvite($filteredPassword, $loginResponse['user_id'], $linkKey, $linkID);
                        }
                    }
                    
                    
                    //Finally make the redirect to the Passmanager.
                    return $this->redirect(
                        array('controller' => 'Passmanager', 'action' => 'index')
                    );
                } else {
                    $this->Flash->error($loginResponse['error']);
                }
            } else {
                $this->Flash->error("Please enter a valid username and password");
            }
        }
    }

    /**
     * Check if the User is logged in else redirect to login
     * @param $redirect boolean Make redirect to the loginpage
     * @return boolean Is user logged in?
     */
    protected function checkLogin($redirect = true)
    {
        if (!isset($_SESSION['user_id'])) {
            //The user has no session and so we send him back
            $this->Flash->warning("Please login");
            if ($redirect) {
                return $this->redirect(
                    array('controller' => 'App', 'action' => 'index')
                );
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    /**Check if the logged in user is admin
     * @return bool In the User Admin?
     */
    protected function isActualUserAdmin()
    {
        $userAccessLevel = $this->Session->read('User.access_level');
        return isset($userAccessLevel)
        && (int)$userAccessLevel <= Configure::read('AccessLevel.Administration')
        && (int)$userAccessLevel > 0;
    }

    /**
     * Log the actual user out
     */
    protected function logout()
    {
        $_SESSION['user_id'] = null;
        unset($_SESSION['user_id']);
        session_unset();
        session_destroy();
    }
}
