<?php 
namespace App\Config;

class Request{
    
    protected $url                  ='';
    protected $acction              ='';
    protected $controller           ='';
    protected $ControllerClassName  ='';
    protected $controllerfileName   ='';
    protected $defaultControler     = 'home';
    protected $defaultAction        ='index';
    protected $params               = array();

    public function __construct($url) {
        
        $this->url=$url;
        
        $segment = explode('/', $this->getUrl());
        
        $this->resolveController($segment);
        $this->resolveAction($segment);
        $this->resolveParams($segment);
        
    }
    
    public function resolveController(&$segment){
        $this->controller = array_shift($segment);
        
        if(empty($this->controller)){
            $this->controller = $this->defaultControler;
        }
    }
    
    public function resolveAction(&$segment){
        $this->acction = array_shift($segment);
        
        if(empty($this->acction)){
            $this->acction = $this->defaultAction;
        }
    }
    
    public function resolveParams(&$segment){
        $this->params = $segment;
    }

    public function getUrl() {
        return $this->url;
    }
    
    public function getController() {
        return $this->controller;
    }

    public function getControllerClassName() {
        return Inflector::camell($this->getController().'Controller');
    }

    public function getControllerfileName() {
        return $this->fileName = 'App/Controller/'.$this->getControllerClassName().'.php';
    }
    
    public function getAction(){
        return $this->acction;
    }
    
    public function getActionMethodName(){
        return Inflector::lowercamell($this->getAction().'Action');
    }
    
    public function getParams() {
        return $this->params;
    }
    
    public function execute(){
        $controllerClassName    = $this->getControllerClassName();
        $controllerFileName     = $this->getControllerfileName();
        $actionMethodName       = $this->getActionMethodName();
        $params                 = $this->getParams();
        
        if(!file_exists($controllerFileName)){
            exit("El controlador no existe");
        }
        
        
        $clase_controller = 'App\\Controller\\'.$controllerClassName;
        $controller = new $clase_controller();
        
        call_user_func_array([$controller, $actionMethodName], $params);
    }
}