<?php
App::uses('AjaxController', 'Controller');

/**
 * Created by IntelliJ IDEA.
 * User: chris
 * Date: 21.04.16
 * Time: 08:30
 * @property Tag Tag
 * @property UserHasTag UserHasTag
 */
class TagsController extends AjaxController
{

    const DELETE_MODE = 'Delete';
    const EDIT_MODE = 'Edit';

    /**
     * Create a new tag
     */
    public function create()
    {
        $userID = $_SESSION['user_id'];
        $_POST['user_id'] = $userID;
        if (parent::checkIfRequestIsAllowed(parent::CREATE_TAG_ACTION,
            parent::transformPostDataToObject('user_id', 'task'))
        ) {
            //Filter input
            $parentTagID = filter_input(INPUT_POST, 'parentTagID', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
            $tagName = filter_input(INPUT_POST, 'tagName', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            if (isset($parentTagID, $tagName)) {
                if ($parentTagID <= 0) $parentTagID = null;
                $this->Tag->createTag($parentTagID, $tagName);
                $this->sendTagTable();
            } else {
                $this->response->statusCode(400);
                $this->response->body('{"message":"Not all required Fields where filled"}');
            }
        }
    }

    /**
     * Get the Show Tag Popup
     */
    public function getShowPopup()
    {
        $user_data = $this->request->input('json_decode');
        if (parent::checkIfRequestIsAllowedAdmin(parent::POPUP_SHOW_TAG_ACTION, $user_data)
        ) {
            $users = $this->UserHasTag->getUsersForTag($user_data->id);
            $tag = $this->Tag->find('first', array(
                'conditions' => array('Tag.id' => $user_data->id)
            ));
            $this->set('tag', $tag);
            $this->set('users', $users);
            $this->render('/Elements/show_tag');
        }
    }

    /**
     * Remove user from Tag
     */
    public function removeUser()
    {
        $user_data = $this->request->input('json_decode');
        if (parent::checkIfRequestIsAllowedAdmin(parent::REMOVE_USER_FROM_TAG_ACTION, $user_data)
        ) {
            $this->UserHasTag->removeUserFromTag($user_data->id, $user_data->tagID);
            $this->sendTagTable();
        }
    }

    /**
     * Get the Tag table
     */
    public function refresh()
    {
        $user_data = $this->request->input('json_decode');
        if (parent::checkIfRequestIsAllowedAdmin(parent::REFRESH_TAG_ACTION, $user_data)
        ) {
            $this->sendTagTable();
        }
    }

    /**
     * Show the delete Popup
     */
    public function deletePopup()
    {
        $user_data = $this->request->input('json_decode');
        if (parent::checkIfRequestIsAllowedAdmin(parent::POPUP_DELETE_TAG_ACTION, $user_data)
        ) {
            $allTags = $this->Tag->find('all');
            $tag = $this->Tag->find('first', array(
                'conditions' => array('Tag.id' => $user_data->id)
            ));
            if (isset($tag['Tag']['parenttag_id'])) {
                $parent = $this->Tag->find('first', array(
                    'conditions' => array('Tag.id' => $tag['Tag']['parenttag_id']
                    )
                ));
            } else {
                $parent = array('Tag' => array(
                    'id' => -1,
                    'name' => '[The Root Tag]'
                ));
            }
            $this->set('mode', self::DELETE_MODE);
            $this->set('tag', $tag);
            $this->set('allTags', $allTags);
            $this->set('parent', $parent);
            $this->render('/Elements/edit_delete_tag');
        }
    }

    /**
     * Delete a tag
     */
    public function delete()
    {
        if (parent::checkIfRequestIsAllowed(parent::DELETE_TAG_ACTION,
            parent::transformPostDataToObject('tag_id', 'task'))
        ) {
            //Filter Input
            $tagID = filter_input(INPUT_POST, 'tag_id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
            $parentTagID = filter_input(INPUT_POST, 'parentTagID', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
            if ($parentTagID <= 0) $parentTagID = null;
            if (isset($tagID)) {
                if ($tagID != Configure::read('RootTag')) {
                    $this->Tag->deleteTag($tagID, $parentTagID);
                    $this->sendTagTable();
                } else {
                    $this->response->statusCode(400);
                    $this->response->body('{"message":"Root Tag can not be removed"}');
                }
            } else {
                $this->response->statusCode(400);
                $this->response->body('{"message":"Not all required Fields where filled"}');
            }
        }
    }

    /**
     * Get the edit Popup
     */
    public function editPopup()
    {
        $user_data = $this->request->input('json_decode');
        if (parent::checkIfRequestIsAllowedAdmin(parent::POPUP_EDIT_TAG_ACTION, $user_data)
        ) {
            $allTags = $this->Tag->find('all');
            $tag = $this->Tag->find('first', array(
                'conditions' => array('Tag.id' => $user_data->id)
            ));
            if (isset($tag['Tag']['parenttag_id'])) {
                $parent = $this->Tag->find('first', array(
                    'conditions' => array('Tag.id' => $tag['Tag']['parenttag_id']
                    )
                ));
            } else {
                $parent = array('Tag' => array(
                    'id' => -1,
                    'name' => '[Root Tag]'
                ));
            }
            $this->set('mode', self::EDIT_MODE);
            $this->set('tag', $tag);
            $this->set('allTags', $allTags);
            $this->set('parent', $parent);
            $this->render('/Elements/edit_delete_tag');
        }
    }

    /**
     * Edit a tag
     */
    public function edit()
    {
        if (parent::checkIfRequestIsAllowed(parent::EDIT_TAG_ACTION,
            parent::transformPostDataToObject('tag_id', 'task'))
        ) {
            //Filter Input
            $tagID = filter_input(INPUT_POST, 'tag_id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
            $tagName = filter_input(INPUT_POST, 'tagname', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            $parentTagID = filter_input(INPUT_POST, 'parentTagID', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
            if ($parentTagID <= 0) $parentTagID = null;
            if (isset($tagID, $tagName)) {
                //App::import('Model','Tag');
                //$Tag = new Tag();
                $this->Tag->editTag($tagID, $tagName, $parentTagID);
                $this->Tag->clear();
                $this->sendTagTable();
            } else {
                $this->response->statusCode(400);
                $this->response->body('{"message":"Not all required Fields where filled"}');
            }
        }
    }

    /**
     * Get the changed Tag dropdown list
     */
    public function refreshDropdown()
    {
        $user_data = $this->request->input('json_decode');
        if (parent::checkIfRequestIsAllowedAdmin(parent::REFRESH_TAG_ACTION, $user_data)
        ) {
            $this->autoLayout = false;
            $tags = $this->Tag->find('all');
            $this->set('allTags', $tags);
            $this->render('/Elements/tag_selector_list');
        }
    }
    
    /**
     * Send the Tag table to the Client as response
     */
    private function sendTagTable()
    {
        //Resend user table
        $this->autoLayout = false;
        $tags = $this->Tag->find('all');
        $tagCount = $this->Tag->getPasswordTagCountArray();
        $this->set('tags', $tags);
        $this->set('countsTag', $tagCount);
        $this->render('/Elements/tags_table');
    }
}