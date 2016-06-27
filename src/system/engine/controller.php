<?php
abstract class Controller {
	protected $registry;	
	protected $id;
	protected $layout;
	protected $template;
	protected $children = array();
	protected $data = array();
	protected $output;
	
	public function __construct($registry) {
		$this->registry = $registry;
	}
	
	public function __get($key) {
		return $this->registry->get($key);
	}
	
	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}
			
	protected function forward($route, $args = array()) {
		return new Action($route, $args);
	}

	protected function redirect($url, $status = 302) {
		header('Status: ' . $status);
		header('Location: ' . str_replace('&amp;', '&', $url));
		exit();
	}
	
	protected function getChild($child, $args = array()) {
		$action = new Action($child, $args);
		$file = $action->getFile();
		$class = $action->getClass();
		$method = $action->getMethod();
	
		if (file_exists($file)) {
			require_once($file);

			$controller = new $class($this->registry);
			
			$controller->$method($args);
			
			return $controller->output;
		} else {
			trigger_error('Error: Could not load controller ' . $child . '!');
			exit();					
		}		
	}

  	public function clean($data) {
    	if (is_array($data)) {
	  		foreach ($data as $key => $value) {
                if ($key != 'pagination') {
                    unset($data[$key]);
                    $data[$key] = $this->clean($value);
                }
	  		}
		} else { 

            if (!(strpos($data, "http://") === 0 ||
                  strpos($data, "https://") === 0)) {
                //error_log("clean data: " . $data);

                $data = htmlspecialchars($data, ENT_COMPAT);
            }
		}

		return $data;
	}
	
	protected function render() {
        //error_log("entry_review:" . $this->data['entry_review']);

        $this->data = $this->clean($this->data);
        //var_dump($this->data);

		foreach ($this->children as $child) {
			$this->data[basename($child)] = $this->getChild($child);
		}
		
		if (file_exists(DIR_TEMPLATE . $this->template)) {

			extract($this->data);
			
      		ob_start();
      
	  		require(DIR_TEMPLATE . $this->template);
      
	  		$this->output = ob_get_contents();

      		ob_end_clean();

			return $this->output;
    	} else {
			trigger_error('Error: Could not load template ' . DIR_TEMPLATE . $this->template . '!');
			exit();	
    	}
	}

    protected function getWithDefault($data, $key, $default_value) {
        if (!empty($data))
            return $data[$key];
        return $default_value;

    }
}
?>