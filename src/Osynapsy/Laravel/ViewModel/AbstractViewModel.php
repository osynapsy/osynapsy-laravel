<?php
namespace Osynapsy\Laravel\ViewModel;

/**
 * Description of AbstractViewModel
 *
 * @author peter
 */
class AbstractViewModel
{
    protected $laravelModel;
    public $entityId;
    
    public function getLaravelModel()
    {
        $this->laravelModel;
    }
    
    public function setLaravelModel($model)
    {
        $this->laravelModel = $model;
    }

    public function getEntityId()
    {
        return $this->entityId;
    }
    
    public function getValue($requestField, $dbField = null)
    {
        $field = empty($dbField) ? $requestField : $dbField;
        $dbValue = $this->model ? $this->model->{$field} : null;
        return old($requestField, $dbValue);
    }

    public function doQuery($query, array $parameters = [])
    {
        $rs = \DB::select($query, $parameters);
        return json_decode(json_encode($rs), true);
    }

    public function setEntityId($routeParameterId, $defaultValue = null)
    {
        $id = request()->route($routeParameterId, $defaultValue);
        $this->entityId = is_null($id) || $id === 'add' ? $defaultValue : $id;
    }
}
