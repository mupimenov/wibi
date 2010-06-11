<?php
/*
 *      box.php
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

class Box {

    var $renders;

    public function Box() {
        $this->renders = array();
        $args = func_get_args();
        if (count($args) == 0) {
            return;
        }
        foreach ($args as $arg) {
            if (is_array($arg) && count($arg) == 2) {
                $this->renders[] = $arg;
            }
        }
    }

    public function add_renders() {
        $args = func_get_args();
        foreach ($args as $arg) {
            if (is_array($arg) && count($arg) == 2) {
                $this->renders[] = $arg;
            }
        }
    }

    static function inc($a) {
        ob_start();
        call_user_func($a);
        $r = ob_get_contents();
        ob_end_clean();
        return $r;
    }

    public function render_to_variable() {
        $result = "";
        foreach ($this->renders as $r) {
            $result .= self::inc($r);
        }
        return $result;
    }
}

?>
