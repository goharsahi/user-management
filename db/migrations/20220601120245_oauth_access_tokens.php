<?php
declare(strict_types=1);

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class OauthAccessTokens extends AbstractMigration
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
        $this->table("oauth_access_tokens", ["id" => false, "primary_key" => ["access_token"]])
        ->addColumn("access_token", MysqlAdapter::PHINX_TYPE_STRING, ["limit" => 1000, "null" => false])
        ->addColumn("client_id", MysqlAdapter::PHINX_TYPE_STRING, ["limit" => 255, "null" => false])
        ->addColumn("user_id", MysqlAdapter::PHINX_TYPE_STRING, ["limit" => 255, "null" => true])
        ->addColumn("expires", MysqlAdapter::PHINX_TYPE_TIMESTAMP, ["null" => false])
        ->addColumn("scope", MysqlAdapter::PHINX_TYPE_STRING, ["limit" => 2000, "null" => true])
        ->create();
    }
}
