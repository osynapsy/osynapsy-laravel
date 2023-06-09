<?php
namespace Osynapsy\Laravel\ViewModel;

/**
 * Description of AbstractViewModel
 *
 * @author peter
 */
class AbstractViewModel
{
    protected $model;
    
    public function getLaravelModel()
    {
        $this->model;
    }
    
    public function setLaravelModel($model)
    {
        $this->model = $model;
    }
    
    public function getValue($field)
    {
        $dbValue = $this->model ? $this->model->{$field} : null;
        return old($field, $dbValue);
    }

    public function doQuery($query, array $parameters = [])
    {
        $rs = \DB::select($query, $parameters);
        return json_decode(json_encode($rs), true);
    }
}
