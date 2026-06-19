<?php

class Account
{
	//Constant data for minimum balance and maximum limit per day.
	public const LIMITS = [
        'Savings' => [
            'minimum_balance' => 500,
            'maximum_limit' => 20000
        ],
        'Current' => [
            'minimum_balance' => 400,
            'maximum_limit' => 10000
        ],
        'Salary' => [
            'minimum_balance' => 300,
            'maximum_limit' => 15000
        ]
    ];

	private int $account_no;
	private string $user_name;
	private int $phone_no;
	private string $account_type;
	private int $pin;
	private int $balance;
	private bool $is_locked;

	/**
	 * Saves account details.
	 * 
	 * @param int    $_account_no	Account Number
	 * @param string $_user_name	Account Holder Name
	 * @param int    $_phone_no		Account Holder phone number
	 * @param string $_account_type Account Type
	 * @param int    $_pin			Account pin
	 * @param int    $_balance		Current balance of the account
	 * @param bool   $_is_locked	Account is open or close to access
	 * @return void
	 */
	public function __construct(int $_account_no, string $_user_name, int $_phone_no, string $_account_type, int $_pin, int $_balance, bool $_is_locked) {
		$this->account_no = $_account_no;
		$this->user_name = $_user_name;
		$this->phone_no = $_phone_no;
		$this->account_type = $_account_type;
		$this->pin = $_pin;
		$this->balance = $_balance;
		$this->is_locked = $_is_locked;
	}

	/**
	 * Returns account object if account exists
	 *
	 * @param int $_account_no
	 * @return Account|null
	 */
	public static function getAccount(int $_account_no) {
		$accounts = json_decode(file_get_contents("AccountsData.json"), true);

		if (!array_key_exists($_account_no, $accounts,)) {
			return null;
		}

		$account_details = $accounts[$_account_no];

		return new Account($_account_no, $account_details['user_name'], $account_details['phone_no'], $account_details['account_type'], $account_details['pin'], $account_details['balance'], $account_details['is_locked']);
	}

	/**
	 * Saves account changes to JSON file
	 * 
	 * @return void
	 */
	public function saveAccount() {
		$accounts = json_decode(file_get_contents("AccountsData.json"), true);

		$accounts[$this->account_no] = ['user_name' =>  $this->user_name, 'phone_no' => $this->phone_no, 'account_type' => $this->account_type, 'pin' => $this->pin, 'balance' => $this->balance, 'is_locked' => $this->is_locked];

		file_put_contents("AccountsData.json", json_encode($accounts, JSON_PRETTY_PRINT));
	}

	//Getters and Setters of the declared variables
	public function getAccountNo() {

		return $this->account_no;
	}

	public function getUserName() {

		return $this->user_name;
	}

	public function getAccountType() {

		return $this->account_type;
	}

	public function getPhoneNumber() {

		return $this->phone_no;
	}

	public function getPin() {

		return $this->pin;
	}

	public function getBalance() {

		return $this->balance;
	}

	public function setBalance(int $_balance) {
		if ($_balance >= 0) {
			$this->balance = $_balance;
		}
	}

	public function getIsLocked() {

		return $this->is_locked;
	}

	public function setIsLocked() {
		$this->is_locked = true;
	}
}

?>