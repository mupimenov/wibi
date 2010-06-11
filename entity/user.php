<?php

/*
 *      user.php
 *
 *      Copyright 2010 Mikhail Pimenov <mupimenov@gmail.com>
 *
 *      This program is free software; you can redistribute it and/or modify
 *      it under the terms of the GNU General Public License as published by
 *      the Free Software Foundation; either version 2 of the License, or
 *      (at your option) any later version.
 *
 *      This program is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU General Public License for more details.
 *
 *      You should have received a copy of the GNU General Public License
 *      along with this program; if not, write to the Free Software
 *      Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 *      MA 02110-1301, USA.
 */

class User extends Entity {

    var $id;
    var $login;
    var $password_md5;
    var $name;
    var $level;
    var $session_id;

    static $roles = array("create","remove","edit");

    public function User($login, $password_md5, $name, $level, $session_id, $id = null) {
        $this->login = $login;
        $this->password_md5 = $password_md5;
        $this->name = $name;
        $this->level = $level;
        $this->session_id = $session_id;

        $this->id = $id;
    }

    public static function guest() {
        return new User(null,null,"Guest",null,null,null);
    }

    public static function get_user_by_login($login) {
        $r = DB::sqlRow("SELECT * FROM users WHERE login='$login';");
        if ($r) {
            return new User($r["login"], $r["pwd"], $r["name"], $r["lvl"], $r["sid"], $r["id"]);
        } else {
            return self::guest();
        }
    }

    public static function get_present($id) {
        $r = DB::sqlRow("SELECT * FROM `users` WHERE `id`=$id;");
        if ($r) {
            return new User($r["login"], $r["pwd"], $r["name"], $r["lvl"], $r["sid"], $r["id"]);
        } else {
            return self::guest();
        }
        
    }

    public static function save($u) {
        if ($u->login) {
            DB::exec("UPDATE `users` SET `name`='$u->name', `pwd`='$u->password_md5' WHERE `id`=$u->id;");
            return $u;
        }
    }

    public static function setup_session($u) {
        if ($u->login) {
            DB::exec("UPDATE `users` SET `sid`='$u->session_id' WHERE `id`=$u->id;");
            return $u;
        }
    }

    public static function get_actions($u) {
        $r = array();
        switch ($u->level) {
            case 1:
                $r[] = self::$roles[0];
                break;
            case 3:
                $r[] = self::$roles[0];
                $r[] = self::$roles[1];
                break;
            case 7:
                $r = self::$roles;
                break;
            case 8:
                $r = self::$roles;
                break;
            default:
                break;
        }
        return $r;

    }

    static function install($config=null) {
        $qry =
        "CREATE TABLE `users` (
            `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `login` VARCHAR(50) NOT NULL default '',
            `pwd` VARCHAR(50) NOT NULL default '',
            `name` VARCHAR(50) NOT NULL default '',
            `lvl` INT(11) NOT NULL default 0,
            `sid` CHAR(32) binary NOT NULL default '') TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
        $result = DB::exec($qry) or (mysql_error());

        if ($config["update"])
        {
            return true;
        }

        $pwd = md5($config["admin_pwd"]);
        DB::exec("INSERT INTO users (login, pwd, name, lvl) VALUES ('admin', '$pwd', 'Admin', 8);");

        return true;
    }
}
?>
