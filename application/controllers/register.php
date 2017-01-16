<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller {
	
	public function __construct ()
	{
		parent::__construct();
		$this->load->model('model_users');
		$this->load->model('model_settings');
		$this->load->library('email');
	}
		
	public function index()
	{
		
		$this->form_validation->set_rules('rusername','Username','required|min_length[4]|max_length[20]');
		$this->form_validation->set_rules('remail','Email','required|valid_email');  
		$this->form_validation->set_rules('rpassword','Password','required|matches[repassword]|min_length[6]|max_length[24]|md5');
		$this->form_validation->set_rules('repassword','Password','required');
		
		if($this->form_validation->run()	==	FALSE)
		{
			$data['get_sitename'] = $this->model_settings->sitename_settings();
			$data['get_footer'] = $this->model_settings->footer_settings();	
			$this->load->view('login/form_login',$data); 
			
		}else{
				$data_register_new = array
				 (
					'usr_name'			=> set_value('rusername'),
					'usr_email'			=> set_value('remail'),
					'usr_password'		=> set_value('rpassword'),
					'stuts'				=> '0',
					'usr_group'				=>'3'
				 );
				 if($this->model_users->is_usr() == FALSE)
				 {
				 	  if ($this->model_users->kirimEmail(set_value('remail')))
	                  {
	                    // successfully sent mail
	                    $this->session->set_flashdata('msg','<div class="alert alert-success text-center">You are Successfully Registered! <a href="https://mail.google.com/">Please confirm the mail sent to your Email-ID</a>!!!</div>');

		                    $this->model_users->register_new($data_register_new);
									 $this->form_validation->set_rules('rusername');
									 $this->form_validation->set_rules('rpassword');
									 if($this->form_validation->run()	==	FALSE)
									 {
											$this->load->view('login/form_login'); 	
									 }else{

											$valid_user	= $this->model_users->check();
											 if($valid_user	==	FALSE)
											 {
												 $this->session->set_flashdata('error','Username / Password Not Correct !' );
												 redirect('login');
											 }else{
													 $this->session->set_userdata('username',$valid_user->usr_name);
													 $this->session->set_userdata('group',$valid_user->usr_group);
													 switch($valid_user->usr_group)
													 {
														 case 3 ://for member
														 redirect(base_url());
														 break;
														 
														 default: break;
													 }
											 }
									 }
						 
						 redirect(base_url());

	                    redirect('register');
	                  }
	                  else
	                  {
	                    // error
	                    $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">Oops! Error. Cek your connection, Please try again later!!!</div>');
	                    redirect('register');
	                  }

							 
				 }else{
						$this->session->set_flashdata('error','Please Write Other User Name !' );
						redirect('register');
				 }
		}
	}

	function verify($hash=NULL)
        {
          
          if ($this->model_users->verifikasiidEmail($hash))
          {
            $this->session->set_flashdata('verify_msg','<div class="alert alert-success text-center">Your Email Address is successfully verified! Please login to access your account!</div>');
            redirect(base_url());
          }
          else
          {
            $this->session->set_flashdata('verify_msg','<div class="alert alert-danger text-center">Sorry! There is error verifying your Email Address! <a href="https://mail.google.com/">Please confirm the mail sent to your Email-ID</a>!!!</div>');
            redirect(base_url());
          }
        }  
	
}