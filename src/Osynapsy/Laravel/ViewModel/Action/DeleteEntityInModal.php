<?php
namespace Osynapsy\Laravel\ViewModel\Action;


/**
 * Description of DeleteEntityInModal
 *
 * @author Pietro Celeste <pietro.celeste@gmail.com>
 */
class DeleteEntityInModal extends DeleteEntity
{
    protected function getComponentIdsToRefresh() : array
    {
        return [];
    }

    protected function afterDelete()
    {
        $componentIds = $this->getComponentIdsToRefresh();
        if (!empty($componentIds)) {
            $this->getResponse()->js(sprintf("parent.Osynapsy.refreshComponents(['%s']);", implode("','", $componentIds)));
        }
        $this->getResponse()->js("parent.Osynapsy.modal.instance.hide();");
    }
}
