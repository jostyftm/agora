<?php 

namespace App\Config;

class View extends Response{
    
    protected $template = '';
    protected $vars     = array();
    protected $folder   = '';


    public function __construct($folder, $template, $vars=array()) {
        $this->template = $template;
        $this->vars     = $vars;
        $this->folder   = $folder;
    }
    
    public function getTemplate() {
        return $this->template;
    }

    public function getVars() {
        return $this->vars;
    }
    public function getFolder() {
        return $this->folder;
    }
    
    public function redirecTo($url){
        
        header('Location: '.$url.$this->getTemplate());
    }

    public function execute(){
        
        $template   = $this->getTemplate();
        $vars       = $this->getVars();
        $folder     = $this->getFolder();
        
        call_user_func(function()use ($folder, $template, $vars){
           extract($vars);
           require 'App/View/'.$folder.'/'.$template.'.tpl.php';
        });
    }
    
}