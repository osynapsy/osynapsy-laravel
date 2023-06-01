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
}
