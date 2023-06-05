<?php
namespace Osynapsy\Laravel\Database;

/**
 * Description of PaginatorSqlBuilder
 *
 * @author peter
 */
class PaginatorSqlBuilder
{
    public static function buildSqlCountFactory($sql)
    {
        $select = 'SELECTX';
        $startWithSelect = (substr(trim(strtoupper($sql)), 0, strlen($select)) === $select);
        $groupByIsPresent = strpos(strtoupper($sql), 'GROUP BY');
        if ($startWithSelect === false || $groupByIsPresent) {
            return sprintf("SELECT COUNT(*) as number_of_rows FROM (%s) a", $sql);
        }
        $fromPosition = strpos(strtoupper($sql), 'FROM');
        return 'SELECT count(*) '.substr($sql, $fromPosition);
    }
    
    public static function buildSqlQuery($rawQuery, $sort, $currentPage, $pageSize, $totalRows)
    {
        $orderBy = empty($sort) ? '' : sprintf(PHP_EOL.'ORDER BY %s', $sort);
        $sqlNoPaginated = sprintf('SELECT a.* FROM (%s) a %s', $rawQuery, $orderBy);
        $dbType = \DB::connection()->getPDO()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        switch ($dbType) {
            case 'oracle':
                return self::buildOracleQuery($sqlNoPaginated, $currentPage, $pageSize);                
            case 'pgsql':
                return self::buildPgSqlQuery($sqlNoPaginated, $currentPage, $pageSize);                
            default:
                return self::buildMySqlQuery($sqlNoPaginated, $currentPage, $pageSize, $totalRows);                
        }
    }        

    protected static function buildMySqlQuery($sql, $currentPage, $pageSize)
    {
        if (empty($pageSize)) {
            return $sql;
            
        }        
        $startFrom = max(0, ($currentPage - 1) * $pageSize);
        return $sql . sprintf(PHP_EOL."LIMIT %s, %s", $startFrom, $pageSize);
    }

    protected static function buildPgSqlQuery($sql, $currentPage, $pageSize)
    {
        if (!empty($pageSize)) {
            $startFrom = max(0, ($currentPage - 1) * $pageSize);
            $sql .= sprintf(PHP_EOL."LIMIT %s OFFSET %s", $pageSize, $startFrom);
        }
        return $sql;
    }

    protected static function buildOracleQuery($rawSql, $currentPage, $pageSize)
    {        
        $sql = sprintf("SELECT a.* FROM (SELECT b.*,rownum as \"_rnum\" FROM (%s) b) a ", $rawSql);        
        if (empty($pageSize)) {
            return $sql;            
        }        
        $startFrom = (($currentPage - 1) * $pageSize) + 1;
        $endTo = $currentPage * $pageSize;        
        return $sql . sprintf(PHP_EOL."WHERE \"_rnum\" BETWEEN %s AND %s", $startFrom, $endTo);
    }
}
