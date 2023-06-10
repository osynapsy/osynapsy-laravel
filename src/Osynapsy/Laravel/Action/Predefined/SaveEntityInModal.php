<?php
namespace Osynapsy\Laravel\Action\Predefined;



/**
 * Description of SaveEntity
 *
 * @author Pietro Celeste <pietro.celeste@gmail.com>
 */
class SaveEntityInModal extends SaveEntity
{
    protected function getComponentIdsToRefresh() : array
    {
        return [];
    }

    protected function closeView()
    {
        $componentIds = $this->getComponentIdsToRefresh();
        if (!empty($componentIds)) {
            $this->getResponse()->js(sprintf("parent.Osynapsy.refreshComponents(['%s']);", implode("','", $componentIds)));
        }
        $this->getResponse()->js("parent.Osynapsy.modal.instance.hide();");
    }
}
