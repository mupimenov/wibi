<?php

/*
 *      user-action.php
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

class UserAction extends Action {

    static $after_render_user_config_form = array();
    static $after_render_user_home = array();

    /*
     * $f = array ("class", "function");
     */
    public static function add_listener_after_render_user_config_form($f) {
       self::$after_render_user_config_form[] = $f;
    }

    public static function add_listener_after_render_user_home($f) {
        self::$after_render_user_home[] = $f;
    }

    public function UserAction() {
        $this->entity_name = "user";
        $this->available_funcs = array("home", "edit", "onedit", "login", "onlogin", "logout");
        $this->protected_funcs = array("edit" => array("edit", "onedit"));
        session_start();
    }

    public function login() {
        self::render_login_form();
    }

    public function onlogin() {
        // do
        $u = User::get_user_by_login($this->parms["user_login"]);
        if ($u->login) {
            if ($u->password_md5 == md5($this->parms["user_pwd"])) {
                $u->session_id = session_id();
                User::setup_session($u);
                $_SESSION['user_id'] = $u->id;
                $_SESSION['sid'] = $u->session_id;
                Logger::addMsg(_("you logged in"));
            } else {
                Logger::addError(_("password is wrong"));
                self::redirect_to(Utils::path("user", "login"));
                return;
            }
        } else {
            Logger::addError(_("where is no user with such login"));
            self::redirect_to(Utils::path("user", "login"));
            return;
        }
        // out
        self::redirect_to(Utils::path("user", "home", array("id" => $u->id)));
    }

    public function logout() {
        self::session_defaults();
        session_destroy();
        Logger::addMsg(_("you logged out"));
        self::redirect_to(Utils::path("page", "viewlast"));
    }

    public function home(){
        if (isset($this->parms["id"])) {
            $id = Validator::numeric($this->parms["id"]);
        } else {
            Logger::addError(_("where user's id?"));
            PageAction::render_error_page();
            return;
        }
        $u = User::get_present($id);
        if  (is_null($u)) {
            PageAction::render_error_page();
            return;
        }
        self::render_user_home(array("user" => $u));
    }

    public function edit() {
        $id = Validator::numeric($this->parms["id"]);
        $u = User::get_present($id);
        if ($u) {
            self::render_user_config_form(array("user" => $u));
        } else {
            PageAction::render_error_page();
        }
        
    }

    public function onedit() {
        // do
        $id = $this->parms["user_id"];
        $u = User::get_present($id);
        if ($this->parms["user_name"]) {            
            $u->name = $this->parms["user_name"];
            Logger::addMsg(_("user name is changed"));
        }
        if ($this->parms["user_pwd"] || $this->parms["user_rpwd"]) {
            if ($this->parms["user_pwd"] == $this->parms["user_rpwd"]) {                
                $u->password_md5 = md5($this->parms["user_pwd"]);
                Logger::addMsg(_("user passwords is changed"));
            } else {
                Logger::addError(_("passwords is not saved: you made a mistake repeating password"));
            }
        }
        // не очень вариант: сохраняется ведь скопом
        User::save($u);
        // out
        self::redirect_to(Utils::path("user", "edit", array("id" => $u->id)));
    }

    // sessions

    static function session_defaults() {
        $_SESSION['user_id'] = 0;
        $_SESSION['sid'] = '';
    }

    public static function get_user() {
        if (!isset($_SESSION['user_id'])) {
            self::session_defaults();
            return User::guest();
        }
        $uid = $_SESSION['user_id'];
        $sid = $_SESSION['sid'];
        $u = User::get_present($uid);
        if ($u->login && $u->session_id == $sid) {
            return $u;
        } else {
            return User::guest();
        }
    }

    public static function is_allowed($action) {
        $u = self::get_user();
        if ($u->login) {
            $allowed_actions = User::get_actions($u);
            if (in_array($action, $allowed_actions)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    // renders
    public static function render_user_state($parms = null) {
        $u = self::get_user();
        $a = '';
        if ($u->login) {
            $uname = Utils::link($u->name, Utils::path("user", "home", array("id" => $u->id)));
            $a = Utils::link(_("log out"), Utils::path("user", "logout"));
        } else {
            $uname = $u->name;
            $a =  Utils::link(_("log in"), Utils::path("user", "login"));
        }
        echo   '<h6>'._("hello").', '.$uname.'<span id="login">'.$a.'</span></h6>';
    }

    public static function render_login_form($parms = null) {
        $user_action = Utils::path("user", "onlogin");
        include 'tpl/user-login-form.tpl';
    }

    public static function render_user_config_form($parms = null) {
        // data
        $u = $parms["user"];
        $user_id = $u->id;
        $user_action = Utils::path("user", "onedit", array("id" => $u->id));
        $user_name = $u->name;
        // out
        include 'tpl/user-config-form.tpl';
        // listeners
        self::invoke_listeners(self::$after_render_user_config_form, $parms);
    }

    public static function render_user_home($parms = null) {
        // data
        $u = $parms["user"];
        $user_id = $u->id;
        $user_name = $u->name;
        $user_ctl = UserAction::ctl(array(
                                             "edit" => Utils::link(_("config"), Utils::path("user", "edit", array("id" => $u->id))),
                                             ));;
        // out
        include "tpl/user-home.tpl";
        // listeners
        self::invoke_listeners(self::$after_render_user_home, $parms);
    }

    //
    // ctls
    //

    public static function ctl($a) {
        // $a = array("rule"=>Utils::link(...))
        // data
        $r = '';
        foreach ($a as $k => $v) {
            if (self::is_allowed($k)) {
                $r .= '<li>'.$v.'</li>';
            }
        }
        // out
        return $r == '' ? '' : '<ul class="ctl">' . $r . '</ul>';
    }

    public static function funcs_ctl($funcs) {
        // $funcs = array ("func1"=>array("class", "func1")
        //                 "func2"=>array("class", "func2"));
        foreach ($funcs as $k => $v) {
            if (self::is_allowed($k)) {
                call_user_func($v);
            }
        }
    }

}
?>
