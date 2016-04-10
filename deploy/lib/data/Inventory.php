<?php
namespace NinjaWars\core\data;

use NinjaWars\core\data\Player;
use \PDO;
use \IteratorAggregate;
use \ArrayIterator;

/**
 * Inventory for characters
 */
class Inventory implements IteratorAggregate {
    private $char  = null;
    private $items = null;

    public function __construct(Player $char) {
        $this->char = $char;
    }

    public function getIterator() {
        return new ArrayIterator(Inventory::of($this->char));
    }

    /**
     * Get inventory as flat array
     */
    public function toArray() {
        return Inventory::of($this->char);
    }

    /**
     * Add a certain number of items to a character's inventory
     *
     * @param string $identity
     * @param int $quantity
     */
    public function add($identity, $quantity = 1) {
        $quantity = (int)$quantity;

        if ($quantity > 0 && !empty($identity)) {
            $up_res = query_resultset(
                "UPDATE inventory SET amount = amount + :quantity
                WHERE owner = :char
                AND item_type = (select item_id from item where item_internal_name = :identity)",
                [
                    ':quantity' => $quantity,
                    ':char'     => $this->char->id(),
                    ':identity' => $identity,
                ]
            );

            $rows = $up_res->rowCount();

            if (!$rows) { // No entry was present, insert one.
                query_resultset(
                    "INSERT INTO inventory (owner, item_type, amount)
                    VALUES (:char, (SELECT item_id FROM item WHERE item_internal_name = :identity), :quantity)",
                    [
                        ':char'     => $this->char->id(),
                        ':identity' => $identity,
                        ':quantity' => $quantity
                    ]
                );
            }
        } else {
            throw new \Exception('Invalid item to add to inventory.');
        }
    }

    public function remove($identity, $quantity = 1) {
        DatabaseConnection::getInstance();
        $statement = DatabaseConnection::$pdo->prepare("UPDATE inventory SET amount = greatest(0, amount - :quantity) WHERE owner = :user AND item_type = (SELECT item_id FROM item WHERE lower(item_internal_name) = lower(:item)) AND amount > 0");
        $statement->bindValue(':user', $this->char->id());
        $statement->bindValue(':item', $identity);
        $statement->bindValue(':quantity', $quantity);
        $statement->execute();
    }

    /**
     * Get inventory list of a character
     */
    public static function of(Player $ch, $sort=null){
        if ($sort==='self') {
            $order = "ORDER BY self_use DESC, item_display_name";
        } else {
            $order = "ORDER BY item_internal_name = 'shuriken' DESC, other_usable desc, item_display_name";
        }

        $sql = "SELECT inventory.amount AS count,
            item_display_name AS name,
            item_display_name || plural AS display,
            item_internal_name AS identity,
            item.item_id, item_display_name,
            item_internal_name, item_type, image, usage, ignore_stealth,
            covert, self_use, other_usable, plural,
            traits
            FROM inventory join item on item_type = item.item_id
            WHERE owner = :owner ".$order;

        return query_array($sql, [':owner'=>[$ch->id(), PDO::PARAM_INT]]);
    }

    /**
     * Pull the counts of all items a player has.
     */
    public function counts() {
        $sql = "SELECT amount AS count, item_display_name AS name, item_internal_name, item_type, item.item_id, other_usable
            FROM inventory join item on item_type = item.item_id
            WHERE owner = :owner ORDER BY item_internal_name = 'shuriken' DESC, item_display_name";
        return query_array($sql, [':owner'=>[$this->char->id(), PDO::PARAM_INT]]);
    }

    /**
     * Get just the item count of a specific item.
     * @return int
     */
    public function amount($identity){
        $items_count = $this->counts();
        foreach($items_count as $item){
            if($item['item_internal_name'] === $identity){
                return $item['count'];
            }
        }
    }

    /**
     * Get the info of a specific item
     * @return array Of info for an item
     */
    public function infoFor($identity){
        $items_count = $this->counts();
        foreach($items_count as $item){
            if($item['item_internal_name'] === $identity){
                return $item;
            }
        }
        return null;
    }
}
