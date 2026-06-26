<?php

require_once "TransactionOperation.php";
require_once "TransactionRepository.php";

class TransactionService implements TransactionOperation
{
	// /**
	//  * Loads transaction data from file.
	//  * @return array
	//  */
	// private function loadTransactions(){

	// 	return json_decode(file_get_contents("Transactions.json"), true) ?? [];
	// }

	private TransactionRepository $transaction_repository;

	/**
	 * Constructor.
	 * @param TransactionRepository $_transaction_repository
	 * @return void
	 */
	public function __construct(TransactionRepository $_transaction_repository) {
		$this->transaction_repository = $_transaction_repository;
	}

	// /**
	//  * Returns today's transactions for the given account.
	//  * @param int $_account_no Account Number
	//  * @return array
	//  */
	// public function getTodayTransactions(int $_account_no) {
	// 	$transactions = $this->loadTransactions();

	// 	if (!array_key_exists($_account_no, $transactions)) {

	// 		return [];
	// 	}

	// 	$today_date = date("Y-m-d");

	// 	$today_transactions = [];

	// 	foreach ($transactions[$_account_no] as $transaction) {
	// 		$transaction_date = substr($transaction["time"], 0, 10);
	// 		if ($transaction_date === $today_date) {
    // 			$today_transactions[] = $transaction;
	// 		}
	// 	}

	// 	return $today_transactions;
	// }

	/**
	 * Returns today's transaction count.
	 * @param int $_account_no
	 * @return int
	 */
	public function getTodayTransactionCount(int $_account_no)
	{
		return count($this->transaction_repository->getTodayTransactions($_account_no));
	}

	/**
	 * Returns today's withdrawal amount.
	 * @param int $_account_no
	 * @return int
	 */
	public function getTodayWithdrawalAmount(int $_account_no)
	{
		$total_amount = 0;

		foreach ($this->transaction_repository->getTodayTransactions($_account_no) as $transaction) {
			$total_amount += $transaction["withdraw_amount"];
		}

		return $total_amount;
	}

	/**
	 * Saves a successful transaction.
	 *
	 * @param int $_account_no
	 * @param int $_withdraw_amount
	 * @param int $_balance
	 * @return void
	 */
	public function saveTransaction(int $_account_no, int $_withdraw_amount, int $_balance)
	{
		$this->transaction_repository->saveTransaction($_account_no, $_withdraw_amount, $_balance);
	}

}