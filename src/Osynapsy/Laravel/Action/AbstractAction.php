<?php
namespace Osynapsy\Laravel\Action;

/**
 * Description of AbstractAction
 *
 * @author Pietro Celeste <pietro.celeste@gmail.com>
 */
abstract class AbstractAction
{
    protected $controller;
    protected $parameters;
    protected $response;

    abstract public function execute(...$params);   

    public function getController()
    {
        return $this->controller;
    }

    public function getDb()
    {
        return $this->controller->getDb();
    }

    public function getModel()
    {
        return $this->getController()->getModel();
    }

    public function getParameter($key)
    {
        return array_key_exists($key, $this->parameters) ? $this->parameters[$key] : null;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setController(Controller $controller)
    {
        $this->controller = $controller;
    }

    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }
    
    public function setResponse(ActionResponse $response)
    {
        $this->response = $response;
    }

    public function raiseException($message, $code = 501)
	{
		throw new \Exception($message, $code);
	}
}
