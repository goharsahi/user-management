<?php
declare(strict_types=1);

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class OauthScopes extends AbstractMigration
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
        $this->table("oauth_scopes", ["id" => false])
        ->addColumn("type", MysqlAdapter::PHINX_TYPE_STRING, ["limit" => 255, "null" => false, "default" => "supported"])
        ->addColumn("scope", MysqlAdapter::PHINX_TYPE_STRING, ["limit" => 2000, "null" => true])
        ->addColumn("client_id", MysqlAdapter::PHINX_TYPE_STRING, ["limit" => 255, "null" => true])
        ->addColumn("is_default", MysqlAdapter::PHINX_TYPE_SMALL_INTEGER, ["null" => true, "default" => NULL])
        ->create();
    }
}
