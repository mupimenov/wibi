<?php
/*
 *      block.php
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
class Block extends Entity {

    var $id;
    var $name;
    var $body;
    var $name_shown;
    var $position;

    function Block($name, $body, $name_shown, $position, $id = null) {
        $this->name = $name;
        $this->body = $body;
        $this->name_shown = $name_shown;
        $this->position = $position;

        $this->id = $id;
    }    

    static function get_id_by_name($name) {
        $last = DB::sqlRow("SELECT `id` FROM `blocks` WHERE `name`='".$name."' ORDER BY `id` DESC LIMIT 1;");
        return $last["id"];
    }

    public static function create_new($name, $body, $name_shown=1, $position=0) {
        $b = new Block($name, $body, $name_shown, $position);
        return self::save($b);
    }

    public static function get_present($id) {
        $r = DB::sqlRow("SELECT * FROM `blocks` WHERE `id`=$id ORDER BY `id` DESC LIMIT 1;");
        if ($r) {
            return new Block($r["name"], $r["body"], $r["name_shown"], $r["position"], $r["id"]);
        } else {
            return null;
        }
        
    }

    public static function get_all_blocks() {
        $ids = DB::sqlMulty("SELECT `id` FROM `blocks` ORDER BY `position` ASC;");
        $bs = array();
        foreach ($ids as $id) {
            $bs[] = self::get_present($id);
        }
        return $bs;
    }

    public static function remove($b) {
        DB::exec("DELETE FROM `blocks` WHERE `id`=$b->id;");
        return true;
    }

    public static function save($b) {
        if ($b->id) {            
            DB::exec("UPDATE `blocks` SET `name`='".$b->name."', `body`='".$b->body."', `name_shown`=$b->name_shown, `position`=$b->position WHERE `id`=$b->id;");
            return $b;
        } else {
            DB::exec("INSERT INTO `blocks` (`name`,`body`,`name_shown`,`position`) VALUES ('".$b->name."','".$b->body."', $b->name_shown, $b->position);");
            $b->id = self::get_id_by_name($b->name);
            return $b;
        }
    }    

    static function install($config = null) {
        $qry =
        "CREATE TABLE IF NOT EXISTS `blocks` (
            `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(50) NOT NULL,
            `body` TEXT NOT NULL,
            `name_shown` INT(11) NOT NULL,
            `position` INT(11) NOT NULL) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";

        $result = DB::exec($qry);

        if ($config["update"])
        {
            return;
        }
    }
}
?>
