<?php

require_once "BankingService.php";
require_once "../Model/Account.php";

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

		echo "User Name : " .$_accounts->getUserName()."\nAccount Number : " . $_accounts->getAccountNumber() . "\nAccount Type : " .$_accounts->getAccountType() . "\nBalance :". $_accounts->getBalance()."\n";
	}
}