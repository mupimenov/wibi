<?php

/*
 *      manager.php
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

class Manager {
    
    protected static $actions = array();
    protected static $interrupt = null;

    public static function set_interrupt($int) {
        self::$interrupt = $int;
    }

    public static function add_action($a) {
        self::$actions[] = $a;
    }

    public static function manage() {    
        $a = null;

        // отобрать валидный экшн
        for ($i=0; $i<count(self::$actions); $i++) {
            self::$actions[$i]->check();
            if (!self::$actions[$i]->is_failed()) {
                $a = self::$actions[$i];
                break;
            }
        }
        
        // проверка: есть ли action
        $f = "do_it";
        if ($a == null) {            
            $a = new PageAction();
            $f = "do_default";
        }
        
        // здесь обрабатываются основные действия
        $b = new Box(array($a, $f));
        $content = $b->render_to_variable();
        
        // если выполенение прервано
        if (!is_null(self::$interrupt)) {
            call_user_func(self::$interrupt);
            return;
        }

        // вывод предложения залогиниться
        $b = new Box(array("UserAction", "render_user_state"));
        $user = $b->render_to_variable();

        // наполнение сайдбара
        $b = new Box(   array("RssAction", "render_available_rss_feeds"),
                        array("PageAction", "render_locked_pages"),
                        array("PageAction", "render_recent_pages"),
                        array("TagAction", "render_all_tags"),
                        array("BlockAction", "render_all_blocks"));
        $sidebar = $b->render_to_variable();

        // вывод лога
        $b = new Box(array("Logger", "render_log"));
        $log = $b->render_to_variable();

        // собственно шаблон
        header("Content-Type: text/html; charset=UTF-8");
        include "entity/tpl/layout.tpl";
    }
}
?>
