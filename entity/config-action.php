<?php

/*
 *      config-action.php
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

class ConfigAction extends Action {

    public function ConfigAction() {
        $this->entity_name = "config";
        $this->available_funcs = array("edit", "onedit");
        $this->protected_funcs = array( "edit" => array( "edit", "onedit" ) );
    }

    public function edit() {
        self::render_config_form();
    }

    public function onedit() {
        // do
        Config::setValue("site_title", $this->parms["site_title"]);
        Config::setValue("site_url", $this->parms["site_url"]);
        Config::setValue("page_last_limit", $this->parms["page_last_limit"]);
        // remember
        Logger::addMsg(_("site configs are saved"));
        // out
        self::redirect_to(Utils::path("config", "edit"));
        //self::render_config_form();
    }

    public static function listen() {
        UserAction::add_listener_after_render_user_config_form(array("ConfigAction","render_edit_url"));
    }

    public static function render_edit_url($parms = null) {
        // out
        echo '<div style="width: 300px; margin: auto;">'.Utils::link(_("site configuration"), Utils::path("config", "edit")).'</div>';
        return;
    }

    public static function render_config_form($parms = null) {
        // data
        $config_action = Utils::path("config", "onedit");
        $site_title = Config::getValue("site_title");
        $site_url = Config::getValue("site_url");
        $page_last_limit = Config::getValue("page_last_limit");
        // out
        include 'tpl/config-form.tpl';;
    }
}
?>
