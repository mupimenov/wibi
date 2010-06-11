<?php
/*
 *      validator.php
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

class Validator {

    public static function numeric($txt) {
        $r = 0;
        if (isset($txt)) {            
            if (is_numeric($txt)) {
                if ((int)$txt == $txt) {
                    $r += $txt;
                } else {
                    $r += 0;
                    Logger::addError(_("parameters you typed are not integer"));
                }
            }
            else {
                $r += 0;
                Logger::addError(_("parameters you typed are not numeric"));
            }
        } else {
            $r += 0;
        }
        return $r;
    }

    public static function nonhtml($txt) {
        $r = "";
        if (isset($txt)) {
            if (!empty($txt) && is_string($txt)) {
                $r .= htmlspecialchars(stripslashes($txt));
            } else {
                $r .= "";
            }
        } else {
            $r .= "";
        }
        return $r;
    }
}

?>
