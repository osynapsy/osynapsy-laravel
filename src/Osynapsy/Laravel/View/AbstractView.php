<?php
namespace Osynapsy\Laravel\View;

use Osynapsy\Html\Tag;
use Osynapsy\Html\Component;
use Osynapsy\Html\DOM;
use Osynapsy\Laravel\ViewModel\AbstractViewModel;
use Illuminate\Support\Facades\Blade;

/**
 * Description of View
 *
 * @author Pietro Celeste <pietro.celeste@gmail.com>
 */
abstract class AbstractView
{    
    protected $layout;
    protected $title;
    protected $scripts = [];    
    protected $css = [];
    protected $viewModel;

    abstract protected function init();       

    public function render()
    {
        $contentView = $this->init();
        $strComponentsToRefresh = request()->header('Osynapsy-Html-Components');
        return empty($strComponentsToRefresh) 
               ? $this->renderFullView($contentView) 
               : $this->renderComponents($strComponentsToRefresh);        
    }
    
    protected function renderFullView($contentView)
    {
        $requires = DOM::getRequire();
        if (!empty($requires)) {            
            $this->appendRequires($requires);
        }        
        $bladeCode = $this->bladeCodeFactory($contentView);
        return Blade::render($bladeCode);
    }
    
    protected function appendRequires($requires)
    {
        foreach ($requires as $require) {
            $url = $require[0];
            $method = 'add'.$require[1];
            if (method_exists($this, $method)) {
                $this->{$method}($url);
            }
        }            
    }
    
    protected function renderComponents($strComponentsToRender)
    {
        $componentIds = explode(';', $strComponentsToRender);                
        $response = new Tag('div', 'response');            
        foreach($componentIds as $id) {
            $response->add(Component::getById($id));                    
        }
        return $response;
    }
    
    protected function bladeCodeFactory($contentView)
    {
        $bladeCode = [];
        if (!empty($this->layout)) {
            $bladeCode[] = sprintf("@extends('%s')", $this->layout);
        }
        $bladeCode[] = "@section('title')";
        $bladeCode[] = $this->title;
        $bladeCode[] = "@endsection";
        $bladeCode[] = "@section('content')";
        $bladeCode[] = $contentView;
        $bladeCode[] = "@endsection";
        $bladeCode[] = "@section('osynapsyjs')";
        $bladeCode[] = '<link href="/assets/vendor/osynapsy/css/style.css?ver=0.8.7-DEV" rel="stylesheet" />';
        $bladeCode[] = sprintf('<script src="/assets/vendor/osynapsy/js/Osynapsy.js?ver=0.8.7-DEV" id="osynapsyjs" token="%s"></script>', csrf_token());
        $bladeCode[] = implode(PHP_EOL, $this->scripts);
        $bladeCode[] = implode(PHP_EOL, $this->css);
        $bladeCode[] = "@endsection";
        return implode(PHP_EOL, $bladeCode).PHP_EOL;
    }

    public function __toString()
    {
        return $this->render();
    }                
    
    public function addJs($filePath)
    {    
        $script = sprintf('<script src="%s"></script>', $filePath);
        $this->appendScript($script);
    }
    
    public function addScript($code)
    {
        $script = sprintf("<script>\n%s\n</script>", $code);
        $this->appendScript($script);
    }
    
    protected function appendScript($script)
    {
        if (!in_array($script, $this->scripts)) {
            $this->scripts[] = $script;
        }
    }
    
    public function addCss($filePath)
    {    
        $css = sprintf('<link href="%s" rel="stylesheet">', $filePath);
        $this->appendCss($css);
    }
           
    public function addStyle($style)
    {
        $css = sprintf("<style>\n%s\n</style>",$style);
        $this->appendCss($css);
    }
    
    protected function appendCss($css)
    {
        if (!in_array($css, $this->css)) {
            $this->css[] = $css;
        }
    }
    
    protected function getViewModel() : AbstractViewModel
    {
        return $this->viewModel;
    }
    
    protected function setLayout($layout)
    {
        $this->layout = $layout;
    }
    
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    public function setViewModel(AbstractViewModel $viewModel)
    {
        $this->viewModel = $viewModel;
    }
}

