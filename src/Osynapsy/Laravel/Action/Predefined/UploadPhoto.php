<?php
namespace Osynapsy\Laravel\Action\Predefined;

use Osynapsy\Laravel\Action\AbstractAction;

/**
 * Description of UploadPhoto
 *
 * @author peter
 */
class UploadPhoto extends AbstractAction
{
    public function execute(...$params)
    {        
        $fieldId = $params[0];
        $fileFileldId = $fieldId . '_file';
        $filePath = request()->hasfile($fileFileldId) ? $this->saveFile(request()->file($fileFileldId)) : '';        
        $this->getResponse()->js(sprintf("document.getElementById('%s').value = '%s'", $fieldId, $filePath));
        $this->getResponse()->js(sprintf("document.getElementById('%s_file').value = ''", $fieldId));
        $this->getResponse()->js(sprintf("document.getElementById('%s_preview').innerHTML = '<img src=\"%s\" style=\"max-width: 100%%\">'", $fieldId, $filePath));
    }
    
    protected function saveFile($file)
    {
        $repoRoot = $this->getRepoRoot();
        $result = sprintf('/%s/%s', $repoRoot, $file->getClientOriginalName());
        $file->move($repoRoot, $file->getClientOriginalName());
        return $result;        
    }
    
    protected function getRepoRoot()
    {
        return 'upload';
    }
}
