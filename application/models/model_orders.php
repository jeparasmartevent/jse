<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_orders extends CI_Model {
	
	public function get_user_id_by_session()
	{ 
		$usr_name = $this->session->userdata('username');
		$gry = $this->db->where('usr_name',$usr_name)
						->select('usr_id')
						->limit(1)
						->get('users');
				if($gry->num_rows() > 0 )
					{
							return $gry->row()->usr_id;
					}else{
						
							return 0;
						 }	
	}
	
	
	public function process()
	{
				
		//here for create new invoice
		$invoice = array(
						'data'		=>	date('Y-m-d H:i:s'),
						'due_date'	=>	date('Y-m-d H:i:s',mktime(date('H'),date('i'),date('s'),date('m'),date('d') + 7,date('Y'))),
						'user_id'	=> $this->get_user_id_by_session(),
						'status'	=>	'unpaid'
						);
		$this->db->insert('invoices',$invoice);
		$invoice_id = $this->db->insert_id();
		//here for put ordered items in orders table
		foreach ($this->cart->contents() as $item)
		{
			$data = array(
						'invoice_id'		=> $invoice_id,
						'product_id'		=> $item['id'],
						'product_type'		=> $item['name'],
						'product_title'		=> $item['title'],
						'qty'				=> $item['qty'],
						'price'				=> $item['price']
						
						 );
			$this->db->insert('orders',$data);
		}

		//send email
		$usr_name = $this->session->userdata('username');
		$from_email = 'jeparasmart*****.info@gmail.com';
		$to_email = $this->session->userdata('email');
		$subject = 'Verifikasi Pembayaran';
		$message =

		'
		<html>
		<head>
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<style type="text/css">
		        .tombol{
		                  background:red;
		                  color:white;
		                  padding:10px 50px;
		                  text-decoration:none;
		                  border-radius: 5px;
		                  font-family:sans-serif;
		                  font-size:12pt;
		                }
		    </style>
		</head>
		<body>
			<h2>Pembayaran Produk JeparaSmartEvent</h2>
      		<hr/>
		  	<p>
		  		Halo '.$usr_name.', Anda telah melakukan request untuk pembelian produk dari JSE, batas waktu pembayaran adalah 7 Hari dari waktu pemesanan.
		  		Silahkan melakukan tranfer ke Mandiri, BRI, atau BCA dengan rincian berikut.
		  	</p>
		  	<br/>
		  	<h3>Bank Mandiri</h3>
		  	<p>Bank : Mandiri</p><p>No. Rekening : 1430033210887</p>
		  	<h3>Bank BRI</h3>
		  	<p>Bank : BRI</p><p>No. Rekening : 58887677879998</p>
		  	<h3>Bank BCA</h3>
		  	<p>Bank : BCA</p><p>No. Rekening : 2213446667646</p>
		  	<br/>
		  	<p>Atas nama : PT. Jepara Smart Event</p>
		  	<p><strong>Jumlah Transfer : Rp. '.$this->cart->format_number ($item['price']).'</strong></p>
		  	<p>Jika Anda sudah melakukan pembayaran silahkan klik konfirmasi permbayaran dibawah ini</p>
		  	<p><a href='. site_url("customer/payment_confirmation/$invoice_id") . ' class="tombol">Konfirmasi Pembayaran</a></p>
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
		
		return TRUE;
	}
	
	public function all_invoices()
	{ // get all orders from orders tble
		$get_orders = $this->db->get('invoices');
			if($get_orders->num_rows() > 0 ) {
					return $get_orders->result();
			} else {
					 return array();
			}
	}
	public function get_invoice_by_id($invoice_id)
	{
		$get_invoice_by = $this->db->where('id',$invoice_id)->limit(1)->get('invoices');
		if($get_invoice_by->num_rows() > 0 ) {
					return $get_invoice_by->result();
			} else {
					 return FALSE;
					}
	}
	
	public function get_orders_by_invoice($invoice_id)
	{
		$get_orders_by = $this->db->where('invoice_id',$invoice_id)->get('orders');
		if($get_orders_by->num_rows() > 0 ) {
					return $get_orders_by->result();
			} else {
					 return FALSE;
					}
	}

	public function upstatus($id,$data_status)
		{	
			$this->db->where('id',$id)
					->update('invoices',$data_status);
						
		}
	
	
}//end class
