<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ;

class Database {

    /** @var \PDO */
    private $pdo;

    /**
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function quoteIdentifier(string $name) : string
    {
        return "`".str_replace("`","``",$name)."`";
    }

    public function quote($value) : string
    {
        if(is_int($value)) {
            return (string)$value;
        }
        return $this->pdo->quote($value);
    }

    /**
     * @param string $sql
     * @param array $bindValues
     * @return array|bool
     */
    public function selectRow(string $sql, array $bindValues = []) {
        $result = $this->selectAll($sql, $bindValues);
        if (!isset($result[0])) {
            return FALSE;
        } else {
            return $result[0];
        }
    }

    /**
     * @param string $sql
     * @param array $bindValues
     * @return mixed|bool
     */
    public function selectOne(string $sql, array $bindValues = []) {
        $result = $this->selectAll($sql, $bindValues);
        if (!isset($result[0])) {
            return FALSE;
        } else {
            return current($result[0]);
        }
    }

    /**
     * @param string $sql
     * @param array $bindValues
     * @return array|bool
     */
    public function selectColumn(string $sql, array $bindValues = []) {
        $rows = $this->selectAll($sql, $bindValues);
        if ($rows === FALSE) {
            return FALSE;
        } else {
            $result = array();
            foreach ($rows as $row) {
                $result[] = current($row);
            }
            return $result;
        }
    }

    /**
     * @param string $column Name of the column used as key in the result-array
     * @param string $sql
     * @param array $bindValues
     * @return array|bool
     */
    public function selectAllAssoc(string $column, string $sql, array $bindValues = []) {
        $result = $this->selectAll($sql, $bindValues);
        if ($result === FALSE) {
            return FALSE;
        } else {
            $arr = [];
            foreach ($result as $row) {
                $key = $row[$column];
                unset($row[$column]);
                $arr[$key] = $row;
            }
            return $arr;
        }
    }

    /**
     * @param string $sql
     * @param array $bindValues
     * @return array|bool
     */
    public function selectAll(string $sql, array $bindValues = []) {
        $statement = $this->pdo->prepare($sql);
        $this->bindValues($statement, $bindValues);
        $success = $statement->execute();
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $statement->closeCursor();
        if ($success) {
            return $result;
        } else {
            return FALSE;
        }
    }

    /**
     * @param string $tableName
     * @param array $values
     * @return int|int[]|bool
     */
    public function insert(string $tableName, array $values) {
        $returnArrayOfIds = true;
        if(!is_array(current($values))) {
            $values = [$values];
            $returnArrayOfIds = false;
        }
        $columnNames = array_keys(current($values));
        $bindValues = [];

        $rows = [];
        $i = -1;
        foreach ($values as $rowValues) {
            $columns_arr = [];
            foreach ($columnNames as $key) {
                $columns_arr[] = ':' . $key.'__'.(++$i);
                $bindValues[$key.'__'.$i] = $rowValues[$key];
            }
            $rows[] = '('.implode(', ', $columns_arr).')';
        }

        $sql = 'INSERT INTO `' . $tableName . '` (' . implode(', ', $columnNames) .
            ') VALUES ' . implode(', ', $rows) . ';';

        $statement = $this->pdo->prepare($sql);
        $this->bindValues($statement, $bindValues);
        $success = $statement->execute();
        $statement->closeCursor();
        if ($success) {
            if($returnArrayOfIds) {
                $high = $this->pdo->lastInsertId();
                return range($high-count($rows)+1, $high);
            }
            return (int)$this->pdo->lastInsertId();
        } else {
            return false;
        }
    }

    /**
     * @param string $tableName
     * @param array $values
     * @param string $sqlWhere
     * @param array $bindValues
     * @return bool
     */
    public function update(string $tableName, array $values, string $sqlWhere, array $bindValues = []) {
        $columns_arr = array();
        $i = 0;
        $new_values = [];
        foreach ($values as $key => $value) {
            $columns_arr[] = $key.' = :__v'.$i.$key;
            $key = '__v'.$i.$key;
            $new_values[$key] = $value;
            $i++;
        }
        $sql = 'UPDATE `' . $tableName . '` SET ' . implode(", ", $columns_arr) .
            ' WHERE '.$sqlWhere;
        $statement = $this->pdo->prepare($sql);
        $this->bindValues($statement, $new_values);
        $this->bindValues($statement, $bindValues);
        $success = $statement->execute();
        $statement->closeCursor();
        return $success;
    }

    /**
     * @param string $sql
     * @param array $bindValues
     * @return bool
     */
    public function exec(string $sql, array $bindValues = []) {
        $statement = $this->pdo->prepare($sql);
        $this->bindValues($statement, $bindValues);
        $success = $statement->execute();
        $statement->closeCursor();
        return $success;
    }

    private function bindValues(\PDOStatement &$statement, array $bindValues = []) {
        foreach ($bindValues as $key => $value) {
            if (is_array($value)) {
                $statement->bindValue(':' . $key, $value[0], $value[1]);
            } else {
                $statement->bindValue(':' . $key, $value);
            }
        }
    }

    public function execute(string $sql, array $bindValues = []) : DatabaseExecuteResult
    {
        $result = new DatabaseExecuteResult();
        $statement = $this->pdo->prepare($sql);
        $this->bindValues($statement, $bindValues);
        $statement->execute();
        $result->setAffectedRowsCount($statement->rowCount());
        $result->setLastInsertId($this->pdo->lastInsertId());
        $statement->closeCursor();
        return $result;
    }

    public function executeAndCountAffectedRows(string $sql, array $bindValues = []) : int
    {
        $statement = $this->pdo->prepare($sql);
        $this->bindValues($statement, $bindValues);
        $statement->execute();
        $rowCount = $statement->rowCount();
        $statement->closeCursor();
        return $rowCount;
    }
}