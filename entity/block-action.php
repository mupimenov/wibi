<?php
/*
 *      block-action.php
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
class BlockAction extends Action {

    public function BlockAction(){
        $this->entity_name = "block";
        $this->available_funcs = array("editall", "edit", "onedit", "remove", "oncreate");
        $this->protected_funcs = array( "edit" => array("editall", "edit", "onedit"),
                                        "remove" => array("remove"),
                                        "create" => array("oncreate"));
    }

    public static function listen() {
        //
    }    

    public function editall(){
        self::render_blocks_within_forms();
    }

    public function onedit() {
        $block_id = Validator::numeric($this->parms["block_id"]);
        $b = Block::get_present($block_id);        
        $b->name = $this->parms["block_name"];        
        $b->body = $this->parms["block_body"];
        if (isset($this->parms["block_name_shown"])) {
            $b->name_shown = 1;
        } else {
            $b->name_shown = 0;
        }
        if (isset($this->parms["block_position"])) {
            $b->position = $this->parms["block_position"];
        } else {
            $b->position = 0;
        }
        // do
        $b2 = Block::save($b);
        Logger::addMsg(sprintf(_("block %s is saved"), $b2->name));
        // redirect
        self::redirect_to(Utils::path("block", "editall"));
    }

    public function remove() {
        $block_id = Validator::numeric($this->parms["id"]);
        $b = Block::get_present($block_id);
        if (Block::remove($b)) {
            Logger::addMsg(sprintf(_("block is removed"), $b->name) );
        }
        // redirect
        self::redirect_to(Utils::path("block", "editall"));
    }

    public function oncreate() {
        $block_name = $this->parms["block_name"];
        $block_body = $this->parms["block_body"];
        if (isset($this->parms["block_name_shown"])) {
            $block_name_shown = 1;
        } else {
            $block_name_shown = 0;
        }
        if (isset($this->parms["block_position"])) {
            $block_position = $this->parms["block_position"];
        } else {
            $block_position = 0;
        }
        $b = Block::create_new($block_name, $block_body, $block_name_shown, $block_position);
        Logger::addMsg(sprintf(_("block is created"), $b->name));
        // redirect
        self::redirect_to(Utils::path("block", "editall"));
    }

    public static function render_blocks_within_forms($parms = null) {
        $blocks = Block::get_all_blocks();
        foreach ($blocks as $b) {
            self::render_block_edit_form($b);
        }
        self::render_block_create_form();
    }

    public static function render_block_edit_form($b) {
        $block_id = $b->id;
        $block_name = $b->name;
        $block_body = $b->body;
        $block_name_shown = "";
        if ($b->name_shown) {
            $block_name_shown = "checked";
        }
        $block_position = $b->position;

        $block_action = Utils::path("block", "onedit");
        $block_remove = UserAction::ctl(array("remove" => Utils::clink("x", Utils::path("block", "remove", array("id" => $block_id)))));

        include 'tpl/block-form.tpl';
    }

    public static function render_block_create_form($parms = null) {        
        $block_id = "";
        $block_name = "";
        $block_body = "";
        $block_name_shown = "checked";
        $block_position = 10;

        $block_remove = "";
        $block_action = Utils::path("block", "oncreate");

        include 'tpl/block-form.tpl';
    }

    public static function render_all_blocks($parms = null) {
        $blocks = Block::get_all_blocks();
        $htmlblocks = "";
        foreach ($blocks as $b) {
            $bname = "";
            if ($b->name_shown > 0) {
                $bname = "<div class=\"block-name\">" . $b->name . "</div>";
            }
            $htmlblocks .= "<div class=\"block\">" . $bname . $b->body . "</div>";
        }
        $format = "<div id=\"blocks\">%s %s</div>";
        printf($format, UserAction::ctl(array("edit" => Utils::link(_("edit"), Utils::path("block", "editall")))), $htmlblocks);
    }
}
?>
