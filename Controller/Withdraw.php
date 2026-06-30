<?php


header("Content-Type: application/json");

require_once "../Config/Database.php";
require_once "../Model/Account.php";
require_once "../Repository/AccountRepository.php";
require_once "../Repository/TransactionRepository.php";
require_once "../Service/TransactionService.php";
require_once "../Service/WithdrawService.php";

$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["status" => false, "message" => "Method Not Allowed. Use POST."]);

    exit;
}

if (!array_key_exists("account_number", $data) || !array_key_exists("amount", $data)) {
    echo json_encode(["status" => false, "message" => "Invalid Request"]);

    exit;
}

$database = new Database();

$account_repository = new AccountRepository($database);

$transaction_repository = new TransactionRepository($database);

$transaction_service = new TransactionService($transaction_repository);

$withdraw_service = new WithdrawService($transaction_service);

$account = $account_repository->getAccount($data["account_number"]);



if ($account === null) {

    echo json_encode(["status" => false, "message" => "Account Not Found"]);

    exit;
}

$error_message = $withdraw_service->withdraw($account, $data["amount"]);

if ($error_message !== null) {
    echo json_encode(["status" => false, "message" => $error_message]);

    exit;
}

echo json_encode(["status" => true, "message" => "Withdrawal Successful","data" => ["user_name" => $account->getUserName(), "account_number" => $account->getAccountNumber(), "account_type"=>$account->getAccountType()->getAccountType(), "phone_number" => $account->getPhoneNumber(), "remaining_balance" => $account->getBalance()]]);