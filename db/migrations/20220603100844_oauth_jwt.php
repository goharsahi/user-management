<?php
declare(strict_types=1);

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class OauthJwt extends AbstractMigration
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
        $this->table("oauth_jwt", ["id" => false, "primary_key" => ["client_id"]])
        ->addColumn("client_id", MysqlAdapter::PHINX_TYPE_STRING, ["limit" => 255, "null" => false])
        ->addColumn("subject", MysqlAdapter::PHINX_TYPE_STRING, ["limit" => 80, "null" => true])
        ->addColumn("public_key", MysqlAdapter::PHINX_TYPE_STRING, ["limit" => 2000, "null" => false])
        ->create();
    }
}
