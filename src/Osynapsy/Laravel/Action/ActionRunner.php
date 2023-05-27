<?php
namespace Osynapsy\Laravel\Action;

/**
 * Description of ActionRunner
 *
 * @author peter
 */
class ActionRunner
{
    protected $actions = [];
    
    public function addAction($actionId, $strClass)
    {
        if (!is_subclass_of($strClass, AbstractAction::class)) {
            throw new \Exception(sprintf("Argument #2 (\$strClass) must be of type %s", $strClass));
        }
        $this->actions[$actionId] = $strClass;        
    }
    
    public function getAction($actionId)
    {
        if (!array_key_exists($actionId, $this->actions)) {
            throw new \Exception(sprintf('No action %s exists', $actionId));
        }
        return $this->actions[$actionId];
    }
    
    public function setDefaultAction(callable $action)
    {
        $this->actions['defaultAction'] = $action;
    }
    
    public function execute()
    {
        $actionId = request()->header('Osynapsy-Action');
        return empty($actionId) ? $this->executeDefaultAction() : $this->executeAction($actionId);                        
    }
    
    protected function executeDefaultAction()
    {
        $function = $this->getAction('defaultAction');
        return $function();
    }
    
    protected function executeAction($actionId)
    {        
        $actionClass = $this->getAction($actionId);
        return (new $actionClass)->execute();
    }
}
