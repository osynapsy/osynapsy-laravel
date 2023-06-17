<?php
namespace Osynapsy\Laravel\Action\Predefined;

use Osynapsy\Laravel\Action\AbstractAction;

/**
 * Description of DeleteEntity
 *
 * @author peter
 */
class DeleteEntity extends AbstractAction
{
   public function execute(...$params)
    {
        try {
            $this->beforeDelete();
            $this->getViewModel()->getLaravelModel()->delete();
            $this->afterDelete();
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    protected function beforeDelete()
    {
    }

    protected function afterDelete()
    {
        $this->getResponse()->goto('back');
    }
}
