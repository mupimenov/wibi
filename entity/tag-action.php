<?php
/*
 *      tag-action.php
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
class TagAction extends Action {

    public function TagAction(){
        $this->entity_name = "tag";
        $this->available_funcs = array("view", "viewall", "edit", "onedit", "remove");
        $this->protected_funcs = array( "edit" => array("edit", "onedit"),
                                        "remove" => array("remove"));
    }

    public static function listen() {
        PageAction::add_listener_within_render_page_form(array("TagAction", "render_tag_subform"));
        PageAction::add_listener_after_oncreate(array("TagAction", "listen_page_oncreate_onedit"));
        PageAction::add_listener_after_onedit(array("TagAction", "listen_page_oncreate_onedit"));
        PageAction::add_listener_after_remove(array("TagAction", "listen_page_remove"));
        PageAction::add_listener_after_render_single_page(array("TagAction", "render_tags_for_page"));
        PageAction::add_listener_after_render_detailed_page(array("TagAction", "render_tags_for_page"));        
    }

    public function view(){
        // checks
        if (isset($this->parms["id"])) {
            $id = Validator::numeric($this->parms["id"]);
        } else {
            Logger::addError(_("where tag's id?"));
            PageAction::render_error_page();
            return;
        }
        $t = Tag::get_present($id);
        if (is_null($t)) {
            PageAction::render_error_page();
            return;
        }
        // do
        $ps = Tag::get_pages_marked_with_tag($t);
        Utils::set_title("pages for tag $t->name - " . Utils::get_title());
        if (count($ps)>0) {
            printf("<h2>"._("pages for tag <i>%s</i>")."</h2>", $t->name);
            foreach ($ps as $p) {
                PageAction::render_short_page(array("page" => $p));
            }
        } else {
             printf("<h2>"._("there are no pages for tag <i>%s</i>")."</h2>", $t->name);
        }
    }

    public function viewall(){
        Utils::set_title(_("list of the tags - ") . Utils::get_title());
        self::render_tags_with_ctl();
    }

    public function edit() {
        $id = $this->parms["id"];
        $t = Tag::get_present($id);
        self::render_tag_form(array("tag" => $t));
    }

    public function onedit() {
        $id = $this->parms["tag_id"];
        $t = Tag::get_present($id);        
        $t->name = $this->parms["tag_name"];        
        // do
        if (Tag::save($t)) {
            Logger::addMsg(sprintf(_("tag %s is updated"), $t->name));
        }
        // redirect
        self::redirect_to(Utils::path("tag", "viewall"));
    }

    public function remove() {
        $id = $this->parms["id"];
        $t = Tag::get_present($id);
        // do
        if (Tag::remove($t)) {
            Logger::addMsg(sprintf(_("tag %s is removed"), $t->name));
        }
        // redirect
        self::redirect_to(Utils::path("tag", "viewall"));
    }

    public static function render_tag_subform($parms = null) {
        $p = $parms["page"];
        // page's tags
        $names = array();
        if ($p) {
            $ts = Tag::get_tags_for_page($p);
            foreach ($ts as $t) {
                $names[] = $t->name;
            }
        }
        // list of all available tags
        $allts = Tag::get_all_tags_desc_count();
        $tagslinks = "<div id=\"all-tags\">";
        foreach ($allts as $t) {
            $tagslinks .= "<a href=\"#\" title=\"$t->name\">$t->name</a>";
        }
        $tagslinks .= "</div>";
        // out
        $format = "<h4>%s</h4><textarea id=\"page-tags\" name=\"page_tags\" class=\"txt wide sizable\">%s</textarea>%s<script type=\"text/javascript\">wb_appendTags();</script>";
        printf($format, _("tags"), join(", ", $names), $tagslinks);
    }

    public static function render_tags_with_ctl($parms = null) {
        $ts = Tag::get_all_tags_desc_count();
        $tags = "";
        foreach ($ts as $t) {
            $tags .= "<div class=\"tag-ctl\">"
                     . UserAction::ctl(array("edit" => Utils::link(_("edit"), Utils::path("tag", "edit", array("id" => $t->id)))))
                     . Utils::link($t->name, Utils::path("tag", "view", array("id" => $t->id)))
                     . "</div>";
        }
        $format = "<div class=\"tags\"><h2>%s:</h2>%s</div>";
        printf($format, _("tags"), $tags);
    }

    public static function render_tag_form($parms = null) {
        $t = $parms["tag"];
        $tag_id = $t->id;
        $tag_name = $t->name;
        $tag_action = Utils::path("tag", "onedit", array("id" => $t->id));
        $tag_remove = UserAction::ctl(array("remove" => Utils::clink("x", Utils::path("tag", "remove", array("id" => $t->id)))));

        include 'tpl/tag-form.tpl';
    }

    public static function render_all_tags($parms = null) {
        $ts = Tag::get_all_tags_desc_count();
        $tags = "";
        foreach ($ts as $t) {
            $tags .= '<span class="tag">'.Utils::link($t->name, Utils::path("tag", "view", array("id" => $t->id))).'</span>';
        }
        $tags .= '<span style="font-style: italic;">' . Utils::link(_("... show all"), Utils::path("tag", "viewall")) . '</span>';
        $format = "<div id=\"tag-list\"><h6>%s:</h6>%s</div>";
        printf($format, _('tags') , $tags);
    }

    static function listen_page_oncreate_onedit($parms = null) {
        // prepare
        $glue = ",";
        $p = $parms["page"];
        $page_tags = $parms["page_tags"];
        $names = split($glue, $page_tags);
        // get ids
        $ts = array();
        foreach ($names as $name) {
            $n = Tag::create_or_get_by(Validator::nonhtml(trim($name)));
            if (!is_null($n)) {
                $ts[] = Tag::create_or_get_by(trim($name));
            }
        }
        // link
        Tag::link_page_with_tags($p, $ts);
    }

    static function listen_page_remove($parms = null) {
        $p = $parms["page"];
        Tag::remove_links_for_page($p);
    }

    public static function render_tags_for_page($parms = null) {
        $p = $parms["page"];
        $ts = Tag::get_tags_for_page($p);
        // data
        $names = array();
        foreach ($ts as $t) {
            $names[] = Utils::link($t->name, Utils::path("tag", "view", array("id" => $t->id)));
        }
        // out
        if (count($names)>0) {
            printf("<h6>%s: %s</h6>", _("tags"), join(", ", $names));
        }
    }
}
?>
