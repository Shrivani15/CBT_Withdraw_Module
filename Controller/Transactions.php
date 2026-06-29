<?php

session_start();

header("Content-Type: application/json");

require_once "../Database.php";
require_once "../TransactionRepository.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);
    echo json_encode(["status" => false, "message" => "Method Not Allowed. Use GET."]);

    exit;
}

if (!array_key_exists("account_number", $_SESSION)) {

    echo json_encode(["status" => false, "message" => "Please Authenticate First"]);

    exit;
}

$database = new Database();

$transaction_repository = new TransactionRepository($database);

$transactions = $transaction_repository->getTodayTransactions($_SESSION["account_number"]);

echo json_encode(["status" => true, "transactions" => $transactions]);