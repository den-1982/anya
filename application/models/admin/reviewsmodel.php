<?php
// CLIENT
class reviewsModel extends CI_Model
{
	public $is_rating = array(
			0 => array('grey',''),
			1 => array('red','Очень плохо'),
			2 => array('orange','Плохо'),
			3 => array('yellow','Нормально'),
			4 => array('pale-green','Хорошо'),
			5 => array('green','Отлично')
		);
	public $is_price_correct = array(
			0 => 'Не помню',
			1 => 'Да (хорошо)',
			2 => 'Нет (плохо)'
		);
	public $is_delivery_in_time = array(
			0 => 'Не помню',
			1 => 'Да (хорошо)',
			2 => 'Нет (плохо)'
		);
	
	public function getCountNewReviews()
	{
		return $this->db->query('SELECT COUNT(*) AS cnt FROM reviews WHERE visibility = 0')->row()->cnt;
	}
	
	public function getReviews()
	{
		return $this->db->query('SELECT * FROM reviews ORDER BY `date` DESC')->result();
	}
	
	public function getReview($id = 0)
	{
		$id = abs((int)$id);
		$id = abs((int)$id);
		return $this->db->query('SELECT r.*, 
										(SELECT text FROM reviews_answer WHERE reviews_id = r.id) AS answer_text,
										(SELECT `date` FROM reviews_answer WHERE reviews_id = r.id) AS answer_date
									FROM reviews r
								WHERE id = '.$id)->row();
	}
	
	public function addReview()
	{
		$data['name'] 				= isset($_POST['name'])					? clean( mb_substr($_POST['name'], 0, 100), true, true) : '';
		$data['comment']			= isset($_POST['comment'])				? clean( mb_substr($_POST['comment'], 0, 1000), true, true) : '';
		$data['rating'] 			= isset($_POST['rating'])				? abs((int)$_POST['rating']) : 0;
		$data['is_price_correct'] 	= isset($_POST['is_price_correct'])		? abs((int)$_POST['is_price_correct']) : 0;
		$data['is_delivery_in_time']= isset($_POST['is_delivery_in_time']) 	? abs((int)$_POST['is_delivery_in_time']) : 0;
		$data['date']				= isset($_POST['date']) 				? strtotime($_POST['date']) : time();
		$data['date']				= $data['date'] ? $data['date'] : time();
		
		$this->db->query('INSERT INTO reviews (name, comment, rating, is_price_correct, is_delivery_in_time, date) 
							VALUES(
								"'.$data['name'].'", 
								"'.$data['comment'].'", 
								"'.$data['rating'].'", 
								"'.$data['is_price_correct'].'", 
								"'.$data['is_delivery_in_time'].'", 
								"'.$data['date'].'"
							)');
		
		# ID
		$id = $this->db->query('SELECT MAX(id) AS id FROM reviews')->row()->id;
		if ( ! $id ) return;
		
		# ANSWER добавляем ответ на вопрос
		$data['reviews_id']	 = $id;
		$data['answer_name'] = isset($_POST['answer_name']) ? clean( mb_substr($_POST['answer_name'], 0, 100), true, true) : '';
		$data['answer_text'] = isset($_POST['answer_text']) ? clean( mb_substr($_POST['answer_text'], 0, 1000), false, true) : '';
		$data['answer_date'] = isset($_POST['answer_date']) ? strtotime($_POST['answer_date']) : time();
		$data['answer_date'] = $data['answer_date'] ? $data['answer_date'] : time();
		
		$this->db->query('INSERT INTO reviews_answer(reviews_id, name, text, date) 
							VALUES(
								"'.$data['reviews_id'].'",
								"'.$data['answer_name'].'",
								"'.$data['answer_text'].'",
								"'.$data['answer_date'].'"
							)');
							
		return;
	}
	
	public function updateReview()
	{
		$data['id'] 				= isset($_POST['id']) 					? abs((int)$_POST['id']) : 0;
		$data['name'] 				= isset($_POST['name'])					? clean( mb_substr($_POST['name'], 0, 100), true, true) : '';
		$data['comment']			= isset($_POST['comment'])				? clean( mb_substr($_POST['comment'], 0, 1000), true, true) : '';
		$data['rating'] 			= isset($_POST['rating'])				? abs((int)$_POST['rating']) : 0;
		$data['is_price_correct'] 	= isset($_POST['is_price_correct'])		? abs((int)$_POST['is_price_correct']) : 0;
		$data['is_delivery_in_time']= isset($_POST['is_delivery_in_time']) 	? abs((int)$_POST['is_delivery_in_time']) : 0;
		$data['date']				= isset($_POST['date']) ? strtotime($_POST['date']) : time();
		$data['date']				= $data['date'] ? $data['date'] : time();
		
		$this->db->query('UPDATE reviews SET 
								name = "'.$data['name'].'",
								comment = "'.$data['comment'].'",
								rating = "'.$data['rating'].'",
								is_price_correct = "'.$data['is_price_correct'].'",
								is_delivery_in_time = "'.$data['is_delivery_in_time'].'",
								`date` = "'.$data['date'].'"
							WHERE id = "'.$data['id'].'"');
		
		
		# ANSWER
		$data['answer_name'] = isset($_POST['answer_name']) ? clean( mb_substr($_POST['answer_name'], 0, 100), true, true) : '';
		$data['answer_text'] = isset($_POST['answer_text']) ? clean( mb_substr($_POST['answer_text'], 0, 1000), false, true) : '';
		$data['answer_date'] = isset($_POST['answer_date']) ? strtotime($_POST['answer_date']) : time();
		$data['answer_date'] = $data['answer_date'] ? $data['answer_date'] : time();
		
		$this->db->query('DELETE FROM reviews_answer WHERE reviews_id = "'.$data['id'].'"');
		$this->db->query('INSERT INTO reviews_answer(reviews_id, name, text, date) 
							VALUES(
								"'.$data['id'].'",
								"'.$data['answer_name'].'",
								"'.$data['answer_text'].'",
								"'.$data['answer_date'].'"
							)');
							
		return;
	}
	
	public function setVisibilityReview()
	{
		$id		= isset($_POST['id']) ? abs((int)$_POST['id']): 0;
		$activ	= (int)$_POST['activ'] ? 0 : 1;
		
		$this->db->query('UPDATE reviews SET visibility = "'.$activ.'" WHERE id = "'.$id.'"');
		
		return $activ;
	}
	
	public function deleteReview($id = 0)
	{
		$id = abs((int)$id);

		$this->db->query('DELETE FROM reviews WHERE id = "'.$id.'"');
		return;
	}
}