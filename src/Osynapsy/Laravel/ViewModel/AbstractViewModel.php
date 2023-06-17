<?php
namespace Osynapsy\Laravel\ViewModel;

/**
 * Description of AbstractViewModel
 *
 * @author peter
 */
class AbstractViewModel
{
    const BEHAVIOR_INSERT = 'insert';
    const BEHAVIOR_UPDATE = 'update';

    public $entityId;
    protected $laravelModel;
    protected $mapFields = [];
    protected $behavior = self::BEHAVIOR_INSERT;

    public function __construct()
    {
        $this->init();
    }

    protected function init()
    {
    }

    public function getLaravelModel()
    {
        return $this->laravelModel;
    }
    
    public function setLaravelModel($model, $id = null)
    {        
        if (!empty($id)) {
            $model = $model->find($id);
            $this->behavior = self::BEHAVIOR_UPDATE;
        }        
        $this->laravelModel = $model;
    }

    public function getBehavior()
    {
        return $this->behavior;
    }

    public function getEntityId()
    {
        return $this->entityId;
    }
    
    public function getValue($requestField)
    {
        $dbField = array_key_exists($requestField, $this->fieldMap) ? $this->fieldMap[$requestField]->getDbName() : $requestField;
        $dbValue = $this->laravelModel ? $this->getLaravelModel()->{$dbField} : null;
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

    public function map($requestField, $dbField, $defaultValue = null)
    {
        $modelField = new ViewModelField($requestField, $dbField, $defaultValue);
        return $this->mapFields[$modelField->getId()] = $modelField;
    }

    public function getMapFields()
    {
        return $this->mapFields;
    }
}
