<?php
require_once(DB_ROOT . "PlayerDAO.class.php");
require_once(DB_ROOT . "PlayerVO.class.php");
/* Player behavior object.
 *
 * This file should make use of a private PlayerVO.class.php and PlayerDAO.class.php
 * to propegate and save its data.
 *
 * @category    Template
 * @package     char
 * @subpackage	player
 * @author      Tchalvak <ninjawarsTchalvak@gmail.com>
 * @author
 * @link        http://ninjawars.net/player.php?player=tchalvak
*/

class Player
{
	public $player_id;
	public $vo;
	public $status;

	public function __construct($player_id_or_username) {
		if (!is_numeric($player_id_or_username)) {
			$sel = "SELECT player_id FROM players WHERE uname = :uname LIMIT 1";
			$this->player_id = DatabaseConnection::$pdo->prepare($sel);
			$this->player_id->bindValue(':uname', $player_id_or_username);
			$this->player_id->execute();
			$this->player_id = $this->player_id->fetchColumn();
		} else {
			$this->player_id = $player_id_or_username;
		}

		$dao = new PlayerDAO();
		$this->vo = $dao->get($this->player_id);
	}

	public function __toString() {
		return $this->vo->uname;
	}
	
	public function name(){
		return $this->vo->uname;
	}
	
	public function id(){
	    return $this->vo->player_id;
	}
	
	public function level(){
	    return $this->vo->level;
	}

	// Save the Player state.
	public function save() {
		$dao = new PlayerDAO();
		$dao->save($this->vo);
	}

	protected function queryStatus() {
		DatabaseConnection::getInstance();

		$statement = DatabaseConnection::$pdo->prepare("SELECT status FROM players WHERE player_id = :player");
		$statement->bindValue(':player', $this->player_id);
		$statement->execute();
		return $this->status = $statement->fetchColumn();
	}

	protected function getStatus() {
		return ($this->vo->status === null ? $this->queryStatus() : $this->vo->status);
	}

	public function addStatus($p_status) {
		if ((int)$p_status == $p_status && $p_status != 0) {
			if ($p_status < 0) {
				return $this->subtractStatus(abs($p_status));
			} else {
				$statement = DatabaseConnection::$pdo->prepare('UPDATE players SET status = status+:status1 WHERE player_id = :player AND status&:status2 = 0');
				$statement->bindValue(':player', $this->player_id, PDO::PARAM_INT);
				$statement->bindValue(':status1', $p_status, PDO::PARAM_INT);
				$statement->bindValue(':status2', $p_status, PDO::PARAM_INT);
				$statement->execute();

				$this->vo->status = null; // *** Ensures that the next call to hasStatus pulls the updated status from the DB ***
			}
		}
	}

	public function resetStatus() {
		$statement = DatabaseConnection::$pdo->prepare('UPDATE players SET status = 0 WHERE player_id = :player');
		$statement->bindValue(':player', $this->player_id, PDO::PARAM_INT);
		$statement->execute();

		$this->vo->status = 0;
	}

	public function subtractStatus($p_status) {
		if ((int)$p_status == $p_status && $p_status > 0) {
			$statement = DatabaseConnection::$pdo->prepare('UPDATE players SET status = status-:status1 WHERE player_id = :player AND status&:status2 <> 0');
			$statement->bindValue(':player', $this->player_id, PDO::PARAM_INT);
			$statement->bindValue(':status1', $p_status, PDO::PARAM_INT);
			$statement->bindValue(':status2', $p_status, PDO::PARAM_INT);
			$statement->execute();

			$this->vo->status = null; // *** Ensures that the next call to hasStatus pulls the updated status from the DB ***
		}
	}

	public function getStrength() {
		$str = $this->vo->strength;

		if ($this->hasStatus(STR_UP2)) {
			return $str+(ceil($str*.25));
		} elseif ($this->hasStatus(STR_UP1)) {
			return $str+(ceil($str*.12));
		} else {
			return $str;
		}
	}

	public function hasStatus($p_status) {
		return (bool)($this->getStatus()&$p_status);
	}

	public function isActive() {
		$activity_threshhold = 91;
		return ($this->vo->days < $activity_threshhold);
	}
	
	public function isAdmin(){
	    $name = strtolower($this->name());
	    if($name == 'tchalvak' || $name == 'beagle'){
	        return true;
	    }
	    return false;
	}

	public function death() {
		$this->resetStatus();
	}

	public function as_vo() {
		return $this->vo;
	}

	public function as_array() {
		return (array) $this->vo;
	}

	public function getClan() {
		DatabaseConnection::getInstance();
		$statement = DatabaseConnection::$pdo->prepare("SELECT clan_id, clan_name 
				FROM clan 
				JOIN clan_player ON clan_id = _clan_id 
				WHERE _player_id = :player");
		$statement->bindValue(':player', $this->player_id);
		$statement->execute();

		if ($data = $statement->fetch()) {
			$clan = new Clan($data['clan_id'], $data['clan_name']);
			return $clan;
		} else {
			return null;
		}
	}

    // Complex wrapper that allows for robust healing with a limit of the max health.	
	public function heal($amount){
	    $health = $this->health();
	    $max_health = $this->max_health();
	    
        if(($health+$amount)>$max_health){
            $amount = $max_health-$health;
        }
	    return $this->addHealth($amount);
	}
	
	// Simple wrapper for changeHealth
	public function addHealth($amount){
	    return $this->changeHealth($amount);
	}
	
	// Simple wrapper for subtractive action.
	public function subtractHealth($amount){
	    return $this->changeHealth((-1*(int)$amount));
	}
	
	// To subtract just send in a negative integer.
	public function changeHealth($add_amount){
    	$amount = (int)$add_amount;
    	$amount2 = $amount;
    	if (abs($amount) > 0) {
        	$id = $this->id();
            // Set health = 0 when it's less than zero, otherwise modify it.
    	    $up = "UPDATE players SET health = 
    		   CASE WHEN health + :amount < 0 THEN 0 ELSE health + :amount2 END 
    		   WHERE player_id  = :player_id";
    		query($up, array(':player_id'=>array($id, PDO::PARAM_INT),
    		    ':amount'=>$amount, ':amount2'=>$amount2));
    	}
    	return $this->health(); // Return the current health.
	}

    // Pull the current health.	
	public function health(){
	    $id = $this->id();
        $sel = "SELECT health from players where player_id = :id";
		return query_item($sel, array(':id'=>array($id, PDO::PARAM_INT)));
	}
	
	public function max_health(){
	    return determine_max_health($this->level());
	}
	
	// Return the current percentage health.
	public function health_percent(){
	    return health_percent($this->health(), $this->level());
	}
		
}
?>
