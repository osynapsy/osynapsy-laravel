<?php
namespace Osynapsy\Laravel\Action;

/**
 * Description of Envelope
 *
 * @author peter
 */
class ActionResponse
{
    protected $repo = [];
    
    public function __construct()
    {
        $this->js(sprintf("document.getElementById('osynapsyjs').setAttribute('token','%s')", csrf_token()));
    }
    
    public function add($target, $message)
    {        
        $this->repo[] = [$target, $message];
    }
    
    public function alert($message)
    {
        $this->js(sprintf("alert('%s')", $message));
    }
    
    public function goto($url)
    {
        $this->add('goto', $url);
    }
    
    public function js($code)
    {
        $this->add('execCode', str_replace(PHP_EOL,'\n',$code));
    }
    
    public function send()
    {
        return json_encode($this->repo, true);
    }
}
