<?php

/*
 *      utils.php
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

class Utils {

    static $title;

    public static function path($e, $a, $args = array()) {        
        if ( defined('WB_HRU') && WB_HRU ) {
            $vals = array();
            foreach ($args as $k => $v) {
                $vals[] = $v;
            }
            $format = self::root() . "%s/%s" . '/' . join("/", $vals);
            $r = sprintf($format, $e, $a);
        } else {
            $parms = array();
            foreach ($args as $k => $v) {
                $parms[] = "$k=$v";
            }
            $others = "";
            if (count($parms) > 0) {
                $others = "&" . join("&", $parms);
            }
            $format = self::root() . "index.php?entity=%s&action=%s" . $others;
            $r = sprintf($format, $e, $a);
        }
        //return self::root()."index.php?entity=$e&action=$a".$others;
        return $r;
    }

    public static function link($title, $path) {
        return "<a href=\"$path\" title=\"$title\">$title</a>";
    }
    
    public static function link_click($title, $click, $path, $summary = "") {
        $summary = ($summary == "")?$title:$summary;
        return "<a href=\"$path\" onclick=\"$click\" title=\"$summary\">$title</a>";
    }

    static function root() {
        if (defined('WB_USE_DB_ROOT') && WB_USE_DB_ROOT) {
            return Config::getValue("site_url");
        } else {
            return "http://" . $_SERVER ['HTTP_HOST'] . str_replace($_SERVER ['SCRIPT_NAME'], '/', $_SERVER ['REQUEST_URI'] );
        }
    }

    static function clink($t, $path, $summary="") {
        $format = '<a href="%s" title="%s" onclick="return wb_confirm(\'%s\');">%s</a>';        
        $title = ($summary == "")? "$t": $summary;
        $comfirm = ($summary == "")? "$t": $summary;
        return sprintf($format, $path, $title, $comfirm, $t);
    }

    public static function get_title() {
        if (self::$title == "") {
            return Config::getValue("site_title");
        } else {
            return self::$title;
        }
    }

    public static function set_title($t) {
        self::$title = $t;
    }
}
?>
