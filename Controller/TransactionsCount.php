<?php

header("Content-Type: application/json");

require_once "../Config/Database.php";
require_once "../Repository/TransactionRepository.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {

    http_response_code(405);

    echo json_encode(["status" => false, "message" => "Method Not Allowed. Use GET."]);

    exit;
}

$date = $_GET["date"] ?? null;

$database = new Database();

$transaction_repository = new TransactionRepository($database);

$transaction = $transaction_repository->getTransactionCount($date);

echo json_encode(["status" => true, "data" => $transaction]);