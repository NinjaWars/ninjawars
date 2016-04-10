<?php
namespace NinjaWars\core\control;

use NinjaWars\core\data\Item;
use NinjaWars\core\data\PurchaseOrder;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Inventory;
use NinjaWars\core\extensions\SessionFactory;

/**
 * Handles all user actions related to the in-game Shop
 */
class ShopController { // extends Controller
	const ALIVE = true;  // *** must be alive to access the shop ***
	const PRIV  = false; // *** do not need to be logged in ***

    const MARKUP = 1.5;

	protected $itemCosts   = [];

	/**
	 * Grabs data from external state for other methods to us
	 */
	public function __construct() {
		$this->itemCosts   = $this->itemForSaleCosts();
	}

	/**
	 * Display the initial shop view
	 *
	 * @return Array
	 */
	public function index() {
		$parts = array(
			'view_part' => 'index',
		);

		return $this->render($parts);
	}

    /**
     * Calculate price of items with markup.
     */
    private function calculatePrice($purchase_order){
        $item_costs        = $this->itemForSaleCosts();
        $potential_cost    = (isset($item_costs[$purchase_order->item->identity()]['item_cost']) ? $item_costs[$purchase_order->item->identity()]['item_cost'] : null);
        $current_item_cost = first_value($potential_cost, 0);
        return (int) ceil($current_item_cost * $purchase_order->quantity * self::MARKUP);

    }

	/**
	 * Command for current user to purchase a quantity of a specific item
	 *
	 * @param quantity int The quantity of the item to purchase
	 * @param item string The identity of the item to purchase
	 * @return Array
	 */
	public function buy() {
		$in_quantity       = in('quantity');
		$in_item           = in('item');
        $player            = Player::find(SessionFactory::getSession()->get('player_id'));
		$gold              = ($player ? $player->gold : null);
		$current_item_cost = 0;
		$no_funny_business = false;
        $no_such_item      = false;
		$item_costs        = $this->itemForSaleCosts();
		$item              = Item::findByIdentity($in_item);
		$quantity 		   = whichever(positive_int($in_quantity), 1);
		$item_text 	       = null;
        $valid             = false;

		if ($item instanceof Item) {
			$item_text = ($quantity > 1 ? $item->getPluralName() : $item->getName());
			$purchase_order = new PurchaseOrder();

			// Determine the quantity from input or as a fallback, default of 1.
			$purchase_order->quantity = $quantity;
			$purchase_order->item     = $item;
            $current_item_cost = $this->calculatePrice($purchase_order);

			if (!$player || !$purchase_order->item || $purchase_order->quantity < 1) {
				$no_such_item = true;
			} else if ($gold >= $current_item_cost) { // Has enough gold.
				try {
                    $inventory = new Inventory($player);
					$inventory->add($purchase_order->item->identity(), $purchase_order->quantity);
                    $player->set_gold($player->gold - $current_item_cost);
                    $player->save();
                    $valid = true;
				} catch (\Exception $e) {
					$invalid_item = $e->getMessage();
					error_log('Invalid Item attempted :'.$invalid_item);
					$no_funny_business = true;
				}
			}
		} else {
			$no_such_item = true;
		}

		$parts = array(
			'current_item_cost' => $current_item_cost,
			'quantity'          => $quantity,
			'item_text'         => $item_text,
			'no_funny_business' => $no_funny_business,
            'no_such_item'      => $no_such_item,
            'valid'             => $valid,
			'view_part'         => 'buy',
		);

		return $this->render($parts);
	}

	/**
	 * Generates the view spec hash for displaying a template
	 *
	 * @param p_parts Array Name/Value pairings to pass to the view
	 * @return Array
	 */
	private function render($p_parts) {
        $player = Player::find(SessionFactory::getSession()->get('player_id'));

		$p_parts['gold']          = ($player ? $player->gold : 0);
		$p_parts['item_costs']    = $this->itemCosts;
		$p_parts['authenticated'] = SessionFactory::getSession()->get('authenticated');

		return [
			'template' => 'shop.tpl',
			'title'    => 'Shop',
			'parts'    => $p_parts,
			'options'  => [ 'quickstat' => 'viewinv' ],
		];
	}

    /**
     * Pulls the shop items costs and all.
     */
    private function itemForSaleCosts() {
        $sel = 'select item_display_name, item_internal_name, item_cost, image, usage from item where for_sale = TRUE order by image asc, item_cost asc';

        if (defined('DEBUG') && DEBUG) {
            $sel = 'select item_display_name, item_internal_name, item_cost, image, usage from item order by image asc, item_cost asc';
        }

        $items_data = query_resultset($sel);
        // Rearrange the array to use the internal identity as indexes.
        $item_costs = array();

        foreach ($items_data as $item_data) {
            $item_costs[$item_data['item_internal_name']] = $item_data;
        }

        return $item_costs;
    }
}
