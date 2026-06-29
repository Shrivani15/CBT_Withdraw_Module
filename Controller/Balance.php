<?php

session_start();

header("Content-Type: application/json");

require_once "../Database.php";
require_once "../Account.php";
require_once "../AccountRepository.php";

if (!isset($_SESSION["account_number"])) {
    echo json_encode(["status" => false, "message" => "Please Authenticate First"]);
    
    exit;
}

$database = new Database();

$account_repository = new AccountRepository($database);

$account = $account_repository->getAccount($_SESSION["account_number"]);

if ($account === null) {
    echo json_encode(["status" => false, "message" => "Account Not Found"]);

    exit;
}

echo json_encode(["status" => true, "account_number" => $account->getAccountNumber(), "user_name" => $account->getUserName(), "account_type" => $account->getAccountType(), "phone_number" => $account->getPhoneNumber(),"balance" => $account->getBalance()]);