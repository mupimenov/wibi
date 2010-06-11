<?php
/*
 *      tag.php
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
class Tag extends Entity {

    var $id;
    var $name;

    public function Tag($name, $id = null) {
        $this->name = $name;
        $this->id = $id;
    }

    static function get_last_tag() {
        $r = DB::sqlRow("SELECT * FROM `tags` ORDER BY `id` DESC LIMIT 1;");
        return new Tag($r["name"], $r["id"]);
    }

    public static function create_or_get_by($name) {
        $r = DB::sqlRow("SELECT * FROM `tags` WHERE `name`='".$name."';");
        if ($r) {
            return new Tag($r["name"], $r["id"]);
        } else {
            DB::exec("INSERT INTO `tags` (`name`) VALUES ('".$name."');");
            return self::get_last_tag();
        }
    }

    public static function get_present($id) {
        $r = DB::sqlRow("SELECT * FROM `tags` WHERE `id`=$id;");
        if ($r) {
            return new Tag($r["name"], $r["id"]);
        } else {
            return null;
        }
        
    }

    public static function remove($t) {
        DB::exec("DELETE FROM `tags_pages` WHERE `tag_id`=$t->id;");
        DB::exec("DELETE FROM `tags` WHERE `id`=$t->id;");
        return true;
    }

    public static function remove_links_for_page($p) {
        DB::exec("DELETE FROM `tags_pages` WHERE `page_id`=$p->id;");
        return true;
    }

    public static function save($t) {
        DB::exec("UPDATE `tags` SET `name`='$t->name' WHERE `id`=$t->id;");
        return $t;
    }

    public static function link_page_with_tags($p, $ts) {
        DB::exec("DELETE FROM `tags_pages` WHERE `page_id`=$p->id;");
        foreach ($ts as $t) {
            DB::exec("INSERT INTO `tags_pages` (`page_id`, `tag_id`) VALUES ($p->id, $t->id);");
        }
        return true;
    }

    public static function get_tags_for_page($p) {
        $ids = DB::sqlMulty("SELECT `tag_id` FROM `tags_pages` WHERE `page_id`=$p->id ORDER BY `tag_id` DESC;");
        $ts = array();
        foreach ($ids as $id) {
            $ts[] = self::get_present($id);
        }
        return $ts;
    }

    public static function get_pages_marked_with_tag($t) {
        $ids = DB::sqlMulty("SELECT `page_id` FROM `tags_pages` WHERE `tag_id`=$t->id ORDER BY `page_id` DESC;");
        $ps = array();
        foreach ($ids as $id) {
            $ps[] = Page::get_present($id);
        }
        return $ps;
    }

    public static function get_all_tags_desc_count() {
        $ids = DB::sqlMulty("SELECT `id`, (SELECT COUNT(*) FROM `tags_pages` WHERE `tag_id`=`tags`.`id`) AS count FROM `tags` ORDER BY count DESC;");
        $ts = array();
        foreach ($ids as $id) {
            $ts[] = self::get_present($id);
        }
        return $ts;
    }

    static function install($config = null) {
        $qry =
        "CREATE TABLE IF NOT EXISTS `tags` (
            `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(50) NOT NULL) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";

        $result = DB::exec($qry);

        $qry =
        "CREATE TABLE IF NOT EXISTS `tags_pages` (
            `page_id` INT(11) NOT NULL,
            `tag_id` INT(11) NOT NULL) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";

        $result = DB::exec($qry);

        if ($config["update"])
        {
            return true;
        }

        return true;
    }
}
?>
