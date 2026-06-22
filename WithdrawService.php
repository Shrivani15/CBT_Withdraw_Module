<?php

require_once "Account.php";
require_once "BankingService.php";
require_once "TransactionHandler.php";

class WithdrawService extends BankingService
{
	private TransactionHandler $transaction_handler;
	private int $remaining_balance;

	/**
	 * Constructor for this class.
	 * @param TransactionHandler $_transaction_handler
	 * @return void
	 */
	public function __construct(TransactionHandler $_transaction_handler) {
		$this->transaction_handler = $_transaction_handler;
	}

	/**
	 * Performs withdraw service.
	 * @param Account $_accounts
	 * @return void
	 */
	#[Override]
	public function service(Account $_accounts) {
		if ($this->transaction_handler->getTodayTransactionCount($_accounts->getAccountNo()) >= 3 ) {
			echo "Maximum 3 transactions allowed per day.\n";

			return;
		}

		$withdraw_amount = (int) readline("Enter Withdrawal Amount: ");

		$this->remaining_balance = $_accounts->getBalance() - $withdraw_amount;

		$error_message = $this->validateWithdrawal($_accounts, $withdraw_amount);

		if ($error_message !== null) {
			echo $error_message . "\n";

			return;
		}
		$this->performWithdrawal($_accounts, $withdraw_amount);
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
		$today_withdrawal_amount = $this->transaction_handler->getTodayWithdrawalAmount($_accounts->getAccountNo());

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

		$this->transaction_handler->saveTransaction($_accounts->getAccountNo(), $_withdraw_amount, $this->remaining_balance);

		$_accounts->setBalance($this->remaining_balance);
		$_accounts->saveAccount();

		echo "UserName : " .$_accounts->getUserName()."\nAccount Number : " . $_accounts->getAccountNo() . "\nAccount Type : " .$_accounts->getAccountType() . "\nPhone Number : ". $_accounts->getPhoneNumber(). "\nWithdraw Amount : $_withdraw_amount\nRemaining Balance :". $_accounts->getBalance()."\n";
	}
}
?>