<?php
namespace lib;

class FrontController implements ControllerInterface
{
    const DEFAULT_CONTROLLER = "services\AddressingService";
    const DEFAULT_ACTION     = "index";
     
    protected $controller    = self::DEFAULT_CONTROLLER;
    protected $action        = self::DEFAULT_ACTION;
    protected $params        = array();
    protected $basePath      = "";
     
    public function __construct(array $options = array()) {
    	$this->basePath = substr(dirname($_SERVER['SCRIPT_NAME']), 1);
        if (empty($options)) {
           $this->parseUri();
   
        }
        else {
            if (isset($options["controller"])) {
                $this->setController($options["controller"]);
            }
            if (isset($options["action"])) {
                $this->setAction($options["action"]);     
            }
            if (isset($options["params"])) {
                $this->setParams($options["params"]);
            }
        }
    }
     
    protected function parseUri() {
        $path = trim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), "/");
        //$path = preg_replace('/[^a-zA-Z0-9]/', "", $path); 
        if (strpos($path, $this->basePath) === 0) {
            $path = substr($path, strlen($this->basePath)+1);
        } 
       if($path!=""){
        @list($controller, $action, $params) = explode("/", $path,3);
       }
        if (isset($controller)) { 
        	
            $this->setController($controller);
        }
        if (isset($action) && $action!="") {
            $this->setAction($action);
        }
        if (isset($params)) {
            $this->setParams(explode("/", $params));
        }
    }
     
    public function setController($controller) {
    	if($controller =="addressing"){
    		$controller = "services\\".ucfirst($controller)."Service";
    	}else{
        	$controller = ucfirst(strtolower($controller));
    	}
        if (!class_exists($controller)) {
            throw new \Exception(
                "The action controller '$controller' has not been defined.");
        }
        $this->controller = $controller;
        return $this;
    }
     
    public function setAction($action) {
        $reflector = new \ReflectionClass($this->controller);
        if (!$reflector->hasMethod($action)) {
            throw new \Exception(
                "The controller action '$action' has been not defined.");
        }
        $this->action = $action;
        return $this;
    }
     
    public function setParams(array $params) {
        $this->params = $params;
        return $this;
    }
     
    public function run() {
        call_user_func_array(array(new $this->controller, $this->action), $this->params);
    }
}