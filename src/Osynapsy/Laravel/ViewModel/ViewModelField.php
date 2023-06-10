<?php
namespace Osynapsy\Laravel\ViewModel;

/**
 * Description of ViewModelField
 *
 * @author peter
 */
class ViewModelField
{
    protected $id;
    protected $nullable = true;
    protected $nameInRequest;
    protected $nameOnDb;
    protected $defaultValue;
    protected $checks = [];

    public function __construct($requestField, $dbField = null, $defaultValue = null)
    {
        $this->id = empty($requestField) ? sprintf('__%s__', $dbField) : $requestField;
        $this->nameInRequest = $requestField;
        $this->nameOnDb = $dbField;
        $this->defaultValue = $defaultValue;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function required()
    {
        $this->checks[] = 'required';
    }

    public function getValue()
    {
        return request()->input($this->nameInRequest) ?? $this->defaultValue;
    }

    public function getDbName()
    {
        return $this->nameOnDb;
    }

    public function getRequestName()
    {
        return $this->nameInRequest;
    }
}
