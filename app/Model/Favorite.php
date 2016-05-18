<?php

/**
 * Created by IntelliJ IDEA.
 * User: chris
 * Date: 05.04.16
 * Time: 11:33
 */
class Favorite extends AppModel
{
    /**Favorite a password with a user
     * @param $passwordID int|string Password to be favored
     * @param $userID int|string Userid
     * @return bool Success?
     * @throws Exception
     */
    public function addFavorite($passwordID, $userID)
    {
        if ($this->isFavorite($passwordID, $userID)) {
            return false;
        } else {
            $newEntry = array(
                'password_id' => $passwordID,
                'user_id' => $userID
            );
            $this->create();
            $this->save($newEntry);
            $this->clear();
            return true;
        }
    }

    /**Remove a favorite
     * @param $passwordID int|string Password
     * @param $userID int|string User
     */
    public function deleteFavorite($passwordID, $userID)
    {
        $this->deleteAll(array(
            'Favorite.password_id' => $passwordID,
            'Favorite.user_id' => $userID
        ));
    }

    /**Check if Password is a favorite
     * @param $passwordID int|string PasswordID
     * @param $userID int|string UserID
     * @param $masterArray array|null All favorites (if not null use this and don't query DB)
     * @return bool
     */
    public function isFavorite($passwordID, $userID, $masterArray = null)
    {
        if (isset($masterArray)) {
            foreach ($masterArray as $value) {
                if ($value['Favorite']['password_id'] == $passwordID && $value['Favorite']['user_id'] == $userID) {
                    return true;
                }
            }
            return false;
        } else {
            $counter = $this->find('count', array(
                'conditions' => array(
                    array('Favorite.password_id' => $passwordID),
                    array('Favorite.user_id' => $userID)
                )
            ));
            return $counter > 0;
        }
    }

    public function getMasterArrayForUser($userID)
    {
        return $this->find('all', array(
            'conditions' => array('Favorite.user_id' => $userID)
        ));
    }
}