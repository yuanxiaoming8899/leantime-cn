<?php

namespace leantime\domain\controllers {

    use leantime\core;
    use leantime\base\controller;
    use leantime\domain\models\auth\roles;
    use leantime\domain\repositories;
    use leantime\domain\services\auth;

    class delUser extends controller
    {

        /**
         * init - initialize private variables
         *
         * @access public
         */
        public function init()
        {

            $this->userRepo = new repositories\users();

        }

        /**
         * run - display template and edit data
         *
         * @access public
         */
        public function run()
        {

            auth::authOrRedirect([roles::$owner, roles::$admin], true);

            //Only Admins
                if (isset($_GET['id']) === true) {

                    $id = (int)($_GET['id']);

                    $user = $this->userRepo->getUser($id);

                    //Delete User
                    if (isset($_POST['del']) === true) {

                        if(isset($_POST[$_SESSION['formTokenName']]) && $_POST[$_SESSION['formTokenName']] == $_SESSION['formTokenValue']) {

                            $this->userRepo->deleteUser($id);

                            $this->tpl->setNotification($this->language->__("notifications.user_deleted"), "success");

                            $this->tpl->redirect(BASE_URL."/users/showAll");

                        }else{
                            $this->tpl->setNotification($this->language->__("notification.form_token_incorrect"), 'error');
                        }

                    }

                    //Sensitive Form, generate form tokens
                    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
                    $_SESSION['formTokenName'] = substr(str_shuffle($permitted_chars), 0, 32);
                    $_SESSION['formTokenValue'] = substr(str_shuffle($permitted_chars), 0, 32);

                    //Assign variables
                    $this->tpl->assign('user', $user);

                    $this->tpl->display('users.delUser');

                } else {

                    $this->tpl->display('general.error');

                }

        }

    }
}
