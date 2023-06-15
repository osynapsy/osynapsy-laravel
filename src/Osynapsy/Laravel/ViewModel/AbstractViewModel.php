<?php
namespace Osynapsy\Laravel\ViewModel;

/**
 * Description of AbstractViewModel
 *
 * @author peter
 */
class AbstractViewModel
{
    public $entityId;
    protected $laravelModel;
    protected $fieldMap = [];

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
        }        
        $this->laravelModel = $model;
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
        return $this->fieldMap[$modelField->getId()] = $modelField;
    }

    public function save()
    {
        if (empty($this->fieldMap)) {
            throw new \Exception('No fieldmap exists.');
        }        
        $model = $this->getLaravelModel();
        $result = [];
        foreach ($this->fieldMap as $field) {
            $result[] = [$field->getDbName(), $field->getValue()];
            $model->{$field->getDbName()} = $field->getValue();
        }
        $model->save();
    }

    public function delete()
    {
        $this->getLaravelModel()->delete();
    }
}
