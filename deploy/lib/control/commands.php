<?php

// ********************* STATUS DEFINES MOVED TO STATUS_DEFINES.PHP FILE ******** //


// ********************* CLASS FUNCTIONS MOVED TO PLAYER OBJECT ******* //


// ************************************
// ********* HEALTH FUNCTIONS *********
// ************************************

function setHealth($who, $new_health) {
	$dbconn = DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare("UPDATE players SET health = :health WHERE uname = :user");
	$statement->bindValue(':health', $new_health);
	$statement->bindValue(':user', $who);
	$statement->execute();

	return $new_health;
}

function getHealth($who) {
	$dbconn = DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare("SELECT health FROM players WHERE uname = :user");
	$statement->bindValue(':user', $who);
	$statement->execute();

	return $statement->fetchColumn();
}

function changeHealth($who, $amount) {
	$amount = (int)$amount;

	if (abs($amount) > 0) {
		$dbconn = DatabaseConnection::getInstance();
		$statement = DatabaseConnection::$pdo->prepare("UPDATE players SET health = health + ".
		   "CASE WHEN health + :amount < 0 THEN health*(-1) ELSE :amount2 END ".
		   "WHERE uname  = :user");
		$statement->bindValue(':user', $who);
		$statement->bindValue(':amount', $amount);
		$statement->bindValue(':amount2', $amount);
		$statement->execute();
	}

	return getHealth($who);
}

function addHealth($who, $amount) {
	return changeHealth($who, $amount);
}

function subtractHealth($who, $amount) {
	return changeHealth($who, ((-1)*$amount));
}

// ************************************
// ************************************



// ************************************
// ********** GOLD FUNCTIONS **********
// ************************************

function getGold($who) {
	if(DEBUG){
		throw new Exception('Use of deprecated function "getGold" from commands.php, should be replaced with get_gold($char_id)');
	}
	return get_gold(get_char_id($who));
}

function changeGold($who, $amount) {
	if(DEBUG){
		throw new Exception('Use of deprecated function "changeGold" from commands.php, should be replaced with add_gold($char_id, $amount)');
	}
	return add_gold(get_char_id($who), $amount);
}

function addGold($who, $amount) {
	return changeGold($who, $amount);
}

function subtractGold($who, $amount) {
	return changeGold($who, ((-1)*$amount));
}

// ************************************
// ************************************



// ************************************
// ********** TURNS FUNCTIONS *********
// ************************************

function getTurns($who) {
	$dbconn = DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare("SELECT turns FROM players WHERE uname = :user");
	$statement->bindValue(':user', $who);
	$statement->execute();

	return $statement->fetchColumn();
}

function changeTurns($who, $amount) {
	$amount = (int)$amount;
	if (abs($amount) > 0) {
		$dbconn = DatabaseConnection::getInstance();
		$statement = DatabaseConnection::$pdo->prepare("UPDATE players SET turns = turns + ".
		   "CASE WHEN turns + :amount < 0 THEN turns*(-1) ELSE :amount2 END ".
		   "WHERE uname  = :user");
		$statement->bindValue(':amount', $amount);
		$statement->bindValue(':amount2', $amount);
		$statement->bindValue(':user', $who);
		$statement->execute();
    }

	return getTurns($who);
}

function addTurns($who, $amount) {
	return changeTurns($who, $amount);
}

function subtractTurns($who, $amount) {
	return changeTurns($who, ((-1)*$amount));
}

// ************************************
// ************************************



// ************************************
// ********** KILLS FUNCTIONS *********
// ************************************

function getKills($who) {
	$char_id = get_char_id($who);
	get_kills($char_id);
}

// Deprecated now.
function changeKills($who, $amount) {
 	return change_kills(get_char_id($who), $amount);
}

function addKills($who, $amount) {
	$char_id = get_char_id($who);
	update_levelling_log($who, $amount);
	return change_kills($char_id, $amount);
}

function subtractKills($who,$amount) {
	$amount = (int)$amount;
	$char_id = get_char_id($who);
	update_levelling_log($who, $amount);
	return change_kills($char_id, $amount);
}

function get_kills($char_id) {
	return query_item("SELECT kills FROM players WHERE player_id = :player_id", 
		array(':player_id'=>array($char_id, PDO::PARAM_INT)));
}

