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
            $this->getViewModel()->save();
            $this->getResponse()->goto('back');
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
}
