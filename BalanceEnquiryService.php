<?php

require_once "BankingService.php";
require_once "Account.php";

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

		echo "UserName : " .$_accounts->getUserName()."\nAccount Number : " . $_accounts->getAccountNo() . "\nAccount Type : " .$_accounts->getAccountType() . "\nBalance :". $_accounts->getBalance()."\n";
	}
}