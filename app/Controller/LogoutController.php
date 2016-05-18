<?php
/**
 * Created by IntelliJ IDEA.
 * User: chris
 * Date: 05.04.16
 * Time: 10:22
 */


App::uses('Controller', 'Controller');


class LogoutController extends AppController
{


    public function beforeFilter(){
        $this->logout();
    }

    public function index(){
        $this->autoRender = false;

        //Go to the login page
        return $this->redirect(
            array('controller' => 'App', 'action' => 'index')
        );
    }
}