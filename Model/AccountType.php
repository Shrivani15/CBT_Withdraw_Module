<?php

class AccountType
{
    private int $id;
    private string $account_type;
    private int $minimum_balance;
    private int $maximum_limit;

    public function __construct(int $_id, string $_account_type, int $_minimum_balance, int $_maximum_limit) {
        $this->id = $_id;
        $this->account_type = $_account_type;
        $this->minimum_balance = $_minimum_balance;
        $this->maximum_limit = $_maximum_limit;
    }

    public function getId() {
        return $this->id;
    }

    public function getAccountType() {
        return $this->account_type;
    }

    public function getMinimumBalance() {
        return $this->minimum_balance;
    }

    public function getMaximumLimit() {
        return $this->maximum_limit;
    }
}