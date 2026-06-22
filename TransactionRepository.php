<?php

interface TransactionRepository
{
	public function getTodayTransactionCount(int $_account_no);

	public function saveTransaction(int $_account_no, int $_amount, int $_balance);
}

?>