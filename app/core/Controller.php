<?php
//*************************************************************************
//* main controller
//*************************************************************************
//*************************************************************************
//* namespaces
//*************************************************************************
namespace app\core;

//*************************************************************************
//* class loading
//*************************************************************************
use app\core\View;



abstract class Controller{

    /**
     * @var
     */

    public $route;
    public $views;
    public $acl;

    public function __construct($route) {
        $this->route = $route;
        $this->views = new View($route);
        $this->model = $this->loadModel($route['controller']);
        $this->startAclChecking();
       
        
    }

    /**
     * load models
     * @return void
     */
    
    public function loadModel(string $name) {
        $path = 'app\admin\models\\'.ucfirst($name);
        if(class_exists($path)) {
            return new $path;
        }
    }

    /**
     * here we are checking is there pages or not
     * @return void
     */

    public function startAclChecking() {
        if (!$this->checkAcl()) {
			View::errorCode(403);
		}
    }

     /**
     * acl checking
     * @return boolean
     */

    public function checkAcl() : bool {
		$this->acl = require 'acl/'.$this->route['controller'].'.php';
		if ($this->isAcl('all')) {
            debug($this->acl);
            return true ;
		}
		elseif (isset($_SESSION['authorize']['id']) and $this->isAcl('authorize')) {
			return true;
		}
		elseif (!isset($_SESSION['authorize']['id']) and $this->isAcl('guest')) {
			return true;
		}
		elseif (isset($_SESSION['admin']) and $this->isAcl('admin')) {
			return true;
		}
		return false;
	}

    /**
     * acl checking key
     * @param $string
     * @return boolean
     */

	public function isAcl(string $key) : bool {
		return in_array($this->route['action'], $this->acl[$key]);
	}



}
