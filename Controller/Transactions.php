<?php

session_start();

header("Content-Type: application/json");

require_once "../Database.php";
require_once "../TransactionRepository.php";

if (!isset($_SESSION["account_number"])) {

    echo json_encode(["status" => false, "message" => "Please Authenticate First"]);

    exit;
}

$database = new Database();

$transaction_repository = new TransactionRepository($database);

$transactions = $transaction_repository->getTodayTransactions($_SESSION["account_number"]);

echo json_encode(["status" => true, "transactions" => $transactions]);