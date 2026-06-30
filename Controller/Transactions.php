<?php

header("Content-Type: application/json");

require_once "../Config/Database.php";
require_once "../Repository/TransactionRepository.php";
require_once "../Repository/AccountRepository.php";
require_once "../Model/Account.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);
    echo json_encode(["status" => false, "message" => "Method Not Allowed. Use GET."]);

    exit;
}
$database = new Database();

$account_repository = new AccountRepository($database);

$transaction_repository = new TransactionRepository($database);

if (array_key_exists("account_number", $_GET)) {

    $account = $account_repository->getAccount((int)$_GET["account_number"]);

    if ($account === null) {
        echo json_encode(["status" => false, "message" => "Account Not Found"]);

        exit;
    }

    $transactions = $transaction_repository->getTransactions($account->getId());

} else {

    $transactions = $transaction_repository->getTransactions();
}

echo json_encode(["status" => true, "transactions" => $transactions]);