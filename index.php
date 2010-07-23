<?php

/*
 *      index.php
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

/***** CORE *****/
require_once "configenv.php";
require_once "configdb.php";
require_once 'core/logger.php';
require_once 'core/validator.php';
require_once "core/action.php";
require_once "core/entity.php";
require_once "core/db.php";
require_once 'core/utils.php';

/***** Textile *****/
require_once 'core/format.php';

/***** Config module *****/
require_once 'entity/config.php';
require_once 'entity/config-action.php';

/***** Pages (or Posts) *****/
require_once "entity/page.php";
require_once "entity/page-action.php";

/***** User rights *****/
require_once 'entity/user.php';
require_once 'entity/user-action.php';

/***** Tags *****/
require_once "entity/tag.php";
require_once "entity/tag-action.php";

/***** Comments *****/
// out of the box
require_once "entity/comment.php";
require_once "entity/comment-action.php";
// or Intense Debate
require_once 'entity/debate.php';
require_once 'entity/debate-action.php';

/***** Blocks on right side: counters or some another shit. *****/
require_once 'entity/block.php';
require_once 'entity/block-action.php';

/***** RSS *****/
require_once 'entity/rss-action.php';

/***** Activity is an experimental useless module. *****/
require_once 'entity/activity.php';
require_once 'entity/activity-action.php';

/***** Box is a group tool to render output data. *****/
require_once 'core/box.php';

/***** God *****/
require_once "core/manager.php";

/***** Init the Textile module. *****/
Format::init();

/***** Apply the listeners. *****/
ConfigAction::listen();
TagAction::listen();
ActivityAction::listen();
/***** Comments: CommentAction !OR! DebateAction *****/
//CommentAction::listen();
DebateAction::listen();
BlockAction::listen();

Manager::add_action(new ConfigAction());
Manager::add_action(new PageAction());
Manager::add_action(new UserAction());
Manager::add_action(new TagAction());
Manager::add_action(new CommentAction());
Manager::add_action(new DebateAction());
Manager::add_action(new BlockAction());
Manager::add_action(new RssAction());
Manager::add_action(new ActivityAction());

/***** God makes the choice. *****/
Manager::manage();

?>
