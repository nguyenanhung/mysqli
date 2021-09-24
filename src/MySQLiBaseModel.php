<?php
/**
 * Project pdo.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 2021-08-28
 * Time: 10:21
 */

namespace nguyenanhung\MySQLi;

use Exception;
use MysqliDb;

/**
 * Class MySQLiBaseModel
 *
 * @package   nguyenanhung\MyDatabase\Model
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
class MySQLiBaseModel
{
    use Support;

    const VERSION       = '3.0.0';
    const LAST_MODIFIED = '2021-09-24';
    const AUTHOR_NAME   = 'Hung Nguyen';
    const AUTHOR_EMAIL  = 'dev@nguyenanhung.com';
    const PROJECT_NAME  = 'Database Wrapper - MySQLi Database Model';

    const OPERATOR_EQUAL_TO                 = '=';
    const OP_EQ                             = '=';
    const OPERATOR_NOT_EQUAL_TO             = '!=';
    const OP_NE                             = '!=';
    const OPERATOR_LESS_THAN                = '<';
    const OP_LT                             = '<';
    const OPERATOR_LESS_THAN_OR_EQUAL_TO    = '<=';
    const OP_LTE                            = '<=';
    const OPERATOR_GREATER_THAN             = '>';
    const OP_GT                             = '>';
    const OPERATOR_GREATER_THAN_OR_EQUAL_TO = '>=';
    const OP_GTE                            = '>=';
    const OPERATOR_IS_SPACESHIP             = '<=>';
    const OPERATOR_IS_IN                    = 'IN';
    const OPERATOR_IS_LIKE                  = 'LIKE';
    const OPERATOR_IS_LIKE_BINARY           = 'LIKE BINARY';
    const OPERATOR_IS_ILIKE                 = 'ilike';
    const OPERATOR_IS_NOT_LIKE              = 'NOT LIKE';
    const OPERATOR_IS_NULL                  = 'IS NULL';
    const OPERATOR_IS_NOT_NULL              = 'IS NOT NULL';
    const ORDER_ASCENDING                   = 'ASC';
    const ORDER_DESCENDING                  = 'DESC';


    /** @var object Đối tượng khởi tạo dùng gọi đến Class Debug \nguyenanhung\MyDebug\Logger */
    protected $logger;
    /** @var array|null Mảng dữ liệu chứa thông tin database cần kết nối tới */
    protected $database;
    /** @var string DB Name */
    protected $dbName = 'default';
    /** @var string|null Bảng cần lấy dữ liệu */
    protected $table;
    /** @var object Database */
    protected $db;
    /** @var bool Cấu hình trạng thái Debug, TRUE nếu bật, FALSE nếu tắt */
    public $debugStatus = false;
    /** @var null|string Cấu hình Level Debug */
    public $debugLevel = 'error';
    /** @var null|bool|string Cấu hình thư mục lưu trữ Log, VD: /your/to/path */
    public $debugLoggerPath = '';
    /** @var null|string Cấu hình File Log, VD: Log-2018-10-15.log | Log-date('Y-m-d').log */
    public $debugLoggerFilename = '';
    /** @var string Primary Key Default */
    public $primaryKey = 'id';

    /**
     * MySQLiBaseModel constructor.
     *
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     */
    public function __construct()
    {
        if (class_exists('\nguyenanhung\MyDebug\Logger')) {
            $this->logger = new \nguyenanhung\MyDebug\Logger();
            if ($this->debugStatus === true) {
                $this->logger->setDebugStatus($this->debugStatus);
                if ($this->debugLevel) {
                    $this->logger->setGlobalLoggerLevel($this->debugLevel);
                }
                if ($this->debugLoggerPath) {
                    $this->logger->setLoggerPath($this->debugLoggerPath);
                }
                if (empty($this->debugLoggerFilename)) {
                    $this->debugLoggerFilename = 'Log-' . date('Y-m-d') . '.log';
                }
                $this->logger->setLoggerSubPath(__CLASS__);
                $this->logger->setLoggerFilename($this->debugLoggerFilename);
            }
        }
        if (isset($this->database) && is_array($this->database) && !empty($this->database)) {
            $this->db = new MysqliDb();
            $this->db->addConnection($this->dbName, $this->database);
        }
    }

    /**
     * MySQLiBaseModel destructor.
     */
    public function __destruct()
    {
    }

    /**
     * Function getVersion
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 9/28/18 14:47
     *
     * @return string
     */
    public function getVersion(): string
    {
        return self::VERSION;
    }

    /**
     * Function preparePaging
     *
     * @param int $pageIndex
     * @param int $pageSize
     *
     * @return array
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/21/2021 23:24
     */
    public function preparePaging(int $pageIndex = 1, int $pageSize = 10): array
    {
        if ($pageIndex !== 0) {
            if (!$pageIndex || $pageIndex <= 0 || empty($pageIndex)) {
                $pageIndex = 1;
            }
            $offset = ($pageIndex - 1) * $pageSize;
        } else {
            $offset = $pageIndex;
        }

        return array('offset' => $offset, 'limit' => $pageSize);
    }

    /**
     * Function setPrimaryKey
     *
     * @param string $primaryKey
     *
     * @return $this
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/28/2021 42:05
     */
    public function setPrimaryKey(string $primaryKey): MySQLiBaseModel
    {
        $this->primaryKey = $primaryKey;

        return $this;
    }

    /**
     * Function getPrimaryKey
     *
     * @return string
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/28/2021 42:13
     */
    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    /**
     * Function setDatabase
     *
     * @param array  $database
     * @param string $name
     *
     * @return $this
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 20:53
     */
    public function setDatabase(array $database = array(), string $name = 'default'): MySQLiBaseModel
    {
        $this->database = $database;
        $this->dbName   = $name;

        return $this;
    }

    /**
     * Function getDatabase
     *
     * @return array|null
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 20:53
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Function getDbName
     *
     * @return string
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 20:59
     */
    public function getDbName(): string
    {
        return $this->dbName;
    }

    /**
     * Function setTable
     *
     * @param string $table
     *
     * @return $this
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 21:54
     */
    public function setTable(string $table = ''): MySQLiBaseModel
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Function getTable
     *
     * @return string|null
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 21:54
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Function connection
     *
     * @return $this
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 20:43
     */
    public function connection(): MySQLiBaseModel
    {
        if (!is_object($this->db)) {
            $this->db = new MysqliDb();
            $this->db->addConnection($this->dbName, $this->database);
        }

        return $this;
    }

    /**
     * Function disconnect
     *
     * @param string $name
     *
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/20/2021 59:34
     */
    public function disconnect(string $name = '')
    {
        if (empty($name)) {
            $name = $this->dbName;
        }
        try {
            $this->db->disconnect($name);
            unset($this->db);
        } catch (Exception $e) {
            $this->errorException($e, null);
        }
    }

    /**
     * Function disconnectAll
     *
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 29:15
     */
    public function disconnectAll()
    {
        try {
            $this->db->disconnectAll();
        } catch (Exception $e) {
            $this->logger->error(__FUNCTION__, 'Error Message: ' . $e->getMessage());
            $this->logger->error(__FUNCTION__, 'Error Trace As String: ' . $e->getTraceAsString());
        }
    }

    /**
     * Function getDb
     *
     * @return object
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 22:03
     *
     */
    public function getDb()
    {
        return $this->db;
    }

    /*************************** DATABASE METHOD ***************************/
    /**
     * Function countAll
     *
     * @param string $column
     *
     * @return int|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 33:24
     */
    public function countAll(string $column = '*')
    {
        try {
            $results = $this->db->get($this->table, null, $column);

            return count($results);
        } catch (Exception $e) {
            return $this->errorException($e, null);
        }
    }

    /**
     * Function checkExists
     *
     * @param string|array $whereValue
     * @param string       $whereField
     * @param string       $select
     *
     * @return int|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 33:50
     */
    public function checkExists($whereValue = '', string $whereField = 'id', string $select = '*')
    {
        try {
            $this->queryWhereFieldValue($whereValue, $whereField);
            $this->db->get($this->table, null, $select);

            return (int) $this->db->count;
        } catch (Exception $e) {
            return $this->errorException($e, null);
        }
    }

    /**
     * Function checkExistsWithMultipleWhere
     *
     * @param string|array $whereValue
     * @param string       $whereField
     * @param string       $select
     *
     * @return int|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 34:04
     */
    public function checkExistsWithMultipleWhere($whereValue = '', string $whereField = 'id', string $select = '*')
    {
        try {
            $this->queryMultipleWhereField($whereValue, $whereField);
            $this->db->get($this->table, null, $select);

            return (int) $this->db->count;
        } catch (Exception $e) {
            return $this->errorException($e, null);
        }
    }

    /**
     * Function getLatest
     *
     * @param string $selectField
     * @param string $orderByColumn
     *
     * @return array|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 34:21
     */
    public function getLatest(string $selectField = '*', string $orderByColumn = 'id')
    {
        try {
            $this->db->orderBy($orderByColumn, self::ORDER_DESCENDING);

            return $this->db->getOne($this->table, $selectField);
        } catch (Exception $e) {
            return $this->errorException($e, null);
        }

    }

    /**
     * Function getOldest
     *
     * @param string $selectField
     * @param string $orderByColumn
     *
     * @return array|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 34:30
     */
    public function getOldest(string $selectField = '*', string $orderByColumn = 'id')
    {
        try {
            $this->db->orderBy($orderByColumn, self::ORDER_ASCENDING);

            return $this->db->getOne($this->table, $selectField);
        } catch (Exception $e) {
            return $this->errorException($e, null);
        }
    }

    /**
     * Function getInfo
     *
     * @param string|array $value
     * @param string       $field
     * @param string       $selectField
     *
     * @return array|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 34:56
     */
    public function getInfo($value = '', string $field = 'id', string $selectField = '*')
    {
        try {
            $this->queryWhereFieldValue($value, $field);
            $result = $this->db->getOne($this->table, $selectField);
            if ($this->db->count > 0) {
                return $result;
            }

            return null;
        } catch (Exception $e) {
            return $this->errorException($e, null);
        }
    }

    /**
     * Function getInfoWithMultipleWhere
     *
     * @param string|array $wheres
     * @param string       $field
     * @param string       $selectField
     *
     * @return array|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 35:18
     */
    public function getInfoWithMultipleWhere($wheres = '', string $field = 'id', string $selectField = '*')
    {
        try {
            $this->queryMultipleWhereField($wheres, $field);
            $result = $this->db->getOne($this->table, $selectField);
            if ($this->db->count > 0) {
                return $result;
            }

            return null;
        } catch (Exception $e) {
            return $this->errorException($e, null);
        }
    }

    /**
     * Function getValue
     *
     * @param string|array $value
     * @param string       $field
     * @param string       $fieldOutput
     *
     * @return mixed|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 35:39
     */
    public function getValue($value = '', string $field = 'id', string $fieldOutput = '')
    {
        try {
            $this->queryWhereFieldValue($value, $field);
            $result = $this->db->getOne($this->table, $fieldOutput);

            // $this->logger->debug(__FUNCTION__, 'GET Result => ' . json_encode($result));
            return $result->$fieldOutput ?? null;
        } catch (Exception $e) {
            return $this->errorException($e, null);
        }
    }

    /**
     * Function getValueWithMultipleWhere
     *
     * @param string|array $wheres
     * @param string       $field
     * @param string       $fieldOutput
     *
     * @return   mixed|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 36:20
     */
    public function getValueWithMultipleWhere($wheres = '', string $field = 'id', string $fieldOutput = '')
    {
        try {
            $this->queryMultipleWhereField($wheres, $field);
            $result = $this->db->getOne($this->table, $fieldOutput);

            // $this->logger->debug(__FUNCTION__, 'GET Result => ' . json_encode($result));
            return $result->$fieldOutput ?? null;
        } catch (Exception $e) {
            return $this->errorException($e, null);
        }
    }

    /**
     * Function getDistinctResult
     *
     * @param string $selectField
     *
     * @return array|\MysqliDb|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 36:54
     */
    public function getDistinctResult(string $selectField = '*')
    {
        try {
            return $this->db->setQueryOption(['DISTINCT'])->get($this->table, null, $selectField);
        } catch (Exception $e) {
            return $this->errorException($e, null);
        }
    }

    /**
     * Function getResultDistinct
     *
     * @param string $selectField
     *
     * @return array|\MysqliDb|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 36:58
     */
    public function getResultDistinct(string $selectField = '')
    {
        return $this->getDistinctResult($selectField);
    }

    /**
     * Function getResult
     *
     * @param array|string $wheres
     * @param string       $selectField
     * @param array|null   $options
     *
     * @return array|\MysqliDb|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 37:11
     */
    public function getResult($wheres = array(), string $selectField = '*', array $options = null)
    {
        try {
            $this->queryMultipleWhere($wheres);
            $this->queryOrderBy($options);

            // $this->logger->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));

            return $this->queryGetResultWithLimit($options, $selectField);
        } catch (Exception $e) {
            return $this->errorException($e, null);
        }
    }

    /**
     * Function getResultWithMultipleWhere
     *
     * @param array  $wheres
     * @param string $selectField
     * @param null   $options
     *
     * @return array|\MysqliDb|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 37:18
     */
    public function getResultWithMultipleWhere(array $wheres = array(), string $selectField = '*', $options = null)
    {
        try {
            $this->queryOnlyMultipleWhere($wheres);

            $this->queryOrderBy($options);

            // $this->logger->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));

            return $this->queryGetResultWithLimit($options, $selectField);
        } catch (Exception $e) {
            return $this->errorException($e, null);
        }
    }

    /**
     * Function countResult
     *
     * @param array  $wheres
     * @param string $selectField
     *
     * @return int
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 37:51
     */
    public function countResult(array $wheres = array(), string $selectField = '*'): int
    {
        try {
            $this->queryMultipleWhere($wheres);
            $this->db->get($this->table, null, $selectField);
            // $this->logger->debug(__FUNCTION__, 'Total Item Result => ' . json_encode($result));
            if ($this->db->count > 0) {
                return (int) $this->db->count;
            }

            return 0;
        } catch (Exception $e) {
            return $this->errorException($e, 0);
        }
    }

    /**
     * Function add
     *
     * @param array $data
     *
     * @return int
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/16/2021 12:48
     */
    public function add(array $data = array()): int
    {
        try {
            $insertId = $this->db->insert($this->table, $data);
            if ($insertId) {
                return (int) $insertId;
            }

            return 0;
        } catch (Exception $e) {
            return $this->errorException($e, 0);
        }
    }

    /**
     * Function update
     *
     * @param array $data
     * @param array $wheres
     *
     * @return int
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 38:31
     */
    public function update(array $data = array(), array $wheres = array()): int
    {
        try {
            $this->queryOnlyWhereFieldValue($wheres);
            if ($this->db->update($this->table, $data)) {
                return (int) $this->db->count;
            }

            return 0;
        } catch (Exception $e) {
            return $this->errorException($e, 0);
        }
    }

    /**
     * Function delete
     *
     * @param array $wheres
     *
     * @return int
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 38:47
     */
    public function delete(array $wheres = array()): int
    {
        try {
            $this->queryOnlyWhereFieldValue($wheres);

            if ($this->db->delete($this->table)) {
                return (int) $this->db->count;
            }

            return 0;
        } catch (Exception $e) {
            return $this->errorException($e, 0);
        }
    }
}
