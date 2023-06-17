<?php
namespace Osynapsy\Laravel\Action\Predefined;

use Osynapsy\Laravel\Action\AbstractAction;

/**
 * Description of SaveEntity
 *
 * @author Pietro Celeste <pietro.celeste@gmail.com>
 */
class SaveEntity extends AbstractAction
{
    public function execute(...$params)
    {
        try {
            $request = request();
            $mapFields = $this->getViewModel()->getMapFields();
            $this->validateMapFields($mapFields);
            $laravelModel = $this->getViewModel()->getLaravelModel();
            $this->validateLaravelModel($laravelModel);            
            $response = $this->beforeSave($request, $mapFields);
            if (!empty($response)) {
                return $response;
            }
            $id = $this->save($laravelModel, $mapFields);            
            $this->afterSave($request, $id);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    protected function validateMapFields($mapFields)
    {
        if (empty($mapFields)) {
            $this->raiseException('No fieldmap exists.');
        }
    }

    protected function validateLaravelModel($laravelModel)
    {
        if (empty($laravelModel)) {
            $this->raiseException('No laravel model exists');
        }
    }   

    protected function beforeSave($request, $mapFields)
    {
        $this->validateUserInput($request, $mapFields);
    }

    protected function validateUserInput($request, $mapFields)
    {
        $rules = [];
        foreach ($mapFields as $fieldName => $modelField) {
            $rules[$fieldName] = $modelField->getValidationRules();
        }
        $request->validate($rules);
    }

    protected function save($model, $mapFields)
    {       
        foreach ($mapFields as $field) {
            $model->{$field->getDbName()} = $field->getValue();
        }
        $model->save();
        return $model->id;
    }

    protected function afterSave($request, $id)
    {        
        $redirectUrl = 'back';
        $urlPart = explode('/', $request->url());
        if (end($urlPart) === 'add') {
            $urlPart[array_key_last($urlPart)] = $id;
            $redirectUrl = implode('/', $urlPart);
        }
        $this->getResponse()->goto($redirectUrl);
    }
}
