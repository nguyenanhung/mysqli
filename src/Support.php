<?php
/**
 * Project mysqli
 * Created by PhpStorm
 * User: 713uk13m <dev@nguyenanhung.com>
 * Copyright: 713uk13m <dev@nguyenanhung.com>
 * Date: 09/16/2021
 * Time: 00:02
 */

namespace nguyenanhung\MySQLi;

/**
 * Trait Support
 *
 * @package   nguyenanhung\MySQLi
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
trait Support
{
    /**
     * Function errorException
     *
     * @param $e
     * @param $exitCode
     *
     * @return mixed
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/16/2021 34:58
     */
    protected function errorException($e, $exitCode)
    {
        $this->logger->error(__FUNCTION__, 'Error Message: ' . $e->getMessage());
        $this->logger->error(__FUNCTION__, 'Error Trace As String: ' . $e->getTraceAsString());

        return $exitCode;
    }

    /**
     * Function queryWhereFieldValue
     *
     * @param string|array $value
     * @param string       $field
     *
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/16/2021 03:57
     */
    protected function queryWhereFieldValue($value = '', $field = 'id')
    {
        if (is_array($value) && count($value) > 0) {
            foreach ($value as $f => $v) {
                if (isset($v['operator'])) {
                    $this->db->where($v['field'], $v['value'], $v['operator']);
                } else {
                    if (is_array($v)) {
                        $this->db->where($f, $v, self::OPERATOR_IS_IN);
                    } else {
                        $this->db->where($f, $v, self::OPERATOR_EQUAL_TO);
                    }
                }

            }
        } else {
            if (isset($value['operator'])) {
                $this->db->where($value['field'], $value['value'], $value['operator']);
            } else {
                if (is_array($value)) {
                    $this->db->where($field, $value, self::OPERATOR_IS_IN);
                } else {
                    $this->db->where($field, $value, self::OPERATOR_EQUAL_TO);
                }
            }
        }
    }

    /**
     * Function queryOnlyWhereFieldValue
     *
     * @param string|array $wheres
     *
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/16/2021 19:36
     */
    protected function queryOnlyWhereFieldValue($wheres = '')
    {
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $column => $value) {
                if (isset($value['operator'])) {
                    $this->db->where($value['field'], $value['value'], $value['operator']);
                } else {
                    if (is_array($value)) {
                        $this->db->where($column, $value, self::OPERATOR_IS_IN);
                    } else {
                        $this->db->where($column, $value, self::OPERATOR_EQUAL_TO);
                    }
                }
            }
        }
    }

    /**
     * Function queryMultipleWhereField
     *
     * @param string|array $wheres
     * @param string       $field
     *
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/16/2021 05:43
     */
    protected function queryMultipleWhereField($wheres = '', $field = 'id')
    {
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $value) {
                if (is_array($value['value'])) {
                    $this->db->where($value['field'], $value['value'], self::OPERATOR_IS_IN);
                } else {
                    $this->db->where($value['field'], $value['value'], $value['operator']);
                }
            }
        } else {
            $this->db->where($field, $wheres, self::OPERATOR_EQUAL_TO);
        }
    }

    /**
     * Function queryMultipleWhere
     *
     * @param string|array $wheres
     *
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/16/2021 09:50
     */
    protected function queryMultipleWhere($wheres = '')
    {
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $field => $value) {
                if (isset($value['operator'])) {
                    $this->db->where($value['field'], $value['value'], $value['operator']);
                } else {
                    if (is_array($value)) {
                        $this->db->where($field, $value, self::OPERATOR_IS_IN);
                    } else {
                        $this->db->where($field, $value, self::OPERATOR_EQUAL_TO);
                    }
                }
            }
        } else {
            $this->db->where($this->primaryKey, $wheres, self::OPERATOR_EQUAL_TO);
        }
    }

    /**
     * Function queryOnlyMultipleWhere
     *
     * @param string|array $wheres
     *
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/16/2021 17:41
     */
    protected function queryOnlyMultipleWhere($wheres = '')
    {
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $value) {
                if (is_array($value['value'])) {
                    $this->db->where($value['field'], $value['value'], self::OPERATOR_IS_IN);
                } else {
                    $this->db->where($value['field'], $value['value'], $value['operator']);
                }
            }
        }
    }

    /**
     * Function queryOrderBy
     *
     * @param array|null $options
     *
     * @throws \Exception
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 13/06/2022 46:07
     */
    protected function queryOrderBy(array $options = null)
    {
        if (is_array($options) && (isset($options['orderBy']) && is_array($options['orderBy']))) {
            foreach ($options['orderBy'] as $column => $direction) {
                $this->db->orderBy($column, $direction);
            }
        }
    }

    /**
     * Function queryGetResultWithLimit
     *
     * @param $options
     * @param $selectField
     *
     * @return array|\MysqliDb|object|string
     * @throws \Exception
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 13/06/2022 46:13
     */
    protected function queryGetResultWithLimit($options = null, $selectField = '*')
    {
        if (isset($options['limit'], $options['offset']) && $options['limit'] > 0) {
            $page    = $this->preparePaging($options['offset'], $options['limit']);
            $numRows = array($page['offset'], $options['limit']);
            $result  = $this->db->get($this->table, $numRows, $selectField);
        } else {
            $result = $this->db->get($this->table, null, $selectField);
        }

        return $result;
    }
}
