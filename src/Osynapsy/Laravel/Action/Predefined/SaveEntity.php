<?php
namespace Osynapsy\Laravel\Action\Predefined;

use Osynapsy\Laravel\Action\AbstractAction;

/**
 * Description of SaveEntity
 *
 * @author peter
 */
class SaveEntity extends AbstractAction
{
    public function execute(...$params)
    {
        try {
            $this->getViewModel()->save();
            $this->getResponse()->goto('back');
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
}
