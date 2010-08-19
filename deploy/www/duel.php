<?php
require_once(LIB_ROOT."specific/lib_player.php");

$private    = false;
$alive      = false;
$quickstat  = false;
$page_title = "Duel Log";

include SERVER_ROOT."interface/header.php";

$stats          = membership_and_combat_stats();
$vicious_killer = $stats['vicious_killer'];
?>
<h1>Today's Duels</h1>

<div id='vicious-killer'>
    Current Fastest Killer: 
    <a id='vicious-killer-menu' href='player.php?player=<?php echo out($vicious_killer); ?>'><?php echo htmlentities($vicious_killer);?></a>
</div>

<h3>Duel Log</h3>

<?php
DatabaseConnection::getInstance();
$statement = DatabaseConnection::$pdo->query("SELECT dueling_log.*, attackers.player_id AS attacker_id, defenders.player_id AS defender_id FROM dueling_log JOIN players AS attackers ON attackers.uname = attacker JOIN players AS defenders ON defender = defenders.uname ORDER BY id DESC LIMIT 500");

if ($duel = $statement->fetch()) {
	echo "<ul id='duel-log'>";

	do {
		echo "<li>";
		echo render_template('player-link.tpl', array('username'=>$duel['attacker'], 'id'=>$duel['attacker_id']))." has dueled ".render_template('player-link.tpl', array('username'=>$duel['defender'], 'id'=>$duel['defender_id']))." and ".($duel['won'] ? 'won' : 'lost')." for {$duel['killpoints']} killpoints on {$duel['date']}";
		echo "</li>";
	} while ($duel = $statement->fetch());

	echo "</ul>";
}
else {
	echo "<p>No duels for today yet.</p>";
}

include SERVER_ROOT."interface/footer.php";
?>
