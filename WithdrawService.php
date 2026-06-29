<?php

require_once "Account.php";
require_once "BankingService.php";
require_once "TransactionService.php";
require_once "AccountRepository.php";

class WithdrawService extends BankingService
{
	private TransactionService $transaction_service;
	private int $remaining_balance;

	/**
	 * Constructor for this class.
	 * @param TransactionService $_transaction_serivce
	 * @return void
	 */
	public function __construct(TransactionService $_transaction_serivce) {
		$this->transaction_service = $_transaction_serivce;
	}

	/*
	 * Performs withdraw service.
	 * @param Account $_accounts
	 * @return void
	 */
	#[Override]
	public function service(Account $_accounts)
	{
		$withdraw_amount = (int) readline("Enter Withdrawal Amount: ");

		$this->withdraw($_accounts, $withdraw_amount);
	}

	/**
	 * Performs withdrawal.
	 * @param Account $_accounts
	 * @param int $_withdraw_amount
	 * @return void
	 */
	public function withdraw(Account $_accounts, int $_withdraw_amount)
	{
		if ($this->transaction_service->getTodayTransactionCount($_accounts->getAccountNumber()) >= 3) {
			echo "Maximum 3 transactions allowed per day.\n";

			return;
		}

		$this->remaining_balance = $_accounts->getBalance() - $_withdraw_amount;

		$error_message = $this->validateWithdrawal($_accounts, $_withdraw_amount);

		if ($error_message !== null) {
			echo $error_message . "\n";

			return;
		}

		$this->performWithdrawal($_accounts, $_withdraw_amount);
	}

	/**
	 * Validates withdrawal conditions.
	 * @param Account $_accounts
	 * @param int $_withdraw_amount
	 * @return string|null
	 */
	private function validateWithdrawal(Account $_accounts, int $_withdraw_amount) {
		$error_message = null;

		$minimum_balance = Account::LIMITS[$_accounts->getAccountType()]['minimum_balance'];
		$maximum_limit = Account::LIMITS[$_accounts->getAccountType()]['maximum_limit'];
		$today_withdrawal_amount = $this->transaction_service->getTodayWithdrawalAmount($_accounts->getAccountNumber());

		if($_withdraw_amount % 100 != 0){
			
			$error_message = "Withdraw amount must be multiples of 100";
		}
		elseif ($_withdraw_amount > $_accounts->getBalance()) {

			$error_message = "Insufficient Balance.";
		} elseif ($today_withdrawal_amount + $_withdraw_amount > $maximum_limit) {

			$error_message = "Amount entered exceeded daily limit of your account.";
		} elseif ($this->remaining_balance < $minimum_balance) {

			$error_message = "Need to maintain a minimum balance in your account.";
		}

		return $error_message;
	}

	/**
	 * Performs withdrawal and updates account.
	 * @param Account $_accounts
	 * @param int $_withdraw_amount
	 * @return void
	 */
	private function performWithdrawal(Account $_accounts, int $_withdraw_amount) {
		echo "WITHDRAWAL SUCCESSFUL\n";

		$this->transaction_service->saveTransaction($_accounts->getAccountNumber(), $_withdraw_amount, $this->remaining_balance);

		$_accounts->setBalance($this->remaining_balance);
		$account_repository = new AccountRepository(new Database());

		$account_repository->saveAccount($_accounts);

		return "UserName : " .$_accounts->getUserName()."\nAccount Number : " . $_accounts->getAccountNumber() . "\nAccount Type : " .$_accounts->getAccountType() . "\nPhone Number : ". $_accounts->getPhoneNumber(). "\nWithdraw Amount : $_withdraw_amount\nRemaining Balance :". $_accounts->getBalance()."\n";
	}
}
?>