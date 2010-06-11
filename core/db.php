<?php

/*
 *      db.php
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

class DB {

    protected static $connected = false;
    protected static $connection;
    
    protected static function connect() {
        self::$connected = false;

        if (self::$connected == false) {
            if (ConfigDB::$name != "") {
                // connect to the base
                self::$connection = mysql_connect(ConfigDB::$server, ConfigDB::$user, ConfigDB::$pwd);
                if (self::$connection) {					
					self::$connected = true;					
					$r = mysql_select_db(ConfigDB::$name, self::$connection);
					mysql_query('SET names=utf8');  
					mysql_query('SET character_set_client=utf8');
					mysql_query('SET character_set_connection=utf8');   
					mysql_query('SET character_set_results=utf8');   
					mysql_query('SET collation_connection=utf8_general_ci'); 
					return $r;
				}           
                return;               
            }
        }
        
        
    }

    public static function isConnected() {
        if (self::$connected == false) {
            self::connect();
        }
        return self::$connected;
    }

    public static function sqlMulty($query) {
        if (!self::$connected) {
            self::connect();
        }
        $r = mysql_query($query);
        $result = array();
        while ($row = mysql_fetch_row($r)) {
            $result[] = $row[0];
        }
        return $result;
    }

    public static function sqlRow($query) {
        if (!self::$connected) {
            self::connect();            
        }        
        $r = mysql_query($query);        
        return mysql_fetch_assoc($r);
    }

    public static function getLast($query) {
        if (!self::$connected) {
            self::connect();
        }
        $r = mysql_query($query);
        return mysql_fetch_row($r);
    }

    public static function exec($e) {
        //if (!self::$connected) {
            self::connect();
        //}
        return mysql_query($e);
    }
}
?>
