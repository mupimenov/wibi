<?php

/*
 *      logger.php
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

class Logger {

    public static function addMsg($msg) {
        $_SESSION ["msgs"] [] = $msg;
    }

    public static function addError($err) {
        $_SESSION ["errs"] [] = $err;
    }

    public static function render_log($parms = null) {
    // data: errors first
        $r_e = '';
        if (isset($_SESSION ["errs"])) {

            foreach ($_SESSION ["errs"] as $e) {
                $r_e .= '<li>'.$e.'</li>';
            }
        }
        $r_e = $r_e ? '<ul class="errors">'.$r_e.'</ul>' : '';
        // data: then msgs
        $r_m = '';
        if (isset($_SESSION ["msgs"])) {
            foreach ($_SESSION ["msgs"] as $m) {
                $r_m .= '<li>'.$m.'</li>';
            }
        }
        $r_m = $r_m ? '<ul class="messages">'.$r_m.'</ul>' : '';
        // clear session vars
        $_SESSION ["msgs"] = array();
        $_SESSION ["errs"] = array();
        // out
        echo $r_e . $r_m ;
    }
}
?>
