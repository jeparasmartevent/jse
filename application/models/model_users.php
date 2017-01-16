<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_users extends CI_Model {
		
	public function check_usr()
		{
			$username = set_value('username');	
			$password = set_value('password');	
			$stuts = '1';
			$gry = $this->db->where('usr_name',$username)
							->where('usr_password',$password)
							->where('stuts',$stuts)
							->limit(1)
							->get('users');
			if($gry->num_rows()	>	0)
			{
				return $gry->row();	
			}else{
					return array();
			}
				
				
		}//end check_usr function
		
	public function check_user_is_active()
		{
			//if the user try to login and his account is not acctive
			$username = set_value('username');	
			$password = set_value('password');	
			$stuts = '0';
			$gry = $this->db->where('usr_name',$username)
							->where('usr_password',$password)
							->where('stuts',$stuts)
							->limit(1)
							->get('users');
			if($gry->num_rows()	>	0)
			{
				return $gry->row();	
			}else{
					return array();
			}
				
				
		}
	//send verification email to user's email id
	function kirimEmail($to_email)
	{
		$from_email = 'jeparasmart****.info@gmail.com';
		$subject = 'Verify Your Email Address';
		$encrypted_email = md5($to_email);
		$message =

		'
		<html>
		<head>
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<style type="text/css">
		        .tombol{
		                  background:#2C97DF;
		                  color:white;
		                  padding:10px 75px;
		                  text-decoration:none;
		                  border-radius: 5px;
		                  font-family:sans-serif;
		                  font-size:15pt;
		                  height: 200px;
		                }
		    </style>
		</head>
		<body>
			<p>Dear User,</p>
      		<hr/>
		  	<p>Terima kasih anda telah bergabung dengan <b>JeparaSmartEvent</b> | Solution For your Event</p>
		  	<p>Tinggal satu langkah lagi untuk mengaktifkan akun anda. silahkan klik link dibawah ini untuk mengkonfirmasi pendaftaran anda</p>
		  	<br/>
		  	<p><a href='. site_url("register/verify/$encrypted_email") . ' class="tombol">Konfirmasi Pendaftaran</a></p>
		  	<br/>
		  	<p>Demikian kami sampaikan Selamat Menjelajah</p>
		  	<hr/>
		  	<p>Thanks</p>
		  	<p><strong>JeparaSmartEvent Team</strong></p>
		</body>
		</html> 
		';

		//configure email settings
		$config['protocol'] = 'smtp';
		$config['smtp_host'] = 'ssl://smtp.gmail.com'; //smtp host name
		$config['smtp_port'] = '465'; //smtp port number
		$config['smtp_timeout']= '400';
		$config['smtp_user'] = $from_email;
		$config['smtp_pass'] = '******'; //$from_email password
		$config['mailtype'] = 'html';
		$config['charset'] = 'utf-8';
		$config['wordwrap'] = TRUE;
		$config['newline'] = "\r\n"; //use double quotes
		$this->email->initialize($config);
		
		//send mail
		$this->email->from($config['smtp_user'], 'Admin JeparaSmartEvent');
		$this->email->to($to_email);
		$this->email->subject($subject);
		$this->email->message($message);
		return $this->email->send();
	}

	//activate user account
	function verifikasiidEmail($key)
	{
		$data = array('stuts' => 1);
		$this->db->where('md5(usr_email)', $key);
		return $this->db->update('users', $data);
	}
	
	public function members()
		{ 
			$member = $this->db->get('users');
			if($member->num_rows() > 0 ) {
				return $member->result();
			} else {
				return array();
			} //end if num_rows
			
		}//end function member
	
	public function active($usr_id,$data_user)
		{	
			$this->db->where('usr_id',$usr_id)
					->update('users',$data_user);
						
		}
	
	public function disable($usr_id,$data_user)
		{	
			$this->db->where('usr_id',$usr_id)
			->update('users',$data_user);
			
		}
		
	public function register_new($data_register_new)
	{
		$this->db->insert('users',$data_register_new);		
	}
	
	public function is_usr()
	{
		$username = set_value('rusername');	
		$gry = $this->db->where('usr_name',$username)
		->limit(1)
		->get('users');
		if($gry->num_rows()	>	0)
		{
			return TRUE;	
			}else{
			return FALSE;
		}
		
		
	}
	
	public function check()
	{
		$username = set_value('rusername');	
		$password = set_value('rpassword');	
		$stuts = '1';
		$gry = $this->db->where('usr_name',$username)
		->where('usr_password',$password)
		->where('stuts',$stuts)
		->limit(1)
		->get('users');
		if($gry->num_rows()	>	0)
		{
			return $gry->row();	
			}else{
			return array();
		}
		
	}
	
		public function check_password_admin_for_change()
		{
			$old_password	= set_value('oldpassword_admin'); 	
			$usr_name = 'admin';
			$gry = $this->db->where('usr_name',$usr_name)
							->where('usr_password',$old_password)
							->limit(1)
							->get('users');
			if($gry->num_rows()	>	0)
			{
					return $gry->row();	
			}else{
					return array();
			}
		}
		public function m_change_admin_password($new_admin_password)
		{
			
			$usr_name = 'admin';
			$this->db->where('usr_name',$usr_name)	
					->update('users',$new_admin_password);
		}
	
	
		
	}///end class  ///
	
	
?>
		
