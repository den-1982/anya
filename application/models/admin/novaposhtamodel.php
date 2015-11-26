<?php
class novaposhtaModel extends CI_Model
{
	private $api_key = '0166186b16d0e49771a0aebf25ac4770';
	
	private function sendRequest($xml) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://orders.novaposhta.ua/xml.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
	
	private function ApiNovaPoshta_getWarenhouse() 
	{
		$xml = "
		<?xml version=\"1.0\" encoding=\"utf-8\"?>
		<file>
			<auth>{$this->data['settings']->api_key_novaposhta}</auth>
			<warenhouse/>
		</file>";
		
        $response = $this->sendRequest($xml);
		if ( ! $response){
			log_message('error', 'Ошибка "curl_exec" Новая-Почта');
		}
		
		return $response;
	}
	
	public function refreshNovaPoshta() 
	{
		$response = $this->ApiNovaPoshta_getWarenhouse();
		$response = simplexml_load_string($response);
		
		if ( !$response || $response->responseCode != '200'){
			log_message('error', 'Ошибка загрузки БД Новая-Почта');
			return 'Ошибка загрузки БД Новая-Почта';
		}

		$cols	= array();
		$insert = array();
		$wrap	= function($data = ''){
			return $data ? '"'.mysql_real_escape_string($data).'"' : '""';
		};
		
		# название колонок
		foreach ($response->result->whs->warenhouse[0] as $k=>$v){
			$cols[] = $k . ' TEXT ';
		}
		# Создание таблицы
		$this->db->query('CREATE TABLE IF NOT EXISTS novaposhta ('.implode(',', $cols).')');

		# добавляем значения
		foreach ($response->result->whs->warenhouse as $item){
			$insert[] = '('. implode(', ', array_map($wrap, array_values((array)$item))) .')';
		}
		# очищаем таблицу и добавл. нов. значения
		$this->db->query('DELETE FROM novaposhta');
		$this->db->query('INSERT novaposhta VALUES '.implode(',', $insert).' ');
		
		return;
    }
	
	
	
	public function getInvoiceNovaPoshta($cargo_number = '')
	{
		//$cargo_number = '59000105822136';
		$cargo_number = preg_replace('/[^0-9]/', '', $cargo_number);
		
		$getElementsByClassName = function (DOMDocument $dom, $className) {
			$elements = $dom->getElementsByTagName('*');
			$matches = array();
			foreach($elements as $element) {
				if ( ! $element->hasAttribute('class')) continue;

				$classes = preg_split('/\s+/', $element->getAttribute('class'));
				
				if ( ! in_array($className, $classes)) continue;

				$matches[] = $element;
			}
			return $matches;
		};
		
		
		$opts = array(
			'http'=>array(
				'method'=>"GET",
				'header'=>"Accept-language: ru\r\n" .
				"Cookie: language=ru\r\n"
			)
		);
		$context = stream_context_create($opts);
		$file = file_get_contents('http://novaposhta.ua/tracking/?cargo_number='.$cargo_number, false, $context);
		
		$dom = new DOMDocument();
		@$dom->loadHTML($file);
		$result = $getElementsByClassName($dom, 'response');
		
		return isset($result[0]) ? $dom->saveHTML($result[0]) : '<p style="color:red;">Номер не найден. Пожалуйста, проверьте правильнность указаного номера.<p>';
	}
	
	public function getAllWarenListNovaPoshta()
	{
		// $res = array();
		// $res['response'] = $this->db->query('SELECT * FROM novaposhta')->result();
		// return $res;
		
		$res['response'] = array();
		$r = $this->db->query('SELECT * FROM novaposhta')->result();
		foreach ($r as $item){
			$res['response'][$item->city_id][] = $item;
		}
		
		return $res;
	}
	
