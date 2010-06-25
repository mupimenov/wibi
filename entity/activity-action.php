<?php
/*
 *      activity-action.php
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

class ActivityAction extends Action {
    
    public function BlockAction(){
        $this->entity_name = "activity";
        $this->available_funcs = array("viewall", "edit", "onedit", "refresh", "remove", "oncreate");
        $this->protected_funcs = array( "edit" => array("edit", "onedit", "refresh"),
                                        "remove" => array("remove"),
                                        "create" => array("oncreate"));
    }
    
    public function viewall() {
        self::render_all_activities();
    }
    
    public function edit() {
        $activity_id = Validator::numeric($this->parms["id"]);
        $a = Activity::get_present($activity_id);
        self::render_activity_edit_form(array("activity" => $a));
    }
    
    public function onedit() {
        $activity_id = Validator::numeric($this->parms["id"]);
        $a = Activity::get_present($activity_id);
        $a->url = $this->parms["activity_url"];
        
        $a_s = Activity::save($a);
        Logger::addMsg(sprintf(_("url <i>%s</i> is edited"), $a_s->url));
        self::redirect_to(Utils::path("activity", "editall"));
    }
    
    public function refresh() {
        $as = Activity::get_all_activities();
        $cs = self::curls_activities($as);
        
        for ($i = 0; $i < count( $as ); %i++) {
            $current_md5 = md5( $cs[$i] );
            if ( $current_md5 != $as[$i]->md5 ) {
                $as[$i]->md5 = $current_md5;
                Activity::save($as[$i]);
                Logger::addMsg( sprintf( _("refreshing activity with url <i>%s</i>"), $as[$i]->url ) );
            }
        }
        Logger::addMsg( _("activities have been refreshed") );
        self::redirect_to( Utils::path( "activity", "viewall" ) );
    }
    
    public function remove() {
        $activity_id = Validator::numeric($this->parms["id"]);
        $a = Activity::get_present($activity_id);
        if (Activity::remove($a)) {
            Logger::addMsg(sprintf(_("activity with url <i>%</i> is removed"), $a->url) );
        }
        // redirect
        self::redirect_to(Utils::path("activity", "viewall"));
    }
    
    public function oncreate() {
        $activity_url = $this->parms["activity_url"];        
        $a = Activity::create_new($activity_url, "asd");
        Logger::addMsg(sprintf(_("activity with url <i>%</i> is created"), $a->url));
        // redirect
        self::redirect_to(Utils::path("activity", "viewall"));
    }
    
    static function curls_activities($as) {
    
        $master = curl_multi_init( );
        $cis = array();
        
        foreach ( $as as $a ) {
            /* Здесь задача параллелится */
            $ci = curl_init( $a->url );
            curl_setopt( $ci, CURLOPT_RETURNTRANSFER, true );
            curl_multi_add_handle( $master, $ci );
            $cis[] = $ci;
        }
        
        /* погнали по страницам */
        do {
            curl_multi_exec( $master, $running );
        } while ( $running > 0 );
        
        /* содержимое страниц */
        $curl_out = array( );  
        foreach ($cis as $ci) {
            $curl_out[] = curl_multi_getcontent  ( $ci );
        }
        
        return $curl_out;
    }
    
    public static function render_all_activities($parms = null) {
    
        $as = Activity::get_all_activities( );        
        $cs = self::curls_activities($as);

        /* вывод результатов */
        for ( $i = 0; $i < count( $cs ); $i++ ) {        
            
            $current_md5 = md5( $cs[$i] );
            
            preg_match('/<title>(.*)<\/title>/i', $cs[$i], $matches);
            $title = $matches[1];
            
            $new_activity = "";
            if ( $current_md5 != $as[$i]->md5 ) {
                $new_activity = "newactivity";
            }
            
            self::render_activity_block( array( "activity" => $as[$i],
                                                 "title" => $title,
                                                 "newactivity" => $new_activity ) );
        }
        /* вывести формочку */
        self::render_activity_create_form();
    }
    
    public static function render_activity_block($parms = null) {
        $a = $parms["activity"];
        $activity_id = $a->id;
        $activity_title = $parms["title"]; /* ссылка на сайт */
        $activity_new = $parms["newactivity"]; /* на сайте есть изменения */
        
        $activity_ctl = User::ctl( array ( 
            "edit" => Utils::link( _("edit"), Utils::path( "activity", "edit", array( "id" => $a->id ) ) ),
            "remove" => Utils::clink( "x", Utils::path( "activity", "remove", array( "id" => $a->id ) ) ) ) );
        
        include 'tpl/activity-block.tpl';
    }
    
    public static function render_activity_edit_form($parms = null) {
        $a = $parms["activity"];
        $activity_id = $a->id;
        $activity_url = $a->url;
        
        $activity_action = Utils::path("activity", "onedit");
        
        include 'tpl/activity-form.tpl';
    }
    
    public static function render_activity_create_form($parms = null) {
        
        $activity_id = "";
        $activity_url = "";
        
        $activity_action = Utils::path("activity", "oncreate");
        
        include 'tpl/activity-form.tpl';
    }
    
    

}
?>
