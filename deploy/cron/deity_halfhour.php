<?php
require_once(substr(__FILE__, 0, (strpos(__FILE__, 'cron/'))).'resources.php');
require_once(CORE.'base.inc.php');
require_once(LIB_ROOT.'data/DatabaseConnection.php');
require_once(CORE.'data/Deity.php');

use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\Deity;

Deity::tick(30);
