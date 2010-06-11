<?php
/*
 *      comment.php
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

class Comment extends Entity {

    var $id;
    var $page_id;
    var $author;
    var $body;
    var $time;

    public function Comment($page_id, $author, $body, $time, $id = null) {
        $this->page_id = $page_id;
        $this->author = $author;
        $this->body = $body;
        $this->time = $time; // wtf?
        
        $this->id = $id;
    }

    public static function get_comments_for_page($p) {
        $ids = DB::sqlMulty("SELECT `id` FROM `comments` WHERE `page_id`=$p->id ORDER BY `id` ASC;");
        $cs = array();
        foreach ($ids as $id) {
            $cs[] = self::get_present($id);
        }
        return $cs;
    }

    static function get_last_id() {
        $last = DB::sqlRow("SELECT `id` FROM `comments` ORDER BY `id` DESC LIMIT 1;");
        return $last["id"];
    }

    public static function get_last_uniq_comments($count) {
        $ids = DB::sqlMulty("SELECT max(`id`) AS id FROM `comments` GROUP BY `page_id` ORDER BY id DESC LIMIT $count;");        
        $cs = array();
        foreach ($ids as $id) {
            $cs[] = self::get_present($id);
        }        
        return $cs;
    }

    public static function create_new($page_id, $author, $body, $time) {
        $c = new Comment($page_id, $author, $body, $time);
        return self::save($c);
    }

    public static function get_present($id) {
        $r = DB::sqlRow("SELECT * FROM `comments` WHERE `id`=$id;");
        if ($r) {
            return new Comment($r["page_id"], $r["author"], $r["body"], $r["time"], $r["id"]);
        } else {
            return null;
        }
        
    }

    public static function save($c) {
         if ($c->id) {
            return $c; // wtf? comment has no ability to edit
        } else {
            DB::exec("INSERT INTO `comments` (`page_id`, `author`, `body`, `time`) VALUES ($c->page_id, '".$c->author."', '".$c->body."', NOW());");
            $c->id = self::get_last_id();
            return $c;
        }
    }

    public static function remove($c) {
        DB::exec("DELETE FROM `comments` WHERE `id`=$c->id;");
        return true;
    }

    public static function remove_page_comments($p){
        $cs = self::get_comments_for_page($p);
        foreach ($cs as $c) {
            self::remove($c);
        }
        DB::exec("DELETE FROM `comments_locks` WHERE `page_id`=$p->id;");
    }

    public static function is_page_locked($p) {
        $q = DB::sqlRow("SELECT `is_locked` FROM `comments_locks` WHERE `page_id`=$p->id;");
        if ($q["is_locked"]) {
            return $q["is_locked"];
        } else {
            DB::exec("INSERT INTO `comments_locks` (`page_id`, `is_locked`) VALUES ($p->id, 0);");
            return false;
        }
    }

    public static function lock_page($p) {
        $q = DB::sqlRow("SELECT `is_locked` FROM `comments_locks` WHERE `page_id`=$p->id;");
        if (!$q) {
            DB::exec("INSERT INTO `comments_locks` (`page_id`, `is_locked`) VALUES ($p->id, 1);");
            return true;
        }
        DB::exec("UPDATE `comments_locks` SET `is_locked`=1 WHERE `page_id`=$p->id;");
    }

    public static function unlock_page($p) {
        DB::exec("UPDATE `comments_locks` SET `is_locked`=0 WHERE `page_id`=$p->id;");
    }

    static function install($config = null) {
        $qry =
        "CREATE TABLE IF NOT EXISTS `comments` (
            `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `page_id` INT(11) NOT NULL,
            `author` VARCHAR(50) NOT NULL,
            `body` TEXT default '',
            `time` DATETIME NOT NULL) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";

        $result = DB::exec($qry);

        $qry =
        "CREATE TABLE IF NOT EXISTS `comments_locks` (
            `page_id` INT(11) NOT NULL PRIMARY KEY,
            `is_locked` INT(11) NOT NULL default 0 ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";

        $result = DB::exec($qry);

        if ($config["update"])
        {
            return;
        }
    }
}

?>
