<?php
/**
 * Created by PhpStorm.
 * User: Lucas
 * Date: 02.04.2018
 * Time: 23:39
 */

class user
{
    private common $common;

    public function __construct(common $common)
    {
        $this->common = $common;
        if(!array_key_exists('pwd',$_SESSION) ||
            !array_key_exists('id',$_SESSION)) {
            $this->logout();
        }
    }

    public function register(string $username, string $password): bool {
        $query = $this->common->database->query("SELECT `id` FROM `users` WHERE `username` = ?;",utf8_encode($username));
        if(!$query->getRowCount()) {
            $password = password_hash($password, PASSWORD_DEFAULT);
            $this->common->database->query("INSERT INTO `users` SET `username` = ?, `password` = ?;",utf8_encode($username),gzcompress($password));
            return true;
        }

        return false;
    }

    public function login(string $username, string $password): bool {
        $query = $this->common->database->query("SELECT `id`,`password` FROM `users` WHERE `username` = ?;",
            utf8_encode($username));

        if($query->getRowCount()) {
            $rows = $query->fetchAll();
            if(password_verify($password, gzuncompress($rows[0]->password))) {
                $_SESSION['pwd'] = gzuncompress($rows[0]->password);
                $_SESSION['id'] = intval($rows[0]->id);
                return true;
            }
        }

        $this->logout();
        return false;
    }

    public function is_logged(): bool {
        if(array_key_exists('pwd',$_SESSION) && array_key_exists('id',$_SESSION)) {
            if(!empty($_SESSION['pwd']) && $_SESSION['id'] >= 1) {
                $query = $this->common->database->query("SELECT `password` FROM `users` WHERE `id` = ?;",$_SESSION['id']);
                if($query->getRowCount()) {
                    $rows = $query->fetchAll();
                    if($_SESSION['pwd'] == gzuncompress($rows[0]->password)) {
                        return true;
                    }
                }
            }
        }

        $this->logout();
        return false;
    }

    public function logout(): bool {
        $_SESSION['pwd'] = '';
        $_SESSION['id'] = 0;
        return true;
    }
}