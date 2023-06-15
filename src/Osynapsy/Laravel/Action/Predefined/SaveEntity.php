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
            $this->validateUserInput(request());
            $this->getViewModel()->save();
            $this->afterSave();
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    protected function validateUserInput($request)
    {        
    }

    protected function afterSave()
    {
        $this->getResponse()->goto('back');
    }
}
