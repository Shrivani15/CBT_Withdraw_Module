<?php

session_start();

header("Content-Type: application/json");

require_once "../Database.php";
require_once "../Account.php";
require_once "../AccountRepository.php";
require_once "../TransactionRepository.php";
require_once "../TransactionService.php";
require_once "../WithdrawService.php";

if (!isset($_SESSION["account_number"])) {
    echo json_encode(["status" => false, "message" => "Please Authenticate First"]);

    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["amount"])) {
    echo json_encode(["status" => false, "message" => "Withdraw Amount Required"]);

    exit;
}

$database = new Database();

$account_repository = new AccountRepository($database);

$transaction_repository = new TransactionRepository($database);

$transaction_service = new TransactionService($transaction_repository);

$withdraw_service = new WithdrawService($transaction_service);

$account = $account_repository->getAccount($_SESSION["account_number"]);

if ($account === null) {

    echo json_encode(["status" => false, "message" => "Account Not Found"]);

    exit;
}

$withdraw_service->withdraw($account, $data["amount"]);

echo json_encode(["status" => true, "message" => "Transaction Completed"]);