	public function getCitiesNovaPoshta()
	{
		$res = array();
		$res['response'] = $this->db->query('SELECT city, cityRu, city_id 
									FROM novaposhta 
										GROUP BY city_id 
										ORDER BY cityRu ASC')->result();
		return $res;
	}
	
	public function getCityNovaPoshta($city_id = 0)
	{
		$city_id = abs((int)$city_id);
				
		return $this->db->query('SELECT cityRu 
									FROM novaposhta
								WHERE city_id LIKE "'.$city_id.'" LIMIT 1')->row();
	}
	
	public function getOfficeNovaPoshta($wareId = 0)
	{
		$wareId = abs((int)$wareId);
				
		return $this->db->query('SELECT * 
									FROM novaposhta
								WHERE wareId = "'.$wareId.'" ')->row();
	}
	
	public function getWarenListNovaPoshta($city_id = 0)
	{
		$city_id = abs((int)$city_id);
		
		$res = array();
		$res['response'] = $this->db->query('SELECT wareId, addressRu 
									FROM novaposhta
								WHERE city_id LIKE "'.$city_id.'"')->result();
								
		return $res;
	}
	
}

/*
(
	[city_ref] => 000655ca-4079-11de-b509-001d92f78698
	[city_id] => 462
	[city] => Пустомити
	[cityRu] => Пустомыты
	[ref] => e0c8be6e-c489-11e2-874c-d4ae527baec3
	[address] => Відділення №1: вул. Грушевського, 11а
	[addressRu] => Отделение №1: ул. Грушевского, 11а
	[number] => 1
	[wareId] => 10430
	[phone] => (032) 290-19-11
	[weekday_work_hours] => 09:00-18:00
	[weekday_reseiving_hours] => 09:00-09:30
	[weekday_delivery_hours] => 13:30-18:00
	[saturday_work_hours] => 09:00-15:00
	[saturday_reseiving_hours] => 0
	[saturday_delivery_hours] => 0
	[working_monday] => 09:00-18:00
	[working_tuesday] => 09:00-18:00
	[working_wednesday] => 09:00-18:00
	[working_thursday] => 09:00-18:00
	[working_friday] => 09:00-18:00
	[working_saturday] => 09:00-15:00
	[working_sunday] => -
	[departure_monday] => 13:30
	[departure_tuesday] => 13:30
	[departure_wednesday] => 13:30
	[departure_thursday] => 13:30
	[departure_friday] => 13:30
	[departure_saturday] => -
	[departure_sunday] => -
	[receipt_monday] => 09:30
	[receipt_tuesday] => 09:30
	[receipt_wednesday] => 09:30
	[receipt_thursday] => 09:30
	[receipt_friday] => 09:30
	[receipt_saturday] => -
	[receipt_sunday] => -
	[max_weight_allowed] => 0
	[y] => 49.716464000000000
	[x] => 23.904652000000000
)


	[ref] => 01ae2630-e1c2-11e3-8c4a-0050568002cf
	[number] => 1
	[warehouseType] => cargo
	[warehouseTypeDescription] => Вантажне відділення
	[phone] => (032) 290-19-11
	[city] => Пустомити
	[cityRu] => Пустомыты
	[city_ref] => 000655ca-4079-11de-b509-001d92f78698
	[address] => Відділення №1: вул. Грушевського, 11а
	[addressRu] => Отделение №1: ул. Грушевского, 11а
	[y] => 49.716464000000000
	[x] => 23.904652000000000
	[h24] => 
	[max_weight_allowed] => 0
	[working_monday] => 09:00-18:00
	[working_tuesday] => 09:00-18:00
	[working_wednesday] => 09:00-18:00
	[working_thursday] => 09:00-18:00
	[working_friday] => 09:00-18:00
	[working_saturday] => 09:00-15:00
	[working_sunday] => -
	[departure_monday] => 13:30
	[departure_tuesday] => 13:30
	[departure_wednesday] => 13:30
	[departure_thursday] => 13:30
	[departure_friday] => 13:30
	[departure_saturday] => -
	[departure_sunday] => -
	[receipt_monday] => 09:30
	[receipt_tuesday] => 09:30
	[receipt_wednesday] => 09:30
	[receipt_thursday] => 09:30
	[receipt_friday] => 09:30
	[receipt_saturday] => -
	[receipt_sunday] => -
	)
*/

# отделения НОВАЯ ПОЧТА (без ключа)
//$file = file_get_contents('http://novaposhta.ua/shop/office/getjsonwarehouselist');
// $data = json_decode($file);


# CREATE TABLE Создание таблицы с соответсвующими полями
// $arr = array();
// foreach ($data->response[0] as $k=>$v){
	// $arr[] = $k . ' TEXT ';
// }
// $this->db->query('CREATE TABLE novaposhta ('.implode(',', $arr).')');
// exit;

# INSERT
// $arr = array();
// $wrap = function($data = ''){
	// return $data ? '"'.mysql_real_escape_string($data).'"' : '""';
// };
// foreach ($data->response as $k){
	// $arr[] = '('.implode(', ', array_map($wrap, array_values((array)$k))).')';
// }
// $this->db->query('INSERT novaposhta VALUES '.implode(',', $arr).' ');
// exit;





