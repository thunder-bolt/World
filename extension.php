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

    public $world = array('continents', 'countries', 'languages', 'currencies');
    public $table_prefix;
    public $tables;
    public $columns = array(
        'countries' => array(
            'iso2',
            'iso3',
            'name',
            'longname',
            'numcode',
            'continent',
        ),
    );

    // ========================================================================
    // Extension Hook

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
        foreach ($this->world as $info) {
            $this->tables[$info] = $this->table_prefix . $info;
        }
        $this->tableContinents();
        $this->tableCountries();
        $this->tableLanguages();
        $this->tableCurrencies();

        // Routing

    }

    // ========================================================================
    // Table, Data

    /**
     * Add continents table to database.
     */
    private function tableContinents()
    {

    }

    /**
     * Add countries table to database.
     */
    private function tableCountries()
    {
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
                // @todo Find out the reason that cause issue when use 'smallint'.
                $table->addColumn('numcode', 'integer');
                $table->addColumn('continent', 'string', array('length' => 2));
                // Meta
                $table->setPrimaryKey(array('id'));
                $table->addUniqueIndex(array('iso2', 'name'));
                $table->addIndex(array('continent'));
                return $table;
            });
    }

    /**
     * Add languages table to database.
     */
    private function tableLanguages()
    {

    }

    /**
     * Add currencies table to database.
     */
    private function tableCurrencies()
    {

    }

    /**
     *
     */
    private function dataImport($info)
    {
        // Clear old data.
        $this->app['db']->executeUpdate('DELETE FROM ' . $this->tables[$info]);
        // Prepare raw data.
        $file = __DIR__ . '/data/' . $info . '.csv';
        $data  = $this->csvArray($file);
        // Import
        foreach ($data as $row) {
            switch ($info) {
                case 'countries':
                    // @todo Find a simpler method.
                    $this->app['db']->executeUpdate('INSERT INTO ' . $this->tables[$info] .
                        ' (iso2, iso3, name, longname, numcode, continent) ' .
                        'VALUES (:iso2, :iso3, :name, :longname, :numcode, :continent)',
                        array(
                            ':iso2' => $row['iso2'],
                            ':iso3' => $row['iso3'],
                            ':name' => $row['name'],
                            ':longname' => $row['longname'],
                            ':numcode' => $row['numcode'],
                            ':continent' => $row['continent'],
                        )
                    );
                    break;
            }
        }
    }

    // ========================================================================
    // Page

    /**
     * Data import page.
     */
    public  function pageImport()
    {

    }


    // ========================================================================
    // Utility

    /**
     * Parse .csv file into array.
     */
    public function csvArray($filename = '', $delimiter = ',')
    {
        if(!file_exists($filename) || !is_readable($filename)) {
            return false;
        }

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header) {
                    $header = $row;
                }
                else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }

        return $data;
    }

}
