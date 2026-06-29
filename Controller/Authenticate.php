<?php

header("Content-Type: application/json");

require_once "../Config/Database.php";
require_once "../Model/Account.php";
require_once "../Repository/AccountRepository.php";
require_once "../Repository/TransactionRepository.php";
require_once "../Service/TransactionService.php";
require_once "../Service/WithdrawService.php";

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

if (!$withdraw_service->login($account, $data["pin"])) {

    if ($account->getIsLocked()) {

        echo json_encode(["status" => false, "message" => "Account Blocked"]);

    } else {

        echo json_encode(["status" => false, "message" => "Invalid PIN", "attempts_left" => $account->getAttempts()]);
    }

    exit;
}


echo json_encode(["status" => true, "message" => "Authentication Successful"]);