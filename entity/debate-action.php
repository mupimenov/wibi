<?php
/*
 *      debate-action.php
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
class DebateAction extends Action {


    public function DebateAction(){
        $this->entity_name = "debate";
        $this->available_funcs = array("onedit");
        $this->protected_funcs = array("edit" => array("onedit"));
    }

    public static function listen() {
        /* комментарии */
        PageAction::add_listener_after_render_detailed_page(array("DebateAction", "render_page_comments"));
        /* счётчик комментариев */      
        PageAction::add_listener_after_render_single_page(array("DebateAction", "render_countof_comments"));
        ConfigAction::add_listener_after_render_config_form(array("DebateAction", "render_config_form"));      
    }  
    
    public function onedit() {        
        Config::setValue("debate_acct", $this->parms["debate_acct"]);               
        // remember
        Logger::addMsg(_("intense debate configs are saved"));
        // out
        self::redirect_to(Utils::path("config", "edit"));
    }  

    public static function render_page_comments($parms = null) {
        $acct = Config::getValue("debate_acct");
        $p = $parms["page"];
        $page_id = $p->id;
        $page_url = Utils::path("page", "view", array("id" => $page_id));
        include 'tpl/debate-comments.tpl';
    }

    public static function render_countof_comments($parms = null) {
        $acct = Config::getValue("debate_acct");
        $p = $parms["page"];
        $page_id = $p->id;
        $page_url = Utils::path("page", "view", array("id" => $page_id));
        include 'tpl/debate-comments-count.tpl';
    }
    
    public static function render_config_form($parms = null) {        
        $debate_action = Utils::path("debate", "onedit");
        $debate_acct = Config::getValue("debate_acct");        
        
        include 'tpl/debate-form.tpl';
    }

}

?>
