<?php

header("Content-Type: application/json");

require_once "../Config/Database.php";
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

$account = $account_repository->getAccountStatus((int)$_GET["account_number"]);

if ($account === null) {

    echo json_encode(["status" => false, "message" => "Account Not Found"]);

    exit;
}

echo json_encode(["status" => true, "data" => $account]);