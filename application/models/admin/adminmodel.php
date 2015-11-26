<?php
class adminModel extends CI_Model
{
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// VALID_ADMIN (NEW)
	public function VALID_ADMIN()
	{
		# LOGOUT
		if ( isset($_GET['logout']) ){
			$this->session->unset_userdata('admin');
			redirect('/admin');
			exit;
		}
		
		# RECOVER PASSWORD
		if ( isset($_POST['recover']) ){
			// $this->adminModel->recoverPassword();
			redirect('/admin');
			exit;
		}
		
		# AUTH
		if ( isset($_POST['login']) ){
			$login	= isset($_POST['login']) ? $_POST['login'] : '';
			$password	= isset($_POST['password']) ? $_POST['password'] : '';		

			$admin = $this->db->query('SELECT * FROM admin WHERE login = "'.mysql_real_escape_string($login).'" AND password = "'.sha1($password).'"')->row();
			
			if ($admin){
				$this->session->set_userdata('admin', TRUE);
			}
			redirect('/admin');
			exit;
		}
		
		# VALID
		if ( ! $this->session->userdata('admin') ){
			if ($this->uri->total_segments() > 1) redirect('/admin');
			$this->load->view('admin/a_login');
			$this->output->_display();
			exit;
		}
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// GET ADMIN (NEW)
	public function getAdmin()
	{
		return $this->db->query('SELECT * FROM admin')->row();
	}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// DISCOUNTS
	public function getDiscounts()
	{
		return $this->db->query('SELECT * FROM discount ORDER BY `order` ASC')->result();
	}
}
