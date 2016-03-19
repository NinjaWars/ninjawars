<?php
namespace NinjaWars\core\data;

/**
 * Player Accounts and their info
 */
class Account {
    public static $fields = [
        'account_id',
        'account_identity',
        'phash',
        'active_email',
        'type',
        'operational',
        'created_data',
        'last_login',
        'last_login_failure',
        'karma_total',
        'last_ip',
        'confirmed',
        'verification_number',
        'oauth_provider',
        'oauth_id',
    ];

	public function __construct($data = []) {
        $this->info = $data;

        foreach (self::$fields AS $field) {
            $this->$field = (isset($data[$field]) ? $data[$field] : null);
        }
	}

    /**
     * Get an account object by id
     *
     * @param int $account_id
     * @return Account|null
     */
    public static function findById($account_id) {
        $data = account_info($account_id);

        if (isset($data['account_identity']) && !empty($data['account_identity'])) {
            return new Account($data);
        } else {
            return null;
        }
    }

    /**
     * Get an account object by email
     *
     * @param String $email_identity
     * @return Account|null
     */
	public static function find($email_identity) {
        $account_info = query_row('select * from accounts where account_identity = :identity_email',
            [':identity_email'=>$email_identity]
        );

		return self::findById($account_info['account_id']);
	}

    /**
     * Get the account that matches an oauth id.
     *
     * @param int $oauth_id
     * @param String $provider (optional) Defaults to facebook
     * @return Account|null
     */
	public static function findAccountByOauthId($oauth_id, $provider='facebook'){
        $account_info = query_row(
            'SELECT * FROM accounts WHERE (oauth_id = :id AND oauth_provider = :provider) ORDER BY operational, type, created_date ASC LIMIT 1',
            [
                ':id'       => positive_int($accountId),
                ':provider' => $provider,
            ]
        );

		if (empty($account_info) || !$account_info['account_id']) {
			return null;
		} else {
            return self::findById($account_info['account_id']);
		}
	}

    /**
     * Get an account for a character
     *
     * @param Character $char
     * @return Account
     */
    public static function findByChar(Character $char) {
        $query = 'SELECT account_id FROM accounts
            JOIN account_players ON _account_id = account_id
            JOIN players ON _player_id = player_id
            WHERE players.player_id = :pid';

        return self::findById(query_item($query, [':pid' => $char->id()]));
    }

    /**
     * Find account by active_email (as opposed to identity)
     *
     * @param String $email
     * @return Account|null
     */
    public static function findByEmail($email) {
        $normalized_email = strtolower(trim($email));

        if ($normalized_email === '') {
            return null;
        }

        $query = 'SELECT account_id FROM accounts WHERE lower(active_email) = lower(:email) LIMIT 1';

        return self::findById(query_item($query, [':email' => $normalized_email]));
    }

    /**
     * Get the Account by a ninja name (aka player.uname).
     *
     * @param String $ninja_name
     * @return Account
     */
    public static function findByNinjaName($ninja_name) {
        $query = 'SELECT account_id FROM accounts
            JOIN account_players ON account_id = _account_id
            JOIN players ON player_id = _player_id
            WHERE lower(uname) = lower(:ninja_name) LIMIT 1';

        return self::findById(query_item($query, [':ninja_name'=>$ninja_name]));
    }

	public function info() {
		return $this->info;
	}

    public function getId() {
        return $this->account_id;
    }

    /**
     * Simple wrapper function for getting email from accounts
     *
     * @return String email of the account
     */
    public function email() {
        return $this->getActiveEmail();
    }

    /**
     * Alias for getId()
     *
     * @return int
     */
    public function id() {
        return $this->getId();
    }

	public function getActiveEmail() {
		return $this->active_email;
	}

	public function getLastLogin() {
		return $this->info['last_login'];
	}

	public function getLastLoginFailure() {
		return $this->info['last_login_failure'];
	}

	public function getKarmaTotal() {
		return $this->info['karma_total'];
	}

    public function setKarmaTotal($p_amount) {
        $this->info['karma_total'] = (int) $p_amount;
    }

	public function getLastIp() {
		return $this->info['last_ip'];
	}

	/**
	 * Identity wrapper.
	 */
	public function identity() {
		return $this->getIdentity();
	}

	public function getIdentity() {
		return $this->account_identity;
	}

	public function getType() {
		return $this->type;
	}

	public function setType($type) {
		$cast_type = positive_int($type);

		if ($cast_type != $type) {
			throw new \Exception('Account: The account type set was inappropriate.');
		}

		$this->type = $cast_type;

		return $this->type;
	}

	public function setOauthId($id, $provider='facebook') {
		$this->oauth_id = $id;
		if($provider){
			$this->oauth_provider = $provider;
		}
		return true;
	}

	public function getOauthId($provider='facebook') {
		return $this->oauth_id;
	}

	public function getOauthProvider() {
		return $this->oauth_provider;
	}

	public function setOauthProvider($provider) {
		return ($this->oauth_provider = $provider);
	}

	/**
	 * Check operational status of account
	 */
	public function isOperational() {
		return (bool) ($this->info['operational'] === true);
	}

	/**
	 * Check whether an account is confirmed.
	 */
	public function isConfirmed() {
		return (bool) ($this->info['confirmed'] === 1);
	}

    /**
     * Change the account password
     *
     * @param String $newPassword
     * @return int Number of rows updated
     */
    public function changePassword($new_password) {
        $query = "UPDATE accounts SET phash = crypt(:password, gen_salt('bf', 10)) WHERE account_id = :account_id";

        return update_query(
            $query,
            [
                ':account_id' => $this->getId(),
                ':password'   => $new_password,
            ]
        );
    }

}
