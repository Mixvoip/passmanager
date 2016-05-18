<?php
//debug Debug controller
/**
 * Created by IntelliJ IDEA.
 * User: chris
 * Date: 05.04.16
 * Time: 10:22
 * What you’re referring to as debug controller, is in fact, debug/test controller,
 * or as I’ve recently taken to calling it, debug plus test controller.
 */


App::uses('AppController', 'Controller');


/**
 * @property User User
 * @property Image Image
 * @property Password Password
 * @property Tag Tag
 * @property UserHasTag UserHasTag
 * @property Folder Folder
 */
class DebugController extends AppController
{

    public function beforeFilter()
    {
        if (Configure::read('debug') <= 0) {
            return $this->redirect(
                array('controller' => 'App', 'action' => 'index')
            );
        }
    }

    public function index()
    {

    }

    public function dumpPost()
    {
        $this->autoRender = false;
        var_dump($_POST);
    }

    public function cls()
    {
        clearCache();
        Cache::clear();
        $this->redirect($this->referer());
    }


    public function dump()
    {
        $this->set('data1', isUrlValid('bla://tes'));
        $this->set('data2', isUrlValid('http://'));
    }


    public function testInstall()
    {

    }

    public function crypto()
    {

        App::uses('CryptoWrapper', 'Vendor');
        $crypto = new CryptoWrapper();
        $crypto->setKey("1234");
        $res = $crypto->encrypt("hallo welt");
        var_dump($res);
        var_dump($crypto->decrypt($res));
        echo '<h1>Random test</h1>';
        var_dump($crypto->genPassword());
    }

    public function encrypt()
    {
        $this->autoRender = false;
        if (isset($_GET['key'], $_GET['text'])) {
            App::uses('CryptoWrapper', 'Vendor');
            $crypto = new CryptoWrapper();
            $crypto->setKey($_GET['key']);
            echo 'Key used: <code>' . $_GET['key'] . '</code><br>';
            echo 'Text used: <code>' . $_GET['text'] . '</code><br>';
            echo 'Encrypted message:';
            echo '<p><code><pre>' . $crypto->encrypt($_GET['text']) . '</pre></code></p>';
        } else {
            echo 'please use ?key=key&text=text as get params';
        }
    }

    public function echoPost()
    {
        var_dump($_POST);
        $this->autoRender = false;
    }

    public function decrypt()
    {
        $this->autoRender = false;
        if (isset($_GET['key'], $_GET['text'])) {
            App::uses('CryptoWrapper', 'Vendor');
            $crypto = new CryptoWrapper();
            $crypto->setKey($_GET['key']);
            echo 'Key used: <code>' . $_GET['key'] . '</code><br>';
            echo 'Text used: <code>' . $_GET['text'] . '</code><br>';
            echo 'Decrypted message:';
            echo '<p><code>' . $crypto->decrypt($_GET['text']) . '</code></p>';
        } else {
            echo 'please use ?key=key&text=text as get params';
        }
    }

    //Not needed any more
    /*public function insert(){
        $this->User->createUser('admin', '1234', 'admin@mixvoip.com', false, 1);
        $this->User->createUser('user1', '1234', 'user1@mixvoip.com', false, 2);
        $this->User->createUser('user2', '1234', 'user2@mixvoip.com', false, 2);

        $this->Image->createImage(1, 'img/mixvoip.png', 'Demo Image', true);
        $this->Image->createImage(1, 'img/doge.png', 'Demo Image for admin only', false);

        $this->Flash->warning("User created");
        return $this->redirect(
            array('controller' => 'Debug', 'action' => 'index')
        );
    }

    public function testShare()
    {
        $this->autoRender = false;
        echo 'Sharing folder 1 with user 2';
        $folder = $this->Folder->find('first', array(
            'conditions' => array('Folder.id' => 1)
        ));

        App::uses('CryptoWrapper', 'Vendor');
        $crypto_user = new CryptoWrapper();
        $crypto_user->setKey('1234');

        App::import('Model', 'UsersHasFolder');
        $UsersHasFolder = new UsersHasFolder();
        $folderLink = $UsersHasFolder->find('first', array(
            'conditions' => array(
                array('UsersHasFolder.user_id' => 1),
                'AND' => array('UsersHasFolder.folder_id' => 1)
            )
        ));

        $shared_key = $crypto_user->decrypt($folderLink['UsersHasFolder']['shared_key_for_User']);

        $crypto_other_user = new CryptoWrapper();
        $crypto_other_user->setKey('1234');

        $key_for_other_user = $crypto_other_user->encrypt($shared_key);

        $linkEntry = array(
            'id' => null,
            'user_id' => 2,
            'folder_id' => 1,
            'shared_key_for_User' => $key_for_other_user
        );

        echo $UsersHasFolder->connectUserFolder($linkEntry);

        var_dump($UsersHasFolder->find('all'));

    }
    */

    public function testA()
    {
        $this->autoRender = false;
        $a = $this->getTagArrayForPassword(1, 9);
        var_dump($a);
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