<?php       
class ControllerCommonLogout extends Controller {   
	public function index() { 
    	$this->user->logout();
 
 		unset($this->session->data['token']);

        setcookie("A_LOGIN", NULL, 0, "/", "");

		$this->redirect($this->url->link('common/login', '', 'SSL'));
  	}
}  
?>