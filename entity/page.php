<?php

/*
 *      page.php
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

class Page extends Entity {

    var $id;
    var $title;
    var $author;
    var $date;
    var $body;

    public function Page($title, $author, $date, $body, $id = null) {
        $this->title = $title;
        $this->author= $author;
        $this->date = $date;
        $this->body = $body;

        $this->id = $id;
    }

    static function get_pages_count() {
        $count = DB::sqlRow("SELECT COUNT(*) FROM `pages`;");
        return $count["COUNT(*)"];
    }

    public static function get_present($id) {
        $r = DB::sqlRow("SELECT * FROM `pages` WHERE `id`=$id;");
        if ($r) {
            return new Page($r["title"], $r["author"], $r["date"], $r["body"], $r["id"]);
        } else {
            return null;
        }
        
    }
    
    public static function create_new($title, $author, $date, $body) {
        $p = new Page($title, $author, $date, $body);
        return self::save($p);
    }

    public static function save($p){
        if ($p->id) {
            DB::exec("UPDATE `pages` SET `title`='$p->title', `body`='$p->body' WHERE `id`=$p->id;");
            return $p;
        } else {
            DB::exec("INSERT INTO `pages` (`title`, `date`, `author`, `body`) VALUES ('$p->title', NOW(), $p->author, '$p->body');");
            $created_page = self::get_last_page();
            DB::exec("INSERT INTO `locks` (`page_id`, `is_locked`) VALUES ($created_page->id, 0);");
            return $created_page;
        }
        
        return $id;
    }

    static function lock($p) {
        DB::exec("UPDATE `locks` SET `is_locked`=1 WHERE `page_id`=$p->id;");
        return $p;
    }

    static function unlock($p) {
        DB::exec("UPDATE `locks` SET `is_locked`=0 WHERE `page_id`=$p->id;");
        return $p;
    }

    static function is_page_locked($p) {
        $r = DB::sqlRow("SELECT `is_locked` FROM `locks` WHERE `page_id`=$p->id;");
        return $r["is_locked"];
    }

    static function get_locked_pages() {
        $ids = DB::sqlMulty("SELECT `page_id` FROM `locks` WHERE `is_locked`=1 ORDER BY `page_id` DESC;");
        $ps = array();
        foreach ($ids as $id) {
            $ps[] = self::get_present($id);
        }
        return $ps;
    }


    static function remove($p) {
        DB::exec("DELETE FROM `pages` WHERE `id`=$p->id;");
        DB::exec("DELETE FROM `locks` WHERE `page_id`=$p->id;");
        return true;
    }

    static function get_last_page() {
        $r = DB::sqlRow("SELECT * FROM `pages` ORDER BY `id` DESC LIMIT 1;");
        return new Page($r["title"], $r["author"], $r["date"], $r["body"], $r["id"]);
    }

    static function get_last_pages($offset, $count) {
        $ids = DB::sqlMulty("SELECT `id` FROM `pages` ORDER BY `id` DESC LIMIT $offset,$count;");
        $ps = array();
        foreach ($ids as $id) {
            $ps[] = self::get_present($id);
        }
        return $ps;
    }

    static function install($config=null) {
        $qry =
            "CREATE TABLE IF NOT EXISTS `pages` (
            `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `title` TEXT default '',
            `date` DATE NOT NULL,
            `author` INT(11) NOT NULL,
            `body` TEXT default '') ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";

        $result = DB::exec($qry);

        $qry =
            "CREATE TABLE IF NOT EXISTS `locks` (
            `page_id` INT(11) NOT NULL PRIMARY KEY,
            `is_locked` INT(11) NOT NULL default 0 ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";

        $result = DB::exec($qry);

        if ($config["update"]) {
            return;
        }

        self::create_new("First page",time(),1,"The first message of this blog");

        return true;
    }
}
?>
