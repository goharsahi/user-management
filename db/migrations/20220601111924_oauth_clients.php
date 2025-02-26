<?php
declare(strict_types=1);

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class OauthClients extends AbstractMigration
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
        $this->table("oauth_clients", ["id" => false, "primary_key" => ["client_id"]])
        ->addColumn("client_id", MysqlAdapter::PHINX_TYPE_STRING, ["limit" => 255, "null" => false])
        ->addColumn("client_secret", MysqlAdapter::PHINX_TYPE_STRING, ["limit" => 80, "null" => false])
        ->addColumn("redirect_uri", MysqlAdapter::PHINX_TYPE_STRING, ["limit" => 2000, "null" => false])
        ->addColumn("grant_types", MysqlAdapter::PHINX_TYPE_STRING, ["limit" => 80, "null" => true])
        ->addColumn("scope", MysqlAdapter::PHINX_TYPE_STRING, ["limit" => 2000, "null" => true])
        ->addColumn("user_id", MysqlAdapter::PHINX_TYPE_STRING, ["limit" => 255, "null" => true])
        ->create();
    }
}
