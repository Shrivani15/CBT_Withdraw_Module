<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../Config/Database.php";
require_once __DIR__ . "/../Repository/AccountRepository.php";
require_once __DIR__ . "/../Repository/TransactionRepository.php";
require_once __DIR__ . "/../Service/TransactionService.php";

class TransactionController
{
    private AccountRepository $account_repository;
    private TransactionRepository $transaction_repository;

    /**
     * Constructor.
     * @return void
     */
    public function __construct()
    {
        $database = new Database();

        $this->account_repository = new AccountRepository($database);

        $this->transaction_repository = new TransactionRepository($database);
    }

    /**
     * Returns transaction details.
     * GET /transactions
     * @return void
     */
    public function transactions()
    {
        if (array_key_exists("account_number", $_GET)) {

            $account = $this->account_repository->getAccount((int)$_GET["account_number"]);

            if ($account === null) {

                echo json_encode(["status" => false, "message" => "Account Not Found"]);

                return;
            }

            if (!$this->validateAccount($account)) {
                return;
            }

            $transactions = $this->transaction_repository->getTransactions($account->getId());

        } else {

            $transactions = $this->transaction_repository->getTransactions();
        }

        echo json_encode(["status" => true, "transactions" => $transactions]);
    }

    /**
     * Returns transaction count.
     * GET /transactions/count
     * @return void
     */
    public function transactionCount()
    {
        $date = $_GET["date"] ?? null;

        $transaction = $this->transaction_repository->getTransactionCount($date);

        echo json_encode(["status" => true, "data" => $transaction]);
    }

    /**
     * Validates account status.
     * @param Account $_account
     * @return bool
     */
    private function validateAccount(Account $_account): bool
    {
        if ($_account->getIsLocked()) {

            echo json_encode(["status" => false, "message" => "Account Blocked"]);

            return false;
        }

        return true;
    }
}