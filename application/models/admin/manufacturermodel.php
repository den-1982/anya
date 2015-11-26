<?php
class manufacturerModel extends CI_Model
{
	public function getManufacturers()
	{
		return $this->db->query('SELECT * FROM manufacturer ORDER BY `order`')->result();
	}
	
	public function getManufacturer($id = 0)
	{
		$id = abs((int)$id);
		return $this->db->query('SELECT * FROM manufacturer WHERE id = '.$id)->row();
	}
	
	public function setVisibilityManufacturer(){
		$id		= isset($_POST['id']) ? abs((int)$_POST['id']) : 0;
		$activ	= (int)$_POST['activ'] ? 0 : 1;
		
		$this->db->query('UPDATE manufacturer SET visibility = '.$activ.' WHERE id = '.$id);
		
		return $activ;
	}
	
	public function addManufacturer()
	{
		$name = mysql_real_escape_string(trim($_POST['name']));
		$image = ''; //mysql_real_escape_string(trim($_POST['image']));
		$this->db->query('INSERT INTO manufacturer (name, image) VALUES("'.$name.'", "'.$image.'")');
		
		$id = $this->db->query('SELECT MAX(id) AS id FROM manufacturer LIMIT 1')->row()->id;
		
		$dir = ROOT.'/img/manufacturer/'.$id.'/';
		if ( ! is_dir($dir)){ mkdir($dir, 0755, true);}
		
		if (isset($_FILES['image'])){
			$img = $_FILES['image'];
			if ($img['error'] == 0){
				$i = $dir.$id.'.jpg';
				$this->my_imagemagic->upload($img['tmp_name'], $i);
			}
		}
		
		return;
	}
	
	public function updateManufacturer()
	{
		$id = abs((int)$_POST['id']);
		$name = mysql_real_escape_string(trim($_POST['name']));
		$image = ''; //mysql_real_escape_string(trim($_POST['image']));
		$this->db->query('UPDATE manufacturer SET name = "'.$name.'", image = "'.$image.'" WHERE id = '.$id);
		
		$dir = ROOT.'/img/manufacturer/'.$id.'/';
		if ( ! is_dir($dir)){ mkdir($dir, 0755, true);}
		
		if (isset($_FILES['image'])){
			$img = $_FILES['image'];
			if ($img['error'] == 0){
				$i = $dir.$id.'.jpg';
				$this->my_imagemagic->upload($img['tmp_name'], $i);
			}
		}
		
		return;
	}
	
	public function delManufacturer($id = 0)
	{
		$id = abs((int)$id);
		$this->db->query('DELETE FROM manufacturer WHERE id = '.$id);
		
		$dir = ROOT.'/img/manufacturer/'.$id;
		
		if (is_dir($dir)){
			delDir($dir);
		}
		
		return 0;
	}
}



