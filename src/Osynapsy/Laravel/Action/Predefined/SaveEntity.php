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
            $this->closeView();
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    protected function closeView()
    {
        $this->getResponse()->goto('back');
    }
}
