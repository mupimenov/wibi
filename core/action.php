<?php

/*
 *      action.php
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

class Action {    
    var $failed = true;
    var $func = "";
    var $available_funcs = array("view", "edit", "and_so_on");
    var $protected_funcs = array(   "edit" => array("edit", "onedit"),
                                    "create" => array("create", "oncreate") );

    var $entity_name = "none";
    var $parms = array();

    function Action() {

    }

    function check() {
        if (isset ($_REQUEST["entity"]) and ($_REQUEST["entity"] == $this->entity_name)) {
            if (isset ($_REQUEST["action"])) {
                $this->failed = false;
                $this->func = $_REQUEST["action"];
                foreach ($_REQUEST as $p_key => $p_value) {
                    if ($p_key != "action" && $p_key != "entity") {
                        $this->parms[$p_key] = $p_value;
                    }
                }
            }
        }

        return;
    }

    function do_it() {
        /*
         * TO-DO проверку на имя функций
         */
        $func = $this->func;
        if (in_array($func, $this->available_funcs)) {
            foreach ($this->protected_funcs as $rule => $funcs) {
                if (in_array($func, $funcs)) {
                    if (UserAction::is_allowed($rule)) { // разрешено правило для функции
                        // выпонить функцию
                        $this->$func();
                        return;
                    } else {
                        // иначе выдать предложение залогиниться
                        call_user_func(array("UserAction", "render_login_form"));
                        return;
                    }
                }
            }
            $this->$func();
            return;
        } else {
            // иначе такого действия не существует
            // редиректить на главную
            call_user_func(array("PageAction", "render_error_page"));
            return;
        }
    }

    public function do_default() {
        return;
    }

    function is_failed() {
        return $this->failed;
    }

    function redirect_to($r) {
        header ( 'Location: ' . $r, false ) ;
        exit;
    }

    public static function listen() {
        return;
    }

    static function invoke_listeners($ls, $parms) {
        foreach ($ls as $f) {
           call_user_func($f, $parms);
        }
    }   
    
}
?>
