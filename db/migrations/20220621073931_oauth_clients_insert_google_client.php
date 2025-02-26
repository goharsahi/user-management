<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class OauthClientsInsertGoogleClient extends AbstractMigration
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
        $builder = $this->getQueryBuilder();
        $builder
            ->insert(["client_id", "client_secret", "redirect_uri", "grant_types", "scope", "user_id"])
            ->into("oauth_clients")
            ->values(
                [
                    "client_id" => "",
                    "client_secret" => "",
                    "redirect_uri" => "<HOST>/user/googleLogin",
                    "grant_types" => "client_credentials",
                    "scope" => "openid email profile",
                    "user_id" => ""
                ]
            )
            ->execute();
    }
}
