<?php
/**
 * World information data.
 *
 * @author Kolier.Li <kolier.li@gmail.com>
 */

namespace World;

use Doctrine\DBAL\Schema\Schema;

class Extension extends \Bolt\BaseExtension
{

    public $table_prefix;
    public $tables;

    /**
     *  Extension info.
     */
    public function info()
    {
        $data = array(
            'name' =>"World",
            'description' => "World information data.",
            'author' => "Kolier.Li",
            'link' => "http://kolier.li",
            'version' => "1.0",
            'required_bolt_version' => "1.6",
            'type' => "General",
            'first_releasedate' => "2014-08-01",
            'latest_releasedate' => "2014-08-01",
        );

        return $data;
    }

    /**
     *  Initiate extension.
     */
    public function initialize()
    {
        // Database
        $this->table_prefix = $this->app['config']->get('general/database/prefix', "bolt_");
        $this->tableContinents();
        $this->tableCountries();
        $this->tableLanguages();
        $this->tableCurrencies();

    }

    /**
     * Add continents table to database.
     */
    private function tableContinents()
    {
        $tables['continents'] = $this->table_prefix . 'continents';

    }

    /**
     * Add countries table to database.
     */
    private function tableCountries()
    {
        $tables['countries'] = $this->table_prefix . 'countries';
        $me = $this;
        $this->app['integritychecker']->registerExtensionTable(
            // ISO 3166-1
            function(Schema $schema) use ($me) {
                $table = $schema->createTable($me->tables['countries']);
                // Columns
                $table->addColumn('id', 'integer', array('autoincrement' => true));
                $table->addColumn('iso2', 'string', array('length' => 2));
                $table->addColumn('iso3', 'string', array('length' => 3));
                $table->addColumn('name', 'string', array('length' => 95));
                $table->addColumn('longname', 'string', array('length' => 127));
                $table->addColumn('numcode', 'smallint');
                $table->addColumn('continent', 'string', array('length' => 2));
                $table->addColumn('continent', 'string', array('length' => 2));
                $table->addColumn('enabled', 'boolean');
                // Meta
                $table->setPrimaryKey(array('id'));
                $table->addUniqueIndex(array('iso2', 'name'));
                $table->addIndex(array('enabled'));
                $table->addIndex(array('continent'));
                return $table;
            });
    }

    /**
     * Add languages table to database.
     */
    private function tableLanguages()
    {
        $tables['languages'] = $this->table_prefix . 'languages';
    }

    /**
     * Add currencies table to database.
     */
    private function tableCurrencies()
    {
        $tables['currencies'] = $this->table_prefix . 'currencies';
    }

}
