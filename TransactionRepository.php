<?php

interface TransactionRepository
{
    /**
	 * Returns today's transactions(either withdraw or deposit) for the given account.
	 * @param int $_account_no Account Number
	 * @return array
	 */
	public function getTodayTransactions(int $_account_no);

    /**
	 * Saves a successful transaction.
	 * @param int $_account_no      Account Number,
	 * @param int $_amount          Amount withdrawn/Deposit,
	 * @param int $_balance         Account current balance
	 * @return void
	 */
	public function saveTransaction(int $_account_no, int $_amount, int $_balance);
}

?>