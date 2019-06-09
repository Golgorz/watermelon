<?php

namespace core\controllers\monoLog;


use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use PDO;
use PDOStatement;

class MySQLHandler extends AbstractProcessingHandler
{


    /**
     * @var PDO pdo object of database connection
     */
    protected $pdo;

    /**
     * @var PDOStatement statement to insert a new record
     */
    private $statement;

    /**
     * @var string the table to store the logs in
     */
    private $table = 'wm_logs';

    /**
     * @var array default fields that are stored in db
     */
    private $defaultfields = array('id', 'channel', 'level', 'message', 'datetime', 'context', 'level_name');


    /**
     * Constructor of this class, sets the PDO and calls parent constructor
     *
     * @param PDO $pdo                  PDO Connector for the database
     * @param bool $table               Table in the database to store the logs in
     * @param bool|int $level           Debug level which this handler should store
     * @param bool $bubble
     */
    public function __construct(
        PDO $pdo = null,
        $level = Logger::DEBUG,
        $bubble = true
    ) {
       if (!is_null($pdo)) {
            $this->pdo = $pdo;
        }
        parent::__construct($level, $bubble);
    }


    /**
     * Prepare the sql statment depending on the fields that should be written to the database
     */
    private function prepareStatement()
    {
        //Prepare statement
        $columns = "";
        $fields  = "";
        foreach ($this->defaultfields as $key => $f) {
            if ($f == 'id') {
                continue;
            }
            if ($key == 1) {
                $columns .= "$f";
                $fields .= ":$f";
                continue;
            }

            $columns .= ", $f";
            $fields .= ", :$f";
        }

        $this->statement = $this->pdo->prepare(
            'INSERT INTO `' . $this->table . '` (' . $columns . ') VALUES (' . $fields . ')'
        );
    }


    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param  $record[]
     * @return void
     */
    protected function write(array $record)
    {


        /*
         * merge $record['context'] and $record['extra'] as additional info of Processors
         * getting added to $record['extra']
         * @see https://github.com/Seldaek/monolog/blob/master/doc/02-handlers-formatters-processors.md
         */
        if (isset($record['extra'])) {
            $record['context'] = array_merge($record['context'], $record['extra']);
        }

        //'context' contains the array
        $contentArray = array_merge(array(
                                        'channel' => $record['channel'],
                                        'level' => $record['level'],
                                        'message' => $record['message'],
        								'level_name'=> Logger::getLevelName($record['level']),
                                        'datetime' => $record['datetime']->format('Y-m-d H:i:s'),
        								'context'=> json_encode($record["context"])
                                    ), $record['context']);
        // unset array keys that are passed put not defined to be stored, to prevent sql errors
        foreach($contentArray as $key => $context) {
            if (! in_array($key, $this->defaultfields)) {
                unset($contentArray[$key]);
                unset($this->defaultfields[array_search($key, $this->defaultfields)]);
                continue;
            }

            if ($context === null) {
                unset($contentArray[$key]);
                unset($this->defaultfields[array_search($key, $this->defaultfields)]);
            }
        }

        $this->prepareStatement();

        $this->statement->execute($contentArray);
    }
}
