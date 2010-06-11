<?php

/*
 *      config.php
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

class Config extends Entity {

    static function getValue($key) {        
        $q = DB::sqlRow("SELECT * FROM `configs` WHERE `key`='$key';");        
        return $q["value"];
    }

    static function setValue($key, $value) {
        DB::exec("UPDATE `configs` SET `value`='$value' WHERE `key`='$key';");
        return;
    }

    static function install($config=null) {        
        $qry =
        "CREATE TABLE IF NOT EXISTS `configs` (
            `key` VARCHAR(50) NOT NULL PRIMARY KEY,
            `value` TEXT NOT NULL default ''
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
        $result = DB::exec($qry);      
        
        if ($config["update"])
        {
            return;            
        }
          
        $site_url = $config["site_url"];
        $site_title = $config["site_title"];
        $page_last_limit = $config["page_last_limit"];

        DB::exec("INSERT INTO `configs` (`key`, `value`) VALUES ('site_title', '$site_title');");
        DB::exec("INSERT INTO `configs` (`key`, `value`) VALUES ('site_url', '$site_url');");
        DB::exec("INSERT INTO `configs` (`key`, `value`) VALUES ('page_last_limit', '$page_last_limit');");
        return ;
    }
}
?>
