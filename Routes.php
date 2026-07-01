<?php

require_once "Controller/AccountController.php";
require_once "Controller/TransactionController.php";
require_once "Router.php";

$account_controller = new AccountController();

$transaction_controller = new TransactionController();

$router = new Router();

$router->add("POST", "/authenticate", [$account_controller, "authenticate"]);
$router->add("GET", "/balance", [$account_controller, "balance"]);
$router->add("POST", "/withdraw", [$account_controller, "withdraw"]);
$router->add("GET", "/transactions", [$transaction_controller, "transactions"]);
$router->add("GET", "/transaction/count", [$transaction_controller, "transactionCount"]);

$method = $_SERVER["REQUEST_METHOD"];

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$router->dispatch($method, $path);