<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function __construct(){
		parent::__construct();

        // To use site_url and redirect on this controller.
        $this->load->library(array('facebook'));
	}
	
	public function index()
	{
		
		if($this->session->userdata('login') == true){
			redirect('welcome/profile');
		}
		
		if(isset($_GET['state'])){
		
			$user = $this->facebook->getUser();
        
			if ($user) {

				try {
					$data['user_profile'] = $this->facebook->api('/me?fields=email,first_name,last_name,gender,name,birthday,location');
				} catch (FacebookApiException $e) {
					$user = null;
				}
				
				$this->session->set_userdata('login',true);
				$this->session->set_userdata('user_profile',$data['user_profile']);
				redirect('welcome/profile');

			} 
		
		} else {
			$contents['link'] = $this->facebook->getLoginUrl(array(
                'redirect_uri' => site_url('welcome/index'), 
                'scope' => array("email") // permissions here
            ));
			
			$this->load->view('welcome_message',$contents);
		}
	}
	
	public function login(){

		$this->load->library('facebook'); // Automatically picks appId and secret from config
        // OR
        // You can pass different one like this
        //$this->load->library('facebook', array(
        //    'appId' => 'APP_ID',
        //    'secret' => 'SECRET',
        //    ));

		$user = $this->facebook->getUser();
        
        if ($user) {
            try {
                $data['user_profile'] = $this->facebook->api('/me');
            } catch (FacebookApiException $e) {
                $user = null;
            }
        }else {
            // Solves first time login issue. (Issue: #10)
            //$this->facebook->destroySession();
        }

        if ($user) {

            $data['logout_url'] = site_url('welcome/logout'); // Logs off application
            // OR 
            // Logs off FB!
            // $data['logout_url'] = $this->facebook->getLogoutUrl();

        } else {
            $data['login_url'] = $this->facebook->getLoginUrl(array(
                'redirect_uri' => site_url('welcome/login'), 
                'scope' => array("email") // permissions here
            ));
        }
        $this->load->view('login',$data);

	}

    public function profile(){
		if($this->session->userdata('login') != true){
			redirect('');
		}
		$contents['user_profile'] = $this->session->userdata('user_profile');
		$this->load->view('profile',$contents);
		
	}
	
	public function logout(){
		$this->session->sess_destroy();
		redirect('');
		
	}

}