// Change the kills amount of a char.
function change_kills($char_id, $amount, $auto_level_check=true) {
	$amount = (int)$amount;
	if (abs($amount) > 0) {
		// Ignore changes that amount to zero.
		if($amount > 0 && $auto_level_check) {
			// For positive kill changes, check whether levelling occurs.
			level_up_if_possible($char_id, $auto_levelling=true);
		}
		DatabaseConnection::getInstance();

		$update = DatabaseConnection::$pdo->prepare("UPDATE players SET kills = kills + 
		   CASE WHEN kills + :amount1 < 0 THEN kills*(-1) ELSE :amount2 END
		   WHERE player_id = :player_id");
		$update->bindValue(':amount1', $amount);
		$update->bindValue(':amount2', $amount);
		$update->bindValue(':player_id', $char_id);
		$update->execute();
	}
	return get_kills($char_id);
}

// Update the levelling log with the increased kills.
function update_levelling_log($who, $amount){
	// TODO: This should be deprecated once we have only upwards kills_total increases, but for now I'm just refactoring.
	DatabaseConnection::getInstance();
	$amount = (int)$amount;
	if($amount == 0){
		return;
	}
	// *** UPDATE THE KILLS LOG *** //
	$record_check = '<';
	$add = true;
	if($amount>0){
		$record_check = '>';
		$add = false;
	}

	$statement = DatabaseConnection::$pdo->prepare(
		"SELECT * FROM levelling_log WHERE uname = :player AND killsdate = now() AND killpoints $record_check 0 LIMIT 1");  
	//Check for an existing record of either negative or positive types.
	$statement->bindValue(':player', $who);
	$statement->execute();
	
	$notYetANewDay = $statement->fetch();  //positive if todays record already exists
	if ($notYetANewDay != NULL) {
		// If an entry already exists, update it.
		$statement = DatabaseConnection::$pdo->prepare("UPDATE levelling_log SET killpoints = killpoints + :amount WHERE uname = :player AND killsdate = now() AND killpoints $record_check 0");  //increase killpoints
	} else {
		$statement = DatabaseConnection::$pdo->prepare(
			"INSERT INTO levelling_log (uname, killpoints, levelling, killsdate) VALUES (:player, :amount, '0', now())");  
		//create a new record for today
	}
	$statement->bindValue(':amount', $amount);
	$statement->bindValue(':player', $who);
	$statement->execute();
}


// ************************************
// ************************************



// ************************************
// ********** LEVEL FUNCTIONS *********
// ************************************

function getLevel($who) {
	DatabaseConnection::getInstance();

	$statement = DatabaseConnection::$pdo->prepare("SELECT level FROM players WHERE uname = :player");
	$statement->bindValue(':player', $who);
	$statement->execute();
	return $statement->fetchColumn();
}

function changeLevel($who, $amount) {
	$amount = (int)$amount;
	if (abs($amount) > 0) {
		DatabaseConnection::getInstance();

		$statement = DatabaseConnection::$pdo->prepare("UPDATE players SET level = level+:amount WHERE uname = :player");
		$statement->bindValue(':player', $who);
		$statement->bindValue(':amount', $amount);
		$statement->execute();

		// *** UPDATE THE LEVEL INCREASE LOG *** //

		$statement = DatabaseConnection::$pdo->prepare("SELECT * FROM levelling_log WHERE uname = :player AND killsdate = now()");
		$statement->bindValue(':player', $who);
		$statement->execute();

		$notYetANewDay = $statement->fetch();  //Throws back a row result if there is a pre-existing record.

		if ($notYetANewDay != NULL) {
			//if record already exists.
			$statement = DatabaseConnection::$pdo->prepare("UPDATE levelling_log SET levelling=levelling + :amount WHERE uname = :player AND killsdate=now() LIMIT 1");
			$statement->bindValue(':amount', $amount);
			$statement->bindValue(':player', $who);
		} else {	// if no prior record exists, create a new one.
			$statement = DatabaseConnection::$pdo->prepare("INSERT INTO levelling_log (uname, killpoints, levelling, killsdate) VALUES (:player, '0', :amount, now())");  //inserts all except the autoincrement ones
			$statement->bindValue(':amount', $amount);
			$statement->bindValue(':player', $who);
		}

		$statement->execute();
	}

	return getLevel($who);
}

function addLevel($who, $amount) {
	return changeLevel($who, $amount);
}

// ************************************
// ************************************


// ************************************
// ********* BOUNTY FUNCTIONS *********
// ************************************

function setBounty($who, $new_bounty) {
	$new_bounty = (int)$new_bounty;
	DatabaseConnection::getInstance();

	$statement = DatabaseConnection::$pdo->prepare("UPDATE players SET bounty = :bounty WHERE uname = :player");
	$statement->bindValue(':bounty', $new_bounty);
	$statement->bindValue(':player', $who);
	$statement->execute();

	return $new_bounty;
}

function getBounty($who) {
	$char_id = get_char_id($who);
	DatabaseConnection::getInstance();

	$statement = DatabaseConnection::$pdo->prepare("SELECT bounty FROM players WHERE player_id = :player");
	$statement->bindValue(':player', $char_id);
	$statement->execute();
	return $statement->fetchColumn();
}

function changeBounty($who, $amount) {
	$char_id = get_char_id($who);
	$amount = (int)$amount;

	if (abs($amount) > 0) {
		DatabaseConnection::getInstance();
		$statement = DatabaseConnection::$pdo->prepare("UPDATE players SET bounty = bounty+".
			"CASE WHEN bounty+:amount1 < 0 THEN bounty*(-1) ".
			"WHEN bounty+:amount2 > 5000 THEN (5000 - bounty) ".
			"ELSE :amount3 END ".
			"WHERE player_id = :player");
		$statement->bindValue(':player', $char_id);
		$statement->bindValue(':amount1', $amount);
		$statement->bindValue(':amount2', $amount);
		$statement->bindValue(':amount3', $amount);
		$statement->execute();
	}

	return getBounty($who);
}

function addBounty($who, $amount) {
	return changeBounty($who, $amount);
}

function subtractBounty($who, $amount) {
	return changeBounty($who, ((-1)*$amount));
}

function rewardBounty($bounty_to, $bounty_on) {
	$bounty = getBounty($bounty_on);

	setBounty($bounty_on, 0);  //Sets bounty to zero.
	$char_id = get_char_id($bounty_to);
	add_gold($char_id, $bounty);

	return $bounty;
}

function runBountyExchange($username, $defender) {  //  *** BOUNTY EQUATION ***
	// *** Bounty Increase equation: (attacker's level - defender's level) / 5, rounded down, times 25 gold per point ***
	$levelRatio     = floor((getLevel($username) - getLevel($defender)) / 5);

	$bountyIncrease = ($levelRatio > 0 ? $levelRatio * 25 : 0);	//Avoids negative increases.

	$bountyForAttacker = rewardBounty($username, $defender); //returns a value if bounty rewarded.
	if ($bountyForAttacker) {
		// *** Reward bounty whenever available. ***
		return "You have received the $bountyForAttacker gold bounty on $defender's head for your deeds!";
		$bounty_msg = "You have valiantly slain the wanted criminal, $defender! For your efforts, you have been awarded $bountyForAttacker gold!";
		sendMessage("Village Doshin", $username, $bounty_msg);
	} else if ($bountyIncrease > 0) {
		// *** If Defender has no bounty and there was a level difference. ***
		addBounty($username, $bountyIncrease);
		return "Your victim was much weaker than you. The townsfolk are angered. A bounty of  $bountyIncrease gold has been placed on your head!";
	} else {
		return null;
	}
}

// ************************************
// ************************************


// ************************************
// ******** INVENTORY FUNCTIONS *******
// ************************************

// DEPRECATED
// Add an item using the old display name
function addItem($who, $item, $quantity = 1) {
	$item_identity = item_identity_from_display_name($item);

	if ((int)$quantity > 0 && !empty($item) && $item_identity) {
		add_item(get_char_id($who), $item_identity, $quantity);
	} else {
		throw new Exception('Improper deprecated item addition request made.');
	}
}

// Add an item using it's database identity.
function add_item($char_id, $identity, $quantity = 1) {
	$quantity = (int)$quantity;
	if ($quantity > 0 && !empty($identity)) {
	    $up_res = query_resultset(
	        "UPDATE inventory SET amount = amount + :quantity 
	            WHERE owner = :char AND item_type = (select item_id from item where item_internal_name = :identity)",
	        array(':quantity'=>$quantity,
	            ':char'=>$char_id,
	            ':identity'=>$identity));
	    $rows = $up_res->rowCount();

		if (!$rows) { // No entry was present, insert one.
		    $ins_res = query_resultset("INSERT INTO inventory (owner, item_type, amount) 
		        VALUES (:char, (SELECT item_id FROM item WHERE item_internal_name = :identity), :quantity)",
		        array(':char'=>$char_id,
		            ':identity'=>$identity,
		            ':quantity'=>$quantity));
		}
	} else {
	    throw new Exception('Invalid item to add to inventory.');
	}
}

function remove_item($char_id, $identity, $quantity = 1) {
	$quantity = (int)$quantity;
	if ($quantity > 0 && !empty($identity)) {
	    $up_res = query_resultset(
			'UPDATE inventory SET amount = greatest(0, amount - :quantity)
	            WHERE owner = :char AND item_type = (SELECT item_id FROM item WHERE item_internal_name = :identity)'
	        , array(
				':quantity'   => $quantity
				, ':char'     => $char_id
				, ':identity' => $identity
			)
		);
	} else {
	    throw new Exception('Invalid item to add to inventory.');
	}
}

function removeItem($who, $item, $quantity=1) {
	DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare("UPDATE inventory SET amount = amount - :quantity WHERE owner = :user AND item_type = (select item_id from item where lower(item_display_name) = lower(:item)) AND amount > 0");
	$statement->bindValue(':user', $who);
	$statement->bindValue(':item', $item);
	$statement->bindValue(':quantity', $quantity);
	$statement->execute();
}

// ************************************
// ******** LOGGING FUNCTIONS *******
// ************************************


function sendLogOfDuel($attacker, $defender, $won, $killpoints) {
	$killpoints = (int)$killpoints;

	$dbconn = DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare("INSERT INTO dueling_log values 
        (default, :attacker, :defender, :won, :killpoints, now())");
        //Log of Dueling information.
	$statement->bindValue(':attacker', $attacker);
	$statement->bindValue(':defender', $defender);
	$statement->bindValue(':won', $won);
	$statement->bindValue(':killpoints', $killpoints);
	$statement->execute();
}

?>
