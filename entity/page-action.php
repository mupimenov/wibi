<?php

/*
 *      page-action.php
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

class PageAction extends Action {

    static $after_render_detailed_page = array();
    static $within_render_page_form = array();
    static $after_render_single_page = array();

    static $after_render_locked_pages = array();

    static $after_oncreate = array();
    static $after_onedit = array();
    static $after_remove = array();

    public function PageAction() {
        $this->entity_name = "page";
        $this->available_funcs = array("view", "viewall", "viewlast", "create", "oncreate", "edit", "onedit", "remove");
        $this->protected_funcs = array( "create" => array("create", "oncreate"),
                                        "edit" => array("edit", "onedit"),
                                        "remove" => array("remove"));
    }

    // listeners

    public static function add_listener_after_render_detailed_page($f) {
        self::$after_render_detailed_page[] = $f;
    }

    public static function add_listener_within_render_page_form($f) {
        self::$within_render_page_form[] = $f;
    }

    public static function add_listener_after_render_single_page($f) {
        self::$after_render_single_page[] = $f;
    }

    public static function add_listener_after_render_locked_pages($f) {
        self::$after_render_locked_pages[] = $f;
    }

    public static function add_listener_after_oncreate($f) {
        self::$after_oncreate[] = $f;
    }

    public static function add_listener_after_onedit($f) {
        self::$after_onedit[] = $f;
    }

    public static function add_listener_after_remove($f) {
        self::$after_remove[] = $f;
    }

    // object methods
    public function do_default() {
        self::render_last_pages($this->parms);
    }

    public function view() {
        if (isset($this->parms["id"])) {
            $id = Validator::numeric($this->parms["id"]);
        } else {
            Logger::addError(_("where is page's id?"));
            self::render_error_page();
            return;
        }
        $p = Page::get_present($id);        
        if ($p) {
            Utils::set_title("$p->title - " . Utils::get_title());
            self::render_detailed_page(array("page" => $p));
        } else {
            self::render_error_page();
        }
    }

    public function viewall() {
        self::render_all_pages_short();
    }

    public function viewlast(){
        self::render_last_pages($this->parms);
    }

    public function create() {
        self::render_create_form(array_merge($this->parms, array("page" => null)));
    }

    public function oncreate() {
        // data
        $page_title = $this->parms["page_title"];
        if ($page_title == "") {
            $page_title = _("(no title)");
        }
        $page_body = $this->parms["page_body"];
        if ($page_body == "") {
            $page_body = _("(empty body)");
        }
        $page_author = UserAction::get_user()->id;
        $page_date = time();
        // do
        $p = Page::create_new($page_title, $page_author, $page_date, $page_body);
        if (isset($this->parms["page_lock"]) and $this->parms["page_lock"] == 1) {
            $p = Page::lock($p);
        }        
        // log
        Logger::addMsg(_('page is created'));
        // listeners
        self::invoke_listeners(self::$after_oncreate, array_merge(array("page" => $p), $this->parms));
        // out
        self::redirect_to(Utils::path("page", "view", array("id" => $p->id)));
    }

    public function edit() {
        $id = Validator::numeric($this->parms["id"]);
        $p = Page::get_present($id);
        self::render_edit_form(array("page" => $p));
    }

    public function onedit() {
        // data
        $id = Validator::numeric($this->parms["page_id"]);
        $p = Page::get_present($id);
        $p->title = $this->parms["page_title"];
        if ($p->title == "") {
            $p->title = _("(empty title)");
        }
        $p->body = $this->parms["page_body"];
        if (empty($p->body)) {
            $p->body = _("(empty body)");
        }
        // do
        Page::save($p);
        Logger::addMsg(_('page is updated'));
        if ($this->parms["page_lock"] == 1) {
            $p = Page::lock($p);
        } else {
            $p = Page::unlock($p);
        }
        // listeners
        self::invoke_listeners(self::$after_onedit, array_merge(array("page" => $p), $this->parms));
        // out
        self::redirect_to(Utils::path("page", "view", array("id" => $p->id)));
    }

    public function remove() {
        // do
        $id = Validator::numeric($this->parms["id"]);
        $p = Page::get_present($id);
        
        if (Page::remove($p)) {
             Logger::addMsg(sprintf(_("page (%s) is removed"), $p->title));
        }
        // listeners
        self::invoke_listeners(self::$after_remove, array("page" => $p));
        // out
        self::redirect_to(Utils::path("page", "viewlast"));
    }

    public static function render_locked_pages($parms = null) {
        // data
        $locked_ps = Page::get_locked_pages();
        // out
        include 'tpl/pages-locked.tpl';
        // listeners
        self::invoke_listeners(self::$after_render_locked_pages, $parms);
    }

    public static function render_last_pages($parms = null) {
        // quick create
        UserAction::funcs_ctl(array("create" => array("PageAction", "render_qcreate_form")));
        // out
        $offset = 0;
        if (isset($parms["offset"])) {            
            $offset = Validator::numeric($parms["offset"]);
        }        
        $to_display = Config::getValue("page_last_limit");
        $ps = Page::get_last_pages($offset, $to_display);
        foreach ($ps as $p) {
            self::render_single_page(array("page" => $p));
        }
        // back and forward links 
        $prev = "";
        $next = "";
        if ($offset - $to_display >= 0) {
            $t = _("back...");
            $prev = sprintf("<a href=\"%s\" title=\"%s\" style=\"float: left;\">%s</a>", Utils::path("page", "viewlast", array("offset" => $offset - $to_display)), $t, $t);
        }
        if ($offset + $to_display < Page::get_pages_count()) {
            $t = _("...forward");
            $next = sprintf("<a href=\"%s\" title=\"%s\" style=\"float: right;\">%s</a>", Utils::path("page", "viewlast", array("offset" => $offset + $to_display)), $t, $t);
        }
        printf("<div style=\"height: 1.6em; padding-top: 0.6em\">%s %s</div>", $prev, $next);
    }

    public static function render_recent_pages($parms = null) {
        // количество последних постов
        $n = 5;
        $ps = Page::get_last_pages(0, $n);
        // вывести
        include 'tpl/pages-recent.tpl';
    }

    public static function render_all_pages_short($parms = null) {
        $ps = Page::get_last_pages(0, Page::get_pages_count());
        printf("<h2>%s</h2>", _("pages for all time"));
        foreach ($ps as $p) {
            self::render_short_page(array("page" => $p));
        }
    }

    public static function render_short_page($parms = null) {
        $p = $parms["page"];
        $page_id = $p->id;
        $page_ctl = UserAction::ctl(array(
                                             "edit" => Utils::link(_("edit"), Utils::path("page", "edit", array("id" => $p->id))),
                                             ));
        $page_title = Utils::link($p->title, Utils::path("page", "view", array("id" => $p->id)));
        $page_date = $p->date;
        // out
        include 'tpl/page-short.tpl';
    }

    public static function render_single_page($parms = null) {
        $p = $parms["page"];
        $page_id = $p->id;
        // data
        $page_ctl = UserAction::ctl(array(
                                             "edit" => Utils::link(_("edit"), Utils::path("page", "edit", array("id" => $p->id))),
                                             ));
        $page_title = Utils::link($p->title, Utils::path("page", "view", array("id" => $p->id)));
        $page_body = Format::full($p->body);
        $page_date = $p->date;
        $page_author = User::get_present($p->author)->name;
        // out
        include 'tpl/page-view.tpl';
        // listeners
        self::invoke_listeners(self::$after_render_single_page, $parms);
    }

    public static function render_detailed_page($parms = null) {
        // create form
        UserAction::funcs_ctl(array("create" => array("PageAction", "render_qcreate_form")));

        $p = $parms["page"];
        $page_id = $p->id;
        // data
        $page_ctl = UserAction::ctl(array(
                                             "edit" => Utils::link(_("edit"), Utils::path("page", "edit", array("id" => $p->id))),
                                             ));
        $page_title = Utils::link($p->title, Utils::path("page", "view", array("id" => $p->id)));
        $page_body = Format::full($p->body);
        $page_date = $p->date;
        $page_author = User::get_present($p->author)->name;
        // out
        include 'tpl/page-view.tpl';

        self::invoke_listeners(self::$after_render_detailed_page, $parms);
    }

    public static function render_create_form($parms = null) {
        // data
        $page_action = Utils::path("page", "oncreate");
        $page_id = ""; $page_title = ""; $page_body = ""; $page_date = "";
        $page_author = ""; $lock_checked = ""; $page_remove = "";
        if ($parms["page_qcreate"]) {
            $page_body = $parms["page_body"];
        }
        // out
        include 'tpl/page-edit.tpl';
    }

    public static function render_qcreate_form($parms = null) {
        // data
        $page_action = Utils::path("page", "create");
        $page_body = "";
        // out
        include 'tpl/page-qcreate.tpl';
    }

    function render_edit_form($parms = null) {
        // data
        $p = $parms["page"];
        $page_action = Utils::path("page", "onedit");
        $page_id = $p->id;
        $page_title = $p->title;
        $page_body = $p->body;
        $page_date = $p->date;
        $page_author = $p->author;
        $page_remove = UserAction::ctl(array("remove" => Utils::clink("x", Utils::path("page", "remove", array("id" => $p->id)))));
        $lock_checked = "";
        if (Page::is_page_locked($p)) {
            $lock_checked = "checked";
        }
        // out
        include 'tpl/page-edit.tpl';
    }

    public static function render_error_page($parms = null) {
        include 'tpl/page-error.tpl';
    }

}
?>
