<?php
// CLIENT
class reviewsModel extends CI_Model
{
	public function getReviews()
	{
		return $this->db->query('SELECT r.*, 
										(SELECT text FROM reviews_answer WHERE reviews_id = r.id) AS answer_text
									FROM reviews r
								WHERE r.visibility = 1 
									ORDER BY r.date DESC')->result();
	}
	
	public function addReview()
	{
		$response = array(
			'error'=>'',
			'text'=>''
		);
		
		$data['name'] 				= isset($_POST['name']) ? $_POST['name'] : '';
		$data['comment']			= isset($_POST['comment']) ? $_POST['comment'] : '';
		$data['rating'] 			= isset($_POST['rating']) ? abs((int)$_POST['rating']) : 0;
		$data['is_price_correct'] 	= isset($_POST['is_price_correct']) ? abs((int)$_POST['is_price_correct']) : 0;
		$data['is_delivery_in_time']= isset($_POST['is_delivery_in_time']) ? abs((int)$_POST['is_delivery_in_time']) : 0;
		$data['date'] 				= time();
		
		$data['name'] = mb_substr(trim(preg_replace('/\s+/', ' ', strip_tags($data['name']))), 0, 100);
		$data['comment'] = mb_substr(trim(preg_replace('/\s+/', ' ', strip_tags($data['comment']))), 0, 500);
		
		$data['name'] = mysql_real_escape_string($data['name']);
		$data['comment'] = mysql_real_escape_string($data['comment']);
		
		if ( ! $data['comment']) $response['error'] = 'Поле Отзыв обязательно для заполнения!';
		
		if ($response['error']) return $response;
		
		$this->db->query('INSERT INTO reviews (name, comment, rating, is_price_correct, is_delivery_in_time, date) 
							VALUES(
								"'.$data['name'].'", 
								"'.$data['comment'].'", 
								'.$data['rating'].', 
								'.$data['is_price_correct'].', 
								'.$data['is_delivery_in_time'].', 
								'.$data['date'].'
							)');
		
		$html = '<div class="row">
					<div class="col-1">
						</div><h2>Спасибо за Ваш отзыв.</h2>
						<p>Отзыв будет размещен после проверки администратором.</p>
						<a href="/reviews/">&larr; назад</a>
					</div>
				</div>';
		
		$response['text'] = $html;
		
		return $response;
	}

}