<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../Config/Database.php";
require_once __DIR__ . "/../Repository/AccountRepository.php";
require_once __DIR__ . "/../Repository/TransactionRepository.php";
require_once __DIR__ . "/../Service/TransactionService.php";
require_once __DIR__ . "/../Service/WithdrawService.php";

class AccountController
{
    private AccountRepository $account_repository;
    private WithdrawService $withdraw_service;

    /**
     * Constructor.
     * @return void
     */
    public function __construct()
    {
        $database = new Database();

        $this->account_repository = new AccountRepository($database);

        $transaction_repository = new TransactionRepository($database);

        $transaction_service = new TransactionService($transaction_repository);

        $this->withdraw_service = new WithdrawService($transaction_service);
    }

    /**
     * Authenticates the user.
     * @return void
     */
    public function authenticate()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!array_key_exists("account_number", $data) || !array_key_exists("pin", $data)) {
            echo json_encode(["status" => false, "message" => "Invalid Request"]);

            return;
        }

        $account = $this->account_repository->getAccount((int)$data["account_number"]);

        if ($account === null) {
            echo json_encode(["status" => false, "message" => "Account Not Found"]);

            return;
        }

        if (!$this->withdraw_service->login($account, (int)$data["pin"])) {

            if ($account->getIsLocked()) {

                echo json_encode(["status" => false, "message" => "Account Blocked"]);

            } else {

                echo json_encode(["status" => false, "message" => "Invalid PIN", "attempts_left" => $account->getAttempts()]);
            }

            return;
        }

        echo json_encode(["status" => true, "message" => "Authentication Successful"]);
    }

    /**
     * Returns account balance.
     * @return void
     */
    public function balance()
    {
        if (!array_key_exists("account_number", $_GET)) {

            echo json_encode(["status" => false, "message" => "Account Number Required"]);

            return;
        }

        $account = $this->account_repository->getAccount((int)$_GET["account_number"]);

        if ($account === null) {

            echo json_encode(["status" => false, "message" => "Account Not Found"]);

            return;
        }

        if (!$this->validateAccount($account)) {
            return;
        }

        echo json_encode(["status" => true, "account_number" => $account->getAccountNumber(), "user_name" => $account->getUserName(), "account_type" => $account->getAccountType()->getAccountType(), "phone_number" => $account->getPhoneNumber(), "balance" => $account->getBalance()]);
    }

    /**
     * Performs withdrawal.
     * @return void
     */
    public function withdraw()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!array_key_exists("account_number", $data) || !array_key_exists("amount", $data)) {
            echo json_encode(["status" => false, "message" => "Invalid Request"]);

            return;
        }

        $account = $this->account_repository->getAccount((int)$data["account_number"]);

        if ($account === null) {

            echo json_encode(["status" => false, "message" => "Account Not Found"]);

            return;
        }

        if (!$this->validateAccount($account)) {
            return;
        }

        $error_message = $this->withdraw_service->withdraw($account, (int)$data["amount"]);

        if ($error_message !== null) {

            echo json_encode(["status" => false, "message" => $error_message]);

            return;
        }

        echo json_encode([
            "status" => true,
            "message" => "Withdrawal Successful",
            "data" => ["user_name" => $account->getUserName(), "account_number" => $account->getAccountNumber(), "account_type" => $account->getAccountType()->getAccountType(), "phone_number" => $account->getPhoneNumber(), "remaining_balance" => $account->getBalance()]
        ]);
    }

    /**
     * Validates whether the account is blocked.
     * @param Account $_account
     * @return bool
     */
    private function validateAccount(Account $_account) {
        if ($_account->getIsLocked()) {

            echo json_encode(["status" => false, "message" => "Account Blocked"]);

            return false;
        }

        return true;
    }
}