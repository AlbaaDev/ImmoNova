<?php


namespace App\Form;


class ChangePassword {

    /**
     * @SecurityAssert\UserPassword(
     *     message = "Wrong value for your current password"
     * )
     */
    protected $oldPassword;

    protected $newPassword;

    /**
     * @return mixed
     */
    public function getOldPassword() {
        return $this->oldPassword;
    }

    /**
     * @return mixed
     */
    public function getNewPassword() {
        return $this->newPassword;
    }
}