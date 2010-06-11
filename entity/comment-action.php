<?php
/*
 *      comment-action.php
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
class CommentAction extends Action {

    static $prime_numbers = array(2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37, 41, 43, 47, 53, 59, 61, 67, 71, 73, 79, 83, 89, 97);

    public function CommentAction(){
        $this->entity_name = "comment";
        $this->available_funcs = array("oncreate", "remove");
        $this->protected_funcs = array("remove" => array("remove"));
    }

    public static function listen() {
        PageAction::add_listener_after_render_detailed_page(array("CommentAction", "render_page_comments"));
        PageAction::add_listener_within_render_page_form(array("CommentAction", "render_comment_subform"));
        PageAction::add_listener_after_render_single_page(array("CommentAction", "render_countof_comments"));
        PageAction::add_listener_after_oncreate(array("CommentAction", "listen_page_oncreate_onedit"));
        PageAction::add_listener_after_onedit(array("CommentAction", "listen_page_oncreate_onedit"));
        PageAction::add_listener_after_remove(array("CommentAction", "listen_page_remove"));
        UserAction::add_listener_after_render_user_home(array("CommentAction", "render_last_comments"));
    }

    public function oncreate(){
        if (!isset($this->parms["page_id"])) {
            Logger::addError(_("can not create comment: what is the page?"));
            return;
        }
        $page_id = Validator::numeric($this->parms["page_id"]);
        $p = Page::get_present($page_id);
        if (is_null($p)) {
            PageAction::render_error_page();
            return;
        }
        // Для начала проверим можно ли комментировать.
        if (Comment::is_page_locked($p)) {
            Logger::addError(_("commenting is disabled"));
            self::redirect_to(Utils::path("page", "view", array("id" => $p->id)));
            return;
        }
        $page_id = $p->id;
        $comment_author = $this->parms["comment_author"];
        $comment_body = $this->parms["comment_body"];
        $comment_time = time();
        $captcha_gthan = $this->parms["captcha_gthan"];
        $captcha_prime = isset($this->parms["captcha_prime"])?$this->parms["captcha_prime"]:null;
        //check
        $err = false;
        if ($comment_author == "") {
            Logger::addError(_("author's name is empty"));
            $err = true;
        }
        if ($comment_body == "") {
            Logger::addError(_("your message is useless"));
            $err = true;
        }
        if (!isset($captcha_prime) or ($captcha_prime != self::nearest_prime_number($captcha_gthan))) {
            Logger::addError(_("your prime number is not valid"));
            $err = true;
        }
        // do if no errors
        if (!$err) {
            $c = Comment::create_new($page_id, $comment_author, $comment_body, $comment_time);
            Logger::addMsg(_("comment is created: ") . Utils::link("down", "#comment".$c->id));
            // redirect
            self::redirect_to(Utils::path("page", "view", array("id" => $p->id)));
            return;
        }
        $this->parms["comment_author"] = Validator::nonhtml($this->parms["comment_author"]);
        PageAction::render_detailed_page(array_merge($this->parms, array("page" => $p)));
    }

    public function remove(){
        // data
        $id = Validator::numeric($this->parms["id"]);
        $c = Comment::get_present($id);
        // do
        Comment::remove($c);
        Logger::addMsg(sprintf(_("comment by %s is removed"), $c->author));
        // out
        self::redirect_to(Utils::path("page", "view", array("id" => $c->page_id)));
    }

    static function listen_page_oncreate_onedit($parms = null) {
        $p = $parms["page"];
        if ($parms["comments_lock"] == 1) {
            Comment::lock_page($p);
        } else {
            Comment::unlock_page($p);
        }
    }

    static function listen_page_remove($parms = null) {
        $p = $parms["page"];
        Comment::remove_page_comments($p);
    }

    public static function render_page_comments($parms = null) {
        $p = $parms["page"];
        $cs = Comment::get_comments_for_page($p);
        $count = count($cs);
        // out        
        printf("<h4>". _("%s comment(s)") .  "</h4>", $count);
        if ($count > 2 ) {
            printf('<div>' . Utils::link_click(_("sort comments"), "toggleSorting('comments')", "#asc-desc-comments") . '</div>');
        }
        printf('<div id="comments">');        
        foreach ($cs as $c) {
            self::render_comment(array("comment" => $c));
        }
        if (!Comment::is_page_locked($p)) {
            self::render_comment_form($parms);
        } else {
            printf("<h4>"._("commenting is disabled")."</h4>");
        }
        printf('</div>');
    }

    public static function render_countof_comments($parms = null) {
        $p = $parms["page"];
        $cs = Comment::get_comments_for_page($p);
        $count = count($cs);
        // out
        echo '<h6>' . Utils::link(sprintf(_("%s comment(s)"), $count), Utils::path("page", "view", array("id" => $p->id))) . '</h6>';
    }

    public static function render_comment($parms = null) {
        $c = $parms["comment"];
        $comment_id = $c->id;
        $comment_author = Validator::nonhtml($c->author);
        $comment_time = preg_replace('/(\d{4})-(\d{1,2})-(\d{1,2})(.*)/', '\1-\2-\3', $c->time);
        $comment_body = Format::restricted(Validator::nonhtml($c->body));

        $comment_ctl = UserAction::ctl(array(
                                             "remove" => Utils::clink("x", Utils::path("comment", "remove", array("id" => $c->id))),
                                             ));;
        include "tpl/comment-block.tpl";
    }

    public static function render_comment_form($parms = null) {
        $comment_action = Utils::path("comment", "oncreate");
        $p = $parms["page"];
        $page_id = $p->id;
        $comment_author = isset($parms["comment_author"])?$parms["comment_author"]:"";
        $comment_author = ($comment_author == "")?UserAction::get_user()->name:$comment_author;
        $comment_body = isset($parms["comment_body"])?$parms["comment_body"]:"";
        $captcha_gthan = rand(1, 96);

        include "tpl/comment-form.tpl";
    }

    public static function render_comment_subform($parms = null) {
        $p = $parms["page"];
        $format = "<h4>%s</h4><div>%s <input type=\"checkbox\" name=\"comments_lock\" value=\"1\" %s /></div>";
        $checked = "";
        if ($p && Comment::is_page_locked($p)) {
            $checked = "checked";
        }
        printf($format, _("comments"), _("disable comments"), $checked);
    }

    public static function render_last_comments($parms = null) {
        // data        
        $cs = Comment::get_last_uniq_comments(5);        
        $comments = array();
        foreach ($cs as $c) {
            $p = Page::get_present($c->page_id);
            $title = $p->title;
            $comments[] = array("page_title" => Utils::link($title, Utils::path("page", "view", array("id" => $p->id))),
                                "comment_author" => $c->author,
                                "comment_time" => $c->time,
                                "comment_body" => Format::restricted($c->body));
        }
        // out
        include "tpl/comment-last.tpl";
    }

    static function nearest_prime_number($gthan) {
        foreach (self::$prime_numbers as $n) {
            if ($n > $gthan) {
                return $n;
            }
        }
        return false;
    }

}

?>
