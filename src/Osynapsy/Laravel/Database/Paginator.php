<?php
namespace Osynapsy\Laravel\Database;

/**
 * Description of Pagination
 *
 * @author Pietro Celeste <p.celeste@osynapsy.net>
 */
class Paginator
{
    const META_PAGE_MIN = 'pageMin';
    const META_PAGE_MAX = 'pageLast';
    const META_CURRENT_PAGE = 'pageCurrent';
    const META_TOTAL_PAGES = 'totalPages';
    const META_TOTAL_ROWS = 'totalRows';
    const META_PAGE_SIZE = 'pageSize';

    protected $data = [];
    private $db;    
    private $fields = [];
    private $par;
    private $sort = '1 DESC';
    private $sortDefault;
    private $sql;
    private $meta = [
        self::META_PAGE_SIZE => 10,
        self::META_TOTAL_PAGES => 1,
        self::META_PAGE_MIN => 0,
        self::META_PAGE_MAX => 0,
        self::META_CURRENT_PAGE => 1,
        self::META_TOTAL_ROWS => 0,
        'rowsFrom' => 0,
        'rowsTo' => 0
    ];

    /**
     * Costructor of pager component.
     *     
     * @param type $request Osynapsy Request object
     * @param type $defaultPageSize page size
     */
    public function __construct($sql, $sqlParameters = [], $db = null)
    {        
        $this->setSql($db, $sql, $sqlParameters);
    }

    public function addField($field)
    {
        $this->fields[] = $field;
    }

    public function get($currentPage = 1, $pageSize = 20, $sort = null, $filters = [])
    {
        $this->setPageSize($pageSize);        
        if (!empty($sort)) {
            $this->setSort($sort);
        }
        if (!empty($filters)) {
            $this->setFilters($filters);
        }
        $data = $this->loadData($this->sql, $this->par, $currentPage, $pageSize);        
        $pageCurrent = $this->getMeta(self::META_CURRENT_PAGE);
        $pageTotal = $this->getMeta(self::META_TOTAL_PAGES);
        $pagerDimension = min(7, $pageTotal);
        $pagerMedian = floor($pagerDimension / 2);
        $pagerMinimum = max(1, $pageCurrent - $pagerMedian);
        $pagerMaximum = max($pagerDimension, min($pageCurrent + $pagerMedian, $pageTotal));
        $this->setMeta(self::META_PAGE_MAX, $pagerMaximum);
        $this->setMeta(self::META_PAGE_MIN, min($pagerMinimum, $pageTotal - $pagerDimension + 1));
        return $data;
    }

    protected function loadData($sql, $parameters, $requestPage, $requestPageSize = 10)
    {        
        $pageSize = $this->setPageSize($requestPageSize);
        $sqlCount = PaginatorSqlBuilder::buildSqlCountFactory($sql);
        $totalRows = $this->setMeta(self::META_TOTAL_ROWS, $this->getNumberOfRows($sqlCount, $parameters));        
        $currentPage = $this->calcCurrentPage($requestPage, $pageSize, $totalRows);
        $sqlPaginated = PaginatorSqlBuilder::buildSqlQuery($sql, $this->sort, $currentPage, $pageSize, $totalRows);
        $data = json_decode(json_encode(\DB::select($sqlPaginated, $parameters)), true);
        return is_array($data) ? $data : [];
    }
    
    protected function getNumberOfRows($sqlCount, $parameters)
    {        
        $result = \DB::select($sqlCount, $parameters);         
        return empty($result) ? 0 : $result[0]->number_of_rows;
    }   

    private function calcCurrentPage($requestPage, $pageSize, $totalRows)
    {
        $pageCurrent = max(1, (int) $requestPage);
        if ($totalRows == 0 || empty($pageSize)) {
            return $pageCurrent;
        }
        $totalPages = ceil($this->meta[self::META_TOTAL_ROWS] / $pageSize);
        switch ($requestPage) {
            case 'first':
                $pageCurrent = 1;
                break;
            case 'last' :
                $pageCurrent = $totalPages;
                break;
            case 'prev':
                if ($pageCurrent > 1){
                    $pageCurrent--;
                }
                break;
            case 'next':
                if ($pageCurrent < $totalPages) {
                    $pageCurrent++;
                }
                break;
            default:
                $pageCurrent = min($pageCurrent, $totalPages);
                break;
        }
        $this->setMeta('pageCurrent', $pageCurrent);
        $this->setMeta('totalPages', $totalPages);
        return $pageCurrent;
    }   

    public function getDb()
    {
        return $this->db;
    }

    public function getErrors()
    {
        return implode(PHP_EOL, $this->errors);
    }    

    public function getSort()
    {
        return $this->sort;
    }

    public function getMeta($key)
    {
        return array_key_exists($key, $this->meta) ? $this->meta[$key] : null;
    }

    public function getAllMeta()
    {
        return $this->meta;
    }

    public function getTotal($key)
    {
        return $this->getStatistic('total'.ucfirst($key));
    }

    public function setSort($fields)
    {
        $this->sort = str_replace(['_asc','_desc'], [' ASC', ' DESC'], empty($fields) ? $this->sortDefault : $fields);
        return $this;
    }

    public function setSortDefault($fields)
    {
        $this->sortDefault = $fields;
    }

    public function setPageSize($requestSize)
    {        
        $size = min(1000, $requestSize);
        $this->setMeta('pageSize', $size);
        $this->setMeta('pageDimension', $size);
        return $size;
    }

    public function setSql($db, $cmd, array $par = [])
    {
        $this->db = $db;
        $this->sql = $cmd;
        $this->par = $par;
        return $this;
    }

    private function setMeta($key, $value)
    {
        return $this->meta[$key] = $value;
    }

    public function getJson()
    {
        return json_encode($this->get());
    }
}
