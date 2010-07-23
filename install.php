<?php

/*
 *      install.php
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

require_once 'configdb.php';
require_once 'core/db.php';
require_once "core/action.php";
require_once "core/entity.php";

class UtilsEx {
    static function root() {
        return "http://" . $_SERVER ['HTTP_HOST'] . str_replace($_SERVER ['SCRIPT_NAME'], '/', $_SERVER ['REQUEST_URI'] );
    }

    static function mkPath($e, $a) {
        return self::root()."install.php?entity=$e&action=$a";
    }

    static function a($t, $e=null, $a=null) {
        if ($e == null || $a == null) {
            return '<a href="'.self::root().'" title="'.$t.'" >'."$t".'</a>';
        } else {
            return '<a href="'.self::mkPath($e, $a).'" title="'."$a $e".'" >'."$t".'</a>';
        }

    }
}

require_once 'entity/config.php';
require_once 'entity/page.php';
require_once 'entity/user.php';
require_once "entity/tag.php";
require_once "entity/comment.php";
require_once 'entity/block.php';
require_once 'entity/activity.php';
require_once 'entity/debate.php';

class Install extends Action {

    public function Install() {
        $this->entity_name = "install";
        $this->available_funcs = array("setup", "update", "onsetup", "finished");
    }

    public function do_default() {
        $this->setup();
    }

    public function do_it() {
        $func = $this->func;
        if (in_array($func, $this->available_funcs)) {
            $this->$func();
        } else {
            $this->doDefault();
        }
    }

    public function update() {
        Config::install(array("update" => true));
        User::install(array("update" => true));
        Page::install(array("update" => true));
        Tag::install(array("update" => true));
        Comment::install(array("update" => true));
        Block::install(array("update" => true));
        Activity::install(array("update" => true));
        Debate::install(array("update" => true));

        echo UtilsEx::a('to blog');
    }


    public function setup() {
        if (DB::isConnected()) {
            $install_action = UtilsEx::mkPath("install", "onsetup");
            $site_url = UtilsEx::root();
            $site_title = "another wibi blog";
            $update_url = UtilsEx::a("update", "install", "update");
            $form = '<form action="%s" method="post">
                        <input type="submit" class="btn" value="install" />
                        <p>site url:</p>
                        <input type="text" class="txt" name="site_url" value="%s" />
                        <p>site title (http://site.ru/ format)</p>
                        <input type="text" class="txt" name="site_title" value="%s" />
                        <p> admin password (repeat twice)</p>
                        <input type="password" class="txt" name="admin_pwd" />
                        <input type="password" class="txt" name="admin_rpwd" />
                        %s
                    </form>';
            printf($form, $install_action, $site_url, $site_title, $update_url);
        } else {
            echo '<p> connection to the db <span style="color: red;">is not established!</span>';
        }
    }

    public function onsetup() {
        $error = false;
        $log = "<ul>";
        if (!$this->parms["admin_pwd"] OR !$this->parms["admin_rpwd"]) {
            $error = true;
            $log .= "<li>fill all password fields</li>";
        } elseif ($this->parms["admin_pwd"] != $this->parms["admin_rpwd"]) {
            $error = true;
            $log .= "<li>passwords are not equal</li>";
        }
        if (!$this->parms["site_url"] OR
            !strstr(UtilsEx::root(), $this->parms["site_url"])
        ) {
            $error = true;
            $log .= "<li>site url is wrong</li>";
        }
        $log .= "</ul>";
        if ($error) {
            echo $log;
            return;
        }

        Config::install(array(  "update" => false,
                                "site_url" => $this->parms["site_url"],
                                "site_title" => $this->parms["site_title"],
                                "page_last_limit" => 50  ));
        User::install(array(    "update" => false,
                                "admin_pwd" => $this->parms["admin_pwd"]    ));
        Page::install(array("update" => false));
        Tag::install(array("update" => false));
        Comment::install(array("update" => false));
        Block::install(array("update" => false));
        Activity::install(array("update" => false));
        Debate::install(array("update" => false));

        echo UtilsEx::a('to blog');
    }

    public function finished() {
        echo '<p> yehooo!!! </p>';
    }
}

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title>installing wibi</title>
        <link href="<?php echo UtilsEx::root()."html/style.css"; ?>" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <div id="container">
            <div class="install-form">
                <?php
                $i = new Install();
                $i->check();
                if ($i->is_failed()) {
                    $i->do_default();
                } else {
                    $i->do_it();
                }
                ?>
            </div>
        </div>
    </body>
</html>
