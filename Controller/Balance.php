<?php

header("Content-Type: application/json");

require_once "../Config/Database.php";
require_once "../Model/Account.php";
require_once "../Repository/AccountRepository.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);
    echo json_encode(["status" => false, "message" => "Method Not Allowed. Use GET."]);

    exit;
}

if (!array_key_exists("account_number", $_GET)) {
    echo json_encode(["status" => false, "message" => "Account Number Required"]);

    exit;
}
$database = new Database();

$account_repository = new AccountRepository($database);
$account_status = $account_repository->getAccountStatus((int)$_GET["account_number"]);
if ($account_status["account_status"] === "Blocked") {

    echo json_encode([
        "status" => false,
        "message" => "Account Blocked"
    ]);

    exit;
} else {

    $account = $account_repository->getAccount($_GET["account_number"]);

    if ($account === null) {
        echo json_encode(["status" => false, "message" => "Account Not Found"]);

        exit;
    }

    echo json_encode(["status" => true, "account_number" => $account->getAccountNumber(), "user_name" => $account->getUserName(), "account_type"=>$account->getAccountType()->getAccountType(),"phone_number" => $account->getPhoneNumber(),"balance" => $account->getBalance()]);
}