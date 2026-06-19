<?php
date_default_timezone_set("Asia/Kolkata");

require_once "Account.php";
require_once "WithdrawService.php";
require_once "TransactionHandler.php";

class Banking
{
	
	/**
	 * Process requested service until exit
	 *
	 * @return void
	 */
	public static function run() {
		$transaction_handler = new TransactionHandler();
		while (true) {
			echo "\nBANKING SERVICES\n 1. Withdrawal\n 2. View Balance\n 3. Exit\n";

			$choice = (int) readline("Select Service : ");

			switch ($choice) {
				case 1:
					$account = self::getAccountDetails();
					if($account === null) {

						break;
					}
					$service_requested = new WithdrawService($transaction_handler);
					$service_requested->start($account);
					break;

				case 2:
					$account = self::getAccountDetails();
					if ($account === null) {

						break;
					}
					self::showBalance($account);

					break;
				case 3:

					exit;
				default:
					echo "Invalid Service Option\n";

					exit;
			}

			$continue = strtolower(readline("Do you want to continue(Y/N): "));
			if (!($continue === 'y')) {

				break;
			}
		}
	}

	/**
	 * Displays balance details
	 *
	 * @param Account $_account
	 * @return void
	 */
	private static function showBalance(Account $_account) {
		echo "\nBALANCE DETAILS\nUser Name : " . $_account->getUserName() . "\nAccount Number : " . $_account->getAccountNo() . "\nAccount Type   : " . $_account->getAccountType() . "\nBalance : " . $_account->getBalance() . "\n";
	}

	/**
	 * Collects account number and validates
	 *
	 * @return Account|null
	 */
	private static function getAccountDetails() {
		$account_number = (int) readline("Enter Account Number: ");

		$account = Account::getAccount($account_number);
		if($account === null) {
			echo "Invalid Account\n";

			return null;
		}

		if (self::checkAccountOpen($account)) {
			echo "Account Found\n";

			return $account;
		}

		return null;
	}

	/**
	 * Checks whether the account is blocked or not
	 *
	 * @param Account $_account
	 * @return bool
	 */
	private static function checkAccountOpen(Account $_account) {
		if($_account->getIsLocked()) {
			echo "Your account has been blocked\n";

			return false;
		}
		return true;
	}
}

Banking::run();