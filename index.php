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

require_once "configenv.php";
require_once "configdb.php";
require_once 'core/logger.php';
require_once 'core/validator.php';
require_once "core/action.php";
require_once "core/entity.php";
require_once "core/db.php";
require_once 'core/utils.php';

require_once 'core/format.php';

require_once 'entity/config.php';
require_once 'entity/config-action.php';

require_once "entity/page.php";
require_once "entity/page-action.php";

require_once 'entity/user.php';
require_once 'entity/user-action.php';

// Tags
require_once "entity/tag.php";
require_once "entity/tag-action.php";

require_once "entity/comment.php";
require_once "entity/comment-action.php";

require_once 'entity/block.php';
require_once 'entity/block-action.php';

require_once 'entity/rss-action.php';

require_once 'entity/activity.php';
require_once 'entity/activity-action.php';

require_once 'core/box.php';
require_once "core/manager.php";

// inits
Format::init();

// apply listeners
ConfigAction::listen();
TagAction::listen();
CommentAction::listen();
BlockAction::listen();

Manager::add_action(new ConfigAction());
Manager::add_action(new PageAction());
Manager::add_action(new UserAction());
Manager::add_action(new TagAction());
Manager::add_action(new CommentAction());
Manager::add_action(new BlockAction());
Manager::add_action(new RssAction());
Manager::add_action(new ActivityAction());

# Запускаем процесс обработки экшна
Manager::manage();

?>
