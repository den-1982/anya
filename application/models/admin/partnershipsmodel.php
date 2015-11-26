<?php
class partnershipsModel extends CI_Model
{
	public function getPartnerships()
	{
		return $this->db->query('SELECT * FROM partnerships ORDER BY `order` ASC')->result();
	}
	
	public function getPartnership($id = 0)
	{
		$id = abs((int)$id);
		return $this->db->query('SELECT * FROM partnerships WHERE id = '.$id)->row();
	}
	
	public function setVisibilityPartnership(){
		$id		= isset($_POST['id']) ? abs((int)$_POST['id']): 0;
		$activ	= (int)$_POST['activ'] ? 0 : 1;
		
		$this->db->query('UPDATE partnerships SET visibility = '.$activ.' WHERE id = '.$id);
		
		return $activ;
	}
	
	public function addPartnerships()
	{
		$name = mysql_real_escape_string(trim($_POST['name']));
		$url = mysql_real_escape_string(trim($_POST['url']));
		$text = mysql_real_escape_string(trim($_POST['text']));
		$image = mysql_real_escape_string(trim(strip_tags($_POST['image'])));
		
		$this->db->query('INSERT INTO partnerships (name, url, text, image) VALUES("'.$name.'", "'.$url.'", "'.$text.'", "'.$image.'")');
		return;
	}
	
	public function updatePartnerships()
	{
		$id		= abs((int)$_POST['id']);
		$name 	= mysql_real_escape_string(trim($_POST['name']));
		$url 	= mysql_real_escape_string(trim($_POST['url']));
		$text 	= mysql_real_escape_string(trim($_POST['text']));
		$image 	= mysql_real_escape_string(trim(strip_tags($_POST['image'])));
		
		$this->db->query('UPDATE partnerships SET	name = "'.$name.'",
													url = "'.$url.'",
													text = "'.$text.'",
													image = "'.$image.'"
												WHERE id = '.$id);
		
		return;
	}
	
	public function delPartnership($id = 0)
	{
		$id = abs((int)$id);
		$this->db->query('DELETE FROM partnerships WHERE id = '.$id);
		return 0;
	}

}