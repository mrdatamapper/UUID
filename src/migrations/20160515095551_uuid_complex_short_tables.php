<?php

use Phinx\Migration\AbstractMigration;
use DeltaCore\Application;


class UuidComplexShortTables extends AbstractMigration
{
    
    public function up()
    {
        if (!defined("ROOT_DIR")) {
            define('ROOT_DIR', realpath(__DIR__ . '/..'));
        }
        if (!defined("PUBLIC_DIR")) {
            define('PUBLIC_DIR', ROOT_DIR . '/public');
        }
        if (!defined("VENDOR_DIR")) {
            define('VENDOR_DIR', ROOT_DIR . '/vendor');
        }
        if (!defined("DATA_DIR")) {
            define('DATA_DIR', ROOT_DIR . '/data');
        }


        $loader = include ROOT_DIR . "/vendor/autoload.php";

        $app = new Application();
        $app->setLoader($loader);

        $app->init();

        $epoch = $app->getConfig()->get(["UUID", "complexShort", "epoch"], 1451317149374);
        $shard = $app->getConfig(["UUID", "complexShort", "shard"], 1);

        $sql = <<<sql
CREATE OR REPLACE FUNCTION public.uuid_short_complex_tables(IN table_id integer DEFAULT 1) RETURNS bigint AS
$$
DECLARE
    our_epoch bigint := $epoch;
    seq_id bigint;
    now_millis bigint;
    shard_id integer := $shard;
    result bigint;
BEGIN
    if (table_id < 1) or (table_id > 512) then
        return null;
    end if;

    SELECT nextval('uuid_complex_short_tables_' || table_id) % 1024 INTO seq_id;

    SELECT FLOOR(EXTRACT(EPOCH FROM clock_timestamp()) * 1000) INTO now_millis;
    result := (now_millis - our_epoch) << 23;
    result := result | (shard_id << 19);
    result := result | (table_id << 10);
    result := result | (seq_id);
    RETURN result;
END;
$$
LANGUAGE plpgsql VOLATILE LEAKPROOF;
sql;
        $this->execute($sql);

    }
}
