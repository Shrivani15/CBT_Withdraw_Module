<?php

class TransactionHandler
{
	/**
	 * Loads transaction data from file.
	 * @return array
	 */
	private function loadTransactions(){

		return json_decode(file_get_contents("Transactions.json"), true) ?? [];
	}

	/**
	 * Returns today's transactions for the given account.
	 * @param int $_account_no Account Number
	 * @return array
	 */
	private function getTodayTransactions(int $_account_no) {
		$transactions = $this->loadTransactions();

		if (!array_key_exists($_account_no, $transactions)) {

			return [];
		}

		$today_date = date("Y-m-d");

		$today_transactions = [];

		foreach ($transactions[$_account_no] as $transaction) {
			$transaction_date = substr($transaction["time"], 0, 10);
			if ($transaction_date === $today_date) {
    			$today_transactions[] = $transaction;
			}
		}

		return $today_transactions;
	}

	/**
	 * Returns today's transaction count for the given account.
	 * @param int $_account_no  Account Number
	 * @return int
	 */
	public function getTodayTransactionCount(int $_account_no) {

		return count($this->getTodayTransactions($_account_no));
	}

	/**
	 * Returns total amount withdrawn today.
	 * @param int $_account_no Account Number
	 * @return int
	 */
	public function getTodayWithdrawalAmount(int $_account_no){
		$total_amount = 0;

		foreach ($this->getTodayTransactions($_account_no) as $transaction) {
			$total_amount += $transaction["withdraw_amount"];
		}

		return $total_amount;
	}

	/**
	 * Saves a successful transaction.
	 * @param int $_account_no      Account Number,
	 * @param int $_withdraw_amount Amount withdrawn,
	 * @param int $_balance         Account current balance
	 * @return void
	 */
	public function saveTransaction(int $_account_no, int $_withdraw_amount, int $_balance) {
		$transaction = $this->loadTransactions();

		$transaction[$_account_no][] = ["withdraw_amount" => $_withdraw_amount, "balance_after" => $_balance, "time" => date("Y-m-d H:i:s")];

		file_put_contents("Transactions.json", json_encode($transaction, JSON_PRETTY_PRINT));
	}
}