<?php
/*
 *      activity.php
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

class Activity extends Entity {
    var $id;
    var $url;
    var $md5;
    
    function Activity($url, $md5, $id = null) {        
        $this->url = $url;
        $this->md5 = $md5;
        $this->id = $id;
    }
    
    public static function create_new($url, $md5) {
        $a = new Activity($url, $md5);
        self::save($a);
    }
    
    public static function get_present($id) {
        $r = DB::sqlRow("SELECT * FROM `activities` WHERE `id`=$id ORDER BY `id` DESC LIMIT 1;");
        if ($r) {
            return new Activity($r["url"], $r["md5"]);
        } else {
            return null;
        }
    }
    
    public static function get_all_activities() {
        $ids = DB::sqlMulty("SELECT `id` FROM `activities` ORDER BY `id` DESC;");
        $as = array();
        foreach ($ids as $id) {
            $as[] = self::get_present($id);
        }
        return $as;
    }
    
    static function get_last_id() {
        $last = DB::sqlRow("SELECT `id` FROM `activities` ORDER BY `id` DESC LIMIT 1;");
        return $last["id"];
    }
    
    public static function remove($a) {
        DB::exec("DELETE FROM `activities` WHERE `id`=$a->id;");
        return true;
    }

    public static function save($a) {
        if ($a->id) {            
            DB::exec("UPDATE `activities` SET `url`='".$a->url."', `md5`='".$a->md5."' WHERE `id`=$a->id;");
            return $a;
        } else {
            DB::exec("INSERT INTO `activities` (`url`,`md5`) VALUES ('".$a->url."','".$a->md5."');");
            $a->id = self::get_last_id();
            return $a;
        }
    }
    
    static function install($config = null) {
        $qry =
        "CREATE TABLE IF NOT EXISTS `activities` (
            `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `url` VARCHAR(100) NOT NULL,
            `md5` VARCHAR(32) NOT NULL) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";

        $result = DB::exec($qry);

        if ($config["update"])
        {
            return;
        }
    }
}

?>
