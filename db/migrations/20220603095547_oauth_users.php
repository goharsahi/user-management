<?php
declare(strict_types=1);

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class OauthUsers extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $this->table("oauth_users", ["id" => false, "primary_key" => ["username"]])
        ->addColumn("username", MysqlAdapter::PHINX_TYPE_STRING, ["limit" => 255, "null" => false])
        ->addColumn("password", MysqlAdapter::PHINX_TYPE_STRING, ["limit" => 2000, "null" => true])
        ->addColumn("first_name", MysqlAdapter::PHINX_TYPE_STRING, ["limit" => 255, "null" => true])
        ->addColumn("last_name", MysqlAdapter::PHINX_TYPE_STRING, ["limit" => 255, "null" => true])
        ->create();
    }
}
