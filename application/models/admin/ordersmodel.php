<?php
class ordersModel extends CI_Model
{
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// ORDERS
	public function getOrder($id = 0)
	{
		$id = abs((int)$id);
		return $this->db->query('SELECT oi.*, 
										p.id AS pid, 
										p.url, 
										CONCAT("/", p.url, "/p", p.id) AS _url,
										p.name AS pname,
										CONCAT("/img/products/", p.id, "/", p.id, "_82_82.jpg") AS image,
										s.name AS sizeName,
										s.prefix AS prefix
									FROM order_items oi 
									LEFT JOIN products p ON p.id = oi.id_product 
									LEFT JOIN filter_item s ON s.id = oi.size
								WHERE oi.id_order = ' . $id )->result();
	}
	
	public function getOrdersOfUser($id = 0)
	{
		$id = abs((int)$id);
		$user = $this->db->query('SELECT * FROM users WHERE id = '.$id)->row();
		
		if ( ! $user) return array();
		
		$user->orders = $this->db->query('SELECT o.*, 
												u.id AS uid, 
												u.name AS uname 
											FROM orders o 
											LEFT JOIN users u ON u.id = o.id_user 
										WHERE o.id_user = '.$user->id.' 
											ORDER BY o.date DESC')->result();
		
		return $user;
	}
	
	public function getNewOrders()
	{
		return $this->db->query('SELECT o.*, 
										u.name 
									FROM orders o 
									LEFT JOIN users u ON o.id_user = u.id 
								WHERE o.status = 0 
									ORDER BY o.date DESC')->result();
	}
	
	public function getCountNewOrders()
	{
		return $this->db->query('SELECT COUNT(*) AS cnt
									FROM orders 
								WHERE status = 0')->row()->cnt;
	}
	
	public function setStatusOrder()
	{
		$id_order= isset($_POST['id_order']) ? abs((int)$_POST['id_order']) : 0;
		$status = isset($_POST['status']) ? abs((int)$_POST['status']) : 0; 
		
		$this->db->query('UPDATE orders SET status = '.$status.' WHERE id = '.$id_order);
	}
	
	public function delOrder($id = 0)
	{
		$id = abs((int)$id);
		$this->db->query('DELETE FROM orders WHERE id = '.$id);
		
		return;
	}
	
}