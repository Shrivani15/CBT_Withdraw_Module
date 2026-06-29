<?php

session_start();

header("Content-Type: application/json");

require_once "../Database.php";
require_once "../Account.php";
require_once "../AccountRepository.php";
require_once "../TransactionRepository.php";
require_once "../TransactionService.php";
require_once "../WithdrawService.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["status" => false, "message" => "Method Not Allowed. Use POST."]);

    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!array_key_exists("account_number", $data) || !array_key_exists("pin", $data)) {
	echo json_encode(["status" => false, "message" => "Invalid Request"]);

	exit;
}

$database = new Database();

$account_repository = new AccountRepository($database);

$account = $account_repository->getAccount($data["account_number"]);

if ($account === null) {

	echo json_encode(["status" => false, "message" => "Account Not Found"]);

	exit;
}

$transaction_service = new TransactionService(new TransactionRepository($database));

$withdraw_service = new WithdrawService($transaction_service);

if (!$withdraw_service->login($account,$data["pin"])) {

	echo json_encode(["status" => false, "message" => $account->getIsLocked() ? "Account Blocked" : "Invalid PIN", "attempts_left" => $_SESSION["attempts"] ?? 0 ]);

	exit;
}

$_SESSION["account_number"] = $account->getAccountNumber();

echo json_encode(["status" => true, "message" => "Authentication Successful"]);