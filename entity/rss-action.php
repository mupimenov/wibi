<?php
/*
 *      rss-action.php
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
class RssAction extends Action {

    public function RssAction(){
        $this->entity_name = "rss";
        $this->available_funcs = array("pages", "comments");
        $this->protected_funcs = array();
    }

    public function pages() {
        Manager::set_interrupt(array("RssAction", "render_rss_for_pages"));
    }

    public function comments() {
        Manager::set_interrupt(array("RssAction", "render_rss_for_comments"));
    }

    public static function render_rss_for_pages($parms = null) {
        $ps = Page::get_last_pages(0, Config::getValue("page_last_limit"));
        header('Content-type: application/xml; charset=UTF-8');
        include 'tpl/rss-pages.tpl';
    }

    public static function render_rss_for_comments($parms = null) {
        $cs = Comment::get_last_uniq_comments(10);
        header('Content-type: text/xml; charset=UTF-8');
        include 'tpl/rss-comments.tpl';
    }

    public static function render_available_rss_feeds($parms = null) {
        $format = '<div id="feeds"><h6>%s</h6><ul><li>%s</li><li>%s</li></ul></div>';
        printf($format, _("rss:"), Utils::link(_("pages"), Utils::path("rss", "pages")), Utils::link(_("comments"), Utils::path("rss", "comments")));
    }
}
?>
