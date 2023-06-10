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
    
    public function addAction($actionId, $strClass, $viewModel = null)
    {
        if (!is_subclass_of($strClass, AbstractAction::class)) {
            throw new \Exception(sprintf("Argument #2 (\$strClass) must be of type %s", $strClass));
        }
        $this->actions[$actionId] = [$strClass, $viewModel];
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
        return empty($actionId) ? $this->executeDefaultAction() : $this->executeExternalAction($actionId);                        
    }
    
    protected function executeDefaultAction()
    {
        $function = $this->getAction('defaultAction');
        return $function();
    }
    
    protected function executeExternalAction($actionId)
    {        
        list($actionClass, $viewModel) = $this->getAction($actionId);
        $actionParams = request()->input('actionParameters') ?? [];
        $action = new $actionClass();
        $action->setResponse(new ActionResponse());
        if (!empty($viewModel)) {
            $action->setViewModel(new $viewModel);
        }
        $response = $action->execute(...$actionParams);
        if (!empty($response)) {
            $action->getResponse()->alert($response);
        }
        return $action->getResponse()->send();
    }
}
