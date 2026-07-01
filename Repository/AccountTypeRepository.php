<?php

require_once __DIR__ . "/../Config/Database.php";
require_once __DIR__ . "/../Service/PreparedStatementTrait.php";
require_once __DIR__ . "/../Model/Account.php";
require_once __DIR__ . "/../Model/AccountType.php";

class AccountTypeRepository
{
    private mysqli $connection;

    use PreparedStatementTrait;

    /**
     * Constructor.
     * @param Database $_database
     */
    public function __construct(Database $_database) {
        $this->connection = $_database->getConnection();
    }

    /**
     * Returns account type details.
     * @param int $_account_type_id
     * @return array|null
     */
    public function getAccountType(int $_account_type_id){
        $query = "SELECT 
                    minimum_balance, 
                    maximum_limit
                FROM 
                    account_types
                WHERE 
                    id = ?";

        $statement = $this->executeStatement($query, "i", $_account_type_id);

        $result = $statement->get_result();

        $account_type = $result->fetch_assoc();

        $statement->close();

        return $account_type;
    }
}