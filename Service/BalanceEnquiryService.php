<?php

require_once __DIR__ . "/../Model/Account.php";
require_once __DIR__ . "/BankingService.php";
require_once __DIR__ . "/../Repository/AccountRepository.php";
require_once __DIR__ . "/../Config/Database.php";

class BalanceEnquiryService extends BankingService
{
	/**
	 * Displays current account balance.
	 * @param Account $_accounts Account object
	 * @return void
     */

	#[Override]
	public function service(Account $_accounts)
	{
		echo "\nBALANCE DETAILS\n";

		echo "User Name : " .$_accounts->getUserName()."\nAccount Number : " . $_accounts->getAccountNumber() . "\nAccount Type : " .$_accounts->getAccountType()->getAccountType() . "\nBalance :". $_accounts->getBalance()."\n";
	}
}