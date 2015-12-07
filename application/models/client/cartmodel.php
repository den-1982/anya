<?php
class cartModel extends CI_Model
{
//////////////////////////////////////////////////////////// ADD TO CART
	public function addCart()
	{	
		$id 			= isset($_POST['id'])		? abs((int)$_POST['id']) : 0;
		$filter_item_id	= isset($_POST['size'])		? abs((int)$_POST['size']) : 0;
		$type 			= isset($_POST['type'])		? abs((int)$_POST['type']) : 0;
		$quantity 		= isset($_POST['quantity'])	? abs((int)$_POST['quantity']) : 1;
	
		$quantity = $quantity <= 0 ? 1 : $quantity;
		
		# есть ли такой товар
		$product = $this->productModel->getProduct($id);

		if ( ! $product){return 1;}

		# аня попросила внести категорию в заказ
		$product->parent = $this->categoryModel->getCategory($product->parent);
		
		# если есть цена-размер
		if ($product->prices){
			# если неверный размер(кто-то балуется)
			if ( ! isset($product->prices[$filter_item_id])) return;
			
			if ($type == 0){
				$price = $product->prices[$filter_item_id]->roz;
			}elseif($type == 1){
				$price = $product->prices[$filter_item_id]->opt;
			}else{
				return;
			}
		}else{
			$price = $product->price;
		}
		
		# если цена равна нулю - выходим
		if ( ! $price) return;
		
		# опции
		$options = array(
			'type'=>$type,
			'size'=>$filter_item_id 
		);

		$data = array(
			'id'				=> $product->id,
			'qty'				=> $quantity,
			'price'				=> $price,
			'options'			=> $options,
			
			'discount'			=> $product->discount*1 ? $product->discount : (isset($product->prices[$filter_item_id]) ? $product->prices[$filter_item_id]->discount : 0),
			'prices'			=> isset($product->prices[$filter_item_id]) ? (array)$product->prices[$filter_item_id] : '',
			
			'parent'			=> $product->parent,
			'manufacturer'		=> isset($product->manufacturer->name) ? $product->manufacturer->name : '&mdash;',
			'manufacturer_id'	=> $product->manufacturer_id,
			'name'				=> $product->name,
			'image'				=> $product->image,
			'url'				=> $product->url,
			'_url'				=> $product->_url
		);
		
		$this->cart->insert($data);
	}
//////////////////////////////////////////////////////////// EDIT CART
	public function updateCart()
	{
		if (isset($_POST['quantity']) && is_array($_POST['quantity'])){
			foreach ($_POST['quantity'] as $rowid=>$qty){
				$qty = abs((int)$qty);
				$qty = $qty ? $qty : 1;

				# обнавить
				$product = array('rowid'=>$rowid, 'qty'=>$qty);
				$this->cart->update($product);
			}
		}
	}
//////////////////////////////////////////////////////////// DEL CART
	public function delItemCart($rowid)
	{
		$rowid = mysql_real_escape_string($rowid);

		# если 0, то товар удалится
		$product = array('rowid' => $rowid, 'qty' => 0 );
		$this->cart->update($product);
	}
//////////////////////////////////////////////////////////// CHECKOUT
	public function checkout() # оформление заказа
	{
		$cart = $this->cart->contents();

		# если корзина пуста - выход
		if ( ! $cart['total_items']) return;

		# SETTINGS
		$settings = $this->settingsModel->getSettings();
		
		# USER
		$user = $this->userModel->getUser();
		
		## DISCOUNTS
		$_discount['certificate'] = 0;
		$_discount['cart'] = 0;
		$_discount['discount'] = 0;
		
		# если залогинен USER DISCOUNT
		if (isset($user->discount) && ($user->discount * 1)){
			$_discount['discount'] = $user->discount * 1;
		}
		if (isset($user->user_cart_percent) && ($user->user_cart_percent * 1)){
			$_discount['cart'] = $user->user_cart_percent * 1;
		}
		
		# если юзер не зарегался, но есть "user_cart_percent"
		$post['user_cart_discount']	= isset($_POST['user_cart_discount']) ? clean($_POST['user_cart_discount'], true, true) : '';
		if ( !$_discount['cart'] && $post['user_cart_discount']){
			$user = $this->db->query('SELECT * FROM users WHERE user_cart_discount = "'.$post['user_cart_discount'].'"')->row();
			if ( $user ){
				$_discount['cart'] = $user->user_cart_percent * 1;
			}
		}
		
		# СКИДАЧНЫЙ ТАЛОН
		$post['certificate_code']	= isset($_POST['certificate_code']) ? clean($_POST['certificate_code'], true, true) : '';
		if ($post['certificate_code']){
			$res = $this->db->query('SELECT * FROM discount_carts WHERE code = "'.$post['certificate_code'].'"')->row();
			if ($res){
				$_discount['certificate'] = $res->percent * 1;
			}
			# удаляем скидочный талон если его скидка больше остальных
			if ($_discount['certificate'] > max($_discount['cart'], $_discount['discount'])){
				$this->db->query('DELETE FROM discount_carts WHERE code = "'.$post['certificate_code'].'"');
			}
		}
		
		$discount = max(
			$_discount['discount'], 
			$_discount['cart'], 
			$_discount['certificate']
		);
		
		
		///////////////////////////////////////////////////////////////////////////////////
		$error = array();
		$pay = array(0=>'Наличными',1=>'Наложным платежом',2=>'Оплата на карточку Приват Банка');

		$payment	= isset($_POST['payment']) ? (isset($pay[(int)$_POST['payment']]) ? $pay[(int)$_POST['payment']] : 'Наличными') : 'Наличными';	//способ оплаты
		$delivery	= isset($_POST['delivery']) ? ((int)$_POST['delivery'] == 1 ? 'Новая Почта' : 'Самовывоз') : 'Самовывоз';	//способ доставки
		
		$office			= isset($_POST['office']) ? $_POST['office'] : '';	// ID склада Новая Почта
		$NP = $this->novaposhtaModel->getOfficeNovaPoshta($office);
		$cityID			= isset($NP->city_id) ? $NP->city_id : 0;
		$cityName		= isset($NP->cityRu) ? $NP->cityRu : '';
		$officeNumber	= isset($NP->number) ? $NP->number : 0;
		
		$name			= isset($_POST['name']) ? trim(strip_tags($_POST['name'])) : '';
		$mail			= isset($_POST['mail']) ? trim(strip_tags($_POST['mail'])) : '';
		$phone			= isset($_POST['phone']) ? trim(strip_tags($_POST['phone'])) : '';
		$message		= isset($_POST['message']) ? trim(strip_tags($_POST['message'])) : '';

		
		# если USER выбрал регистрацию
		if (isset($_POST['adduser']) && !$user){
			$post['name'] = $name;
			$post['name'] = preg_replace('/[^a-zа-я\s]/ui', '', $post['name']);
			$post['name'] = preg_replace("/\s+/", ' ', $post['name']);
			$post['name'] = trim($post['name']);
			$post['name'] = mb_convert_case($post['name'], MB_CASE_TITLE, "UTF-8");
			if ( ! $post['name']) $error['name'] = 'Поле Ваше ФИО обязательно для заполнения!';
			
			
			$post['city'] = $cityID;
			
			# проверка EMAIL
			$post['email'] = $mail;
			if ( ! filter_var($post['email'], FILTER_VALIDATE_EMAIL)){
				$error['email'] = 'Некорректный e-mail!';
			}else{
				# есть ли такой EMAIL в бвзе
				$res = $this->db->query('SELECT * FROM users WHERE email = "'.$post['email'].'"')->row();
				if ( $res ) $error['email'] = 'Пользователь с такой почтой уже зарегистрирован.';
			}

			# проверка PHONE
			$post['phone'] = $phone;
			$post['number'] = preg_replace("/[^0-9]/u", '', $post['phone']);
			if ( ! $post['number']) $error['phone'] = 'Поле Мобильный телефон обязательно для заполнения!';
			# есть ли такой PHONE в бвзе
			$res = $this->db->query('SELECT * FROM users WHERE number = "'.$post['number'].'"')->row();
			if ( $res ) $error['phone'] = 'Пользователь с таким номером мобильного телефона уже зарегистрирован.';
			
			# сгенерировать новый пароль
			$post['pass'] = '';
			// for($i=0; $i < 4; $i++)	$post['pass'] .= rand(0,9);
			
			# ДОБАВИТЬ В БАЗУ
			if ( ! $error){
				$token = '';
			
				$this->db->query('INSERT INTO users (name, email, phone, number, city, pass, token) 
					VALUES(
						"'.$post['name'].'", 
						"'.$post['email'].'", 
						"'.$post['phone'].'", 
						"'.$post['number'].'", 
						"'.$post['city'].'", 
						"'.$post['pass'].'", 
						"'.$token.'"
					)');
				
				# если все ок - аутификация
				$this->userModel->authUser($post['number'], $post['pass']);
				$user = $this->userModel->getUser();
			}
		}
		#################################################################################

		$info = '<html>
					<head>
					  <title>Заказ товаров '. strtoupper($_SERVER['SERVER_NAME']) .'</title>
					</head>
					<body>
						<h2 style="text-align:center; font-weight:normal;color:#fff;background:#53afea;margin:0;">Заказ товаров '.strtoupper($_SERVER['SERVER_NAME']).'</h2>
						<table width="100%" cellpadding="4" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
							<tr>
								<td width="150px" align="right" style="border:1px solid #ccc;background:#53afea;color:#fff;">ФИО</td>
								<td align="left" style="border:1px solid #ccc;">'.$name.'</td>
							</tr>
							<tr>
								<td width="150px" align="right" style="border:1px solid #ccc;background:#53afea;color:#fff;">телефон</td>
								<td align="left" style="border:1px solid #ccc;">'.$phone.'</td>
							</tr>
							<tr>
								<td width="150px" align="right" style="border:1px solid #ccc;background:#53afea;color:#fff;">email</td>
								<td align="left" style="border:1px solid #ccc;">'.$mail.'</td>
							</tr>
							<tr>
								<td width="150px" align="right" style="border:1px solid #ccc;background:#53afea;color:#fff;">Способ оплаты</td>
								<td align="left" style="border:1px solid #ccc;">'.$payment.'</td>
							</tr>
							<tr>
								<td width="150px" align="right" style="border:1px solid #ccc;background:#53afea;color:#fff;">Способ доставки</td>
								<td align="left" style="border:1px solid #ccc;">'.$delivery.'</td>
							</tr>
							<tr>
								<td width="150px" align="right" style="border:1px solid #ccc;background:#53afea;color:#fff;">Город</td>
								<td align="left" style="border:1px solid #ccc;">'.$cityName.'</td>
							</tr>
							<tr>
								<td width="150px" align="right" style="border:1px solid #ccc;background:#53afea;color:#fff;">Склада "Новая Почта"</td>
								<td align="left" style="border:1px solid #ccc;">'.$officeNumber.'</td>
							</tr>
							<tr>
								<td width="150px" align="right" style="border:1px solid #ccc;background:#53afea;color:#fff;">комментарий:</td>
								<td align="left" style="border:1px solid #ccc;">'.$message.'</td>
							</tr>
						</table>';	
		
		
		# товары
				$msg = '<h2 style="text-align:center; font-weight:normal;color:#fff;background:#53afea;margin:20px 0 0 0;">ТОВАРЫ</h2>
						<table width="100%" cellpadding="4" cellspacing="0" style="border-collapse:collapse;font-size:14px;text-align:center;">
							<tr style="background:#eee;">
								<td width="1x" style="border:1px solid #ccc"></td>
								<td width="1" style="border:1px solid #ccc;"><small>категория</small></td>
								<td style="border:1px solid #ccc;">Наименование</small></td>
								<td width="1" style="border:1px solid #ccc;"><small>Размер мм.</small></td>
								<td width="1" style="border:1px solid #ccc;"><small>Опт / розница</small></td>
								<td width="1" style="border:1px solid #ccc;"><small>Кол-во ед.</small></td>
								<td width="1" style="border:1px solid #ccc;"><small>цена за ед.</small></td>
								<td width="1" style="border:1px solid #ccc;"><small>сумма</small></td>
							</tr>';

							$sum = 0;
							foreach ($cart['products'] as $k){
								# определяем цену - если есть размер, значит есть цена(опт\роз\%)
								if ( ! $k['prices']){
									if ($discount > $k['discount']*1){
										$price = $k['price'];
									}else{
										$price = $k['price'] - round($k['price'] * $k['discount']/ 100, 2);
									}
								}else{
									# если общая скидка больше чем скидка на товар
									if ($discount > $k['discount']*1){
										$price = $k['options']['type'] == 0 ? $k['prices']['roz'] : $k['prices']['opt'];
									}else{
										if ($k['options']['type'] == 0){
											$price = $k['prices']['roz'] - round($k['prices']['roz'] * $k['discount']/ 100, 2);
										}else{
											$price = $k['prices']['opt'] - round($k['prices']['opt'] * $k['discount']/ 100, 2);
										}
									}
								}
								
								# сумма за товар
								$subtotal = $k['qty'] * $price;
								
								# cумма ИТОГО
								$sum += $subtotal;

								$msg .= '<tr>
									<td style="border:1px solid #ccc;">
										<a href="'. SERVER . $k['_url'] .'">
											<img width="70" src="'. SERVER . $k['image'] .'">
										</a>
									</td>
									<td style="border:1px solid #ccc;text-align:left;">
										<a href="'. SERVER . $k['parent']->_url.'">'. $k['parent']->h1 .'</a>
									</td>
									<td style="border:1px solid #ccc;text-align:left;">
										<a href="'. SERVER . $k['_url'] .'">'. $k['name'] .'</a>
									</td>
									<td style="border:1px solid #ccc;">'. ($k['prices'] ? $k['prices']['name'].' '.$k['prices']['prefix'] : '') .'</td>
									<td style="border:1px solid #ccc;">'. ($k['prices'] ? ($k['options']['type'] == 0 ? 'розница' : 'опт') : '') .'</td>
									<td style="border:1px solid #ccc;">'. $k['qty'] .'</td>
									<td style="border:1px solid #ccc;">'. number_format($price, 2, ',', "'") .' грн.</td>
									<td style="border:1px solid #ccc;">'. number_format($subtotal, 2, ',', "'") .' грн.</td>
								</tr>';
							}
					$msg .= '<tr>
								<td></td><td></td><td></td><td></td><td></td><td></td>
								<td style="border:1px solid #ccc;"><b>ВСЕГО: </b></td>
								<td style="border:1px solid #ccc;white-space:nowrap;">'. number_format($sum, 2, ',', "'") .' грн.</td>
							</tr>
							<tr>
								<td></td><td></td><td></td><td></td><td></td><td></td>
								<td style="border:1px solid #ccc;"><b>СКИДКА: </b></td>
								<td style="border:1px solid #ccc;">'. $discount. ' %</td>
							</tr>
							<tr>
								<td></td><td></td><td></td><td></td><td></td><td></td>
								<td style="border:1px solid #ccc;white-space:nowrap;"><b>ИТОГО: </b></td>';
								
								# скидки для не акционных товаров
								if ($discount){
									$_all = 0;
									foreach ($cart['products'] as $k){
										# какая скидка больше
										$_discount = $discount > $k['discount']*1 ? $discount : $k['discount']*1;
										
										if ( ! $k['prices']){
											$_all += $k['qty'] * ($k['price'] - round($k['price'] * $_discount/ 100, 2));
										}else{
											if ($k['options']['type'] == 1){
												$_all += round($k['qty'] * ($k['prices']['opt'] - $k['prices']['opt'] * $_discount / 100), 2);
											}else{
												$_all += round($k['qty'] * ($k['prices']['roz'] - $k['prices']['roz'] * $_discount / 100), 2);
											}
										}
									}
									
									$end = number_format($_all, 2, ',', "'") .' грн.';
								}else{
									$end = number_format($sum, 2, ',', "'") .' грн.';
								}
								
						$msg .= '<td style="border:1px solid #ccc;white-space:nowrap;">'. $end .'</td>
							</tr>
						</table>';
		
		# отправить письмо админу
		// crystalline.in.ua@yandex.ru
		// $this->email->clear();
		// $this->email->from('admin@crystalline.in.ua', strtoupper($_SERVER['SERVER_NAME']));
		// $this->email->to($settings->email);
		// $this->email->subject('ЗАКАЗ');
		// $this->email->message($info.$msg);
		// $this->email->send();
		
		$to			= $settings->email;
		$tema		= 'ЗАКАЗ';	
		$headers	= "From: ".strtoupper($_SERVER['SERVER_NAME'])." <webmaster@".strtoupper($_SERVER['SERVER_NAME']).">\r\n";
		$headers	.= "Content-type: text/html; charset=\"utf-8\"";
		
		mail($to, $tema, $info.$msg, $headers);
		//file_put_contents(ROOT.'/letters/'.time().'.html', $info.$msg);
		
		###########################################################################################
		# отправить письмо клиенту (его заказ)
		// $this->email->clear();
		// $this->email->from('admin@crystalline.in.ua', strtoupper($_SERVER['SERVER_NAME']));
		// $this->email->to($mail);
		// $this->email->subject("Ваш заказ ". strtoupper($_SERVER['SERVER_NAME']));
		// $this->email->message($msg);
		// $this->email->send();
		
		$to			= $mail;
		$tema		= 'Ваш заказ';	
		$headers	= "From: ".strtoupper($_SERVER['SERVER_NAME'])." <webmaster@".strtoupper($_SERVER['SERVER_NAME']).">\r\n";
		$headers	.= "Content-type: text/html; charset=\"utf-8\"";
		
		mail($to, $tema, $msg, $headers);
		###########################################################################################
		
		# INSERT записать в базу данных если USER зарегистрирован
		if ($user){
			# делаем запись в ORDERS
			$this->db->query('INSERT INTO orders (id_user, discount, `date`) 
									VALUES(
										"'.$user->id.'", 
										"'.$discount.'", 
										"'.time().'"
									)');

			# делаем записи товаров
			$id_order = $this->db->query('SELECT MAX(id) AS id FROM orders')->row()->id;
			
			$sum = 0;
			foreach ($cart['products'] as $k)
			{
				# определяем цену - если есть размер, значит есть цена(опт\роз\%)
				if ($k['prices']){
					if ($k['options']['type'] == 1){
						$price = round($k['prices']['opt'] - ($k['prices']['opt'] * $k['discount'] / 100), 2);
					}else{
						$price = round($k['prices']['roz']  - ($k['prices']['roz'] * $k['discount'] / 100), 2);
					}
				}else{
					$price = $k['price'];
				}
				
				# сумма за товар
				$subtotal = $k['qty'] * $price;
				
				# cумма ИТОГО
				$sum += $subtotal;
				
				$this->db->query('INSERT INTO order_items(id_order, id_product, size, price, quantity, type, subtotal) 
									VALUES (
										"'.$id_order.'", 
										"'.$k['id'].'", 
										"'.$k['options']['size'].'", 
										"'.$price.'", 
										"'.$k['qty'].'", 
										"'.$k['options']['type'].'",
										"'.$subtotal.'"
									)');
			}
			
			# общая ссума заказа
			$this->db->query('UPDATE orders SET sum = "'.$sum.'" WHERE id = '.$id_order);
		}
		
		# CLEAR очищяем корзину
		$this->cart->destroy();
		
		# INFO инфо об успешном заказе
		$this->session->set_userdata('check', 'Ваш заказ принят.<br> В ближайшее время вам перезвонит наш менеджер.');
	}
//////////////////////////////////////////////////////////// GET CART HTML	
	public function getCartHtml()
	{
		$cart = $this->cart->contents();
		$cart['cnt_items'] = count($cart['products']);
		
		$user = $this->data['user'];
		
		$total = 0;
		
		$html = '';
		if ( ! count($cart['products'])){
			$html .= '
			<div class="row">
				<div class="col-3"></div>
				<div class="col-3 center">
					<h2 class="center">Ваша корзина пуста.</h2>
					<a class="btn btn-white" href="/">&larr; продолжить покупки</a>
				</div>
				<div class="col-3"></div>
			</div>';
			
		}else{
			
			$html .= '
			<ul class="cart-list" data-cart="items">';
			foreach (array_reverse($cart['products']) as $k=>$i){
				$html .= '
				<li data-cart-item="">
					<div class="ci-box">
						<div class="pp-block-image">
							<a href="'. htmlspecialchars($i['_url']) .'">
								<img src="'.htmlspecialchars($i['image']) .'" alt="'.htmlspecialchars($i['name']).'">
							</a>
						</div>
						<div class="pp-block-price">
							<div class="gd gd-name"><a class="c-pink" href="'. htmlspecialchars($i['_url']) .'">'. $i['name'] .'</a></div>
							
							<div class="gd gd-manufacturer">Производитель: '. (isset($i['manufacturer']) ? $i['manufacturer'] : '&mdash;') .'</div>';
							
							if ($i['prices']){
							$cnt_opt = isset($i['prices']['cnt_opt']) ? $i['prices']['cnt_opt'] : '';
							$cnt_roz = isset($i['prices']['cnt_roz']) ? $i['prices']['cnt_roz'] : '';
							$html .= '
							<div class="gd gd-size">Размер: '. $i['prices']['name'] .' ('. $i['prices']['prefix'] .')</div>
							<div class="gd gd-packing">Упаковка: '. ($i['options']['type'] ? 'опт - '.($cnt_opt) : 'розница - '.($cnt_roz)) .' шт.</div>';		
							unset($cnt_opt);
							unset($cnt_roz);
							}
							
							$html .= '
							<div class="gd gd-price">
								Цена:';
								if ($i['discount']*1){
								$html .= '
								<span class="gd-new-price">'. number_format(($i['price']-($i['price']*$i['discount']/100)), 2, ',', "'") .' грн.</span>';
								}else{
								$html .= '
								<span class="gd-new-price">'. number_format($i['price'], 2, ',', "'") .' грн.</span>';
								}
							$html .= '
							</div>
							
							<div class="gd gd-qty">Количество: <b>&times;</b> '. $i['qty'] .'</div>

							<div class="gd gd-subtotal">
								Сумма: ';
							
								# скидка User
								$discount = isset($user->discount) ? $user->discount*1 : 0;
								# если нет цена + размер
								if ( ! $i['prices']){
									# если USER скидка больше чем скидка на товар
									if ($discount > $i['discount']*1){
										$summa = $i['qty'] * $i['price'];
										$html .= number_format($summa, 2, ',', "'").' грн.';
									}else{
										$summa = $i['qty'] * ($i['price'] - round($i['price'] * $i['discount']/ 100, 2));
										$html .= number_format($summa, 2, ',', "'").' грн.';
									}
								}else{
									# если USER скидка больше чем скидка на товар
									if ($discount > $i['discount']){
										if ($i['options']['type'] == 0){
											$roz = isset($i['prices']['roz']) ? $i['prices']['roz'] : 0;
											$summa = round($i['qty'] * $roz, 2);
											$html .= number_format($summa, 2, ',', "'").' грн.';
											unset($roz);
										}else{
											$opt = isset($i['prices']['opt']) ? $i['prices']['opt'] : 0;
											$summa = round($i['qty'] * $opt, 2);
											$html .= number_format($summa, 2, ',', "'").' грн.';
											unset($opt);
										}
									}else{
										if ($i['options']['type'] == 0){
											$roz = isset($i['prices']['roz']) ? $i['prices']['roz'] : 0;
											$summa = $i['qty'] * ($roz - round($roz * $i['discount']/ 100, 2));
											$html .=  number_format($summa, 2, ',', "'").' грн.';
											unset($roz);
										}else{
											$opt = isset($i['prices']['opt']) ? $i['prices']['opt'] : 0;
											$summa = $i['qty'] * ($opt - round($opt * $i['discount']/ 100, 2));
											$html .= number_format($summa, 2, ',', "'").' грн.';
											unset($opt);
										}
									}
								}
							$html .= '
							</div>

							<div class="gd-counted">
								<div class="quant">
									<button data-quantity-button="minus" class="quant-button minus"><span>−</span></button>
									<input class="quant-box" type="text" name="quantity['. $k .']" value="'. $i['qty'] .'" data-quantity="box" autocomplete="off">
									<button data-quantity-button="plus" class="quant-button plus"><span>+</span></button>
								</div>
							</div>
							
							<div class="gd-counted" data-btn="update" style="display:none;">
								<button class="btn btn-pink" type="submit">Пересчитать</button>
							</div>
							
							<br>
							<a class="gd gd-delete uppercase" data-cart-delete="'. $k .'" href="/cart?del='. $k .'">Удалить</a>
						</div>
					</div>
				</li>';
				
				$total += $summa; 
			}
				$html .= '
				<li>
					<div class="gd-total">
						<div class="gd-label">
							<div class="gd-label-name">Всего:</div>
							<div class="gd-label-box" data-cart="subtotal">'. number_format($total, 2, ',', "'") .' грн.</div>
						</div>
						<div class="gd-label">
							<div class="gd-label-name">Ваша скидка:</div>
							<div class="gd-label-box" data-cart="discount">'. $discount .' %</div>
						</div>
						<div class="gd-label">
							<div class="gd-label-name">Итого:</div>
							<div class="gd-label-box" data-cart="total">';
							if ($discount){
								$total = 0;
								foreach ($cart['products'] as $k){
									# какая скидка больше
									$_discount = $discount > $k['discount']*1 ? $discount : $k['discount']*1;
									
									# если нет цена + размер
									if ( ! $k['prices']){ 
										$total += $k['qty'] * ($k['price'] - round($k['price'] * $_discount/ 100, 2));
									}else{
										if ($k['options']['type'] == 0){
											$roz = isset($k['prices']['roz']) ? $k['prices']['roz'] : 0;
											$total += round($k['qty'] * ($roz - $roz * $_discount / 100), 2);
											unset($roz);
										}else{
											$opt = isset($k['prices']['opt']) ? $k['prices']['opt'] : 0;
											$total += round($k['qty'] * ($opt - $opt * $_discount / 100), 2);
											unset($opt);
										}
									}
								}

								$html .= number_format($total, 2, ',', "'").' грн.';
							}else{
								$html .= number_format($total, 2, ',', "'").' грн.';
							}
							$html .= '
							</div>
						</div>
					</div>
				</li>
			</ul>';	
		}
		
		# убираем пробелы (сжимаем)
		$cart['html'] = preg_replace('/\s+/', ' ', $html);
		
		$cart['cart_total'] = number_format($total, 2, ',', "'");
		
		# Для корзины в подвале
		$bott = '';
		if ( ! count($cart['products'])){
			$bott .= '
			<div class="cbt-content">
				<div class="cbt-empty">Ваша корзина пуста</div>
			</div>';
		}else{
			$bott .= '
			<div class="cbt-content">
				<div class="cbt-content-left">
					<div class="owl-carousel" data-cart-bt="owlCarusel">';
					foreach (array_reverse($cart['products']) as $k=>$v){
						$bott .= '<div class="item">
							<div class="cbt-item">
								<div class="cbt-item-right">
									<div class="gd gd-name">
										<a href="'.htmlspecialchars($v['_url']).'">'.$v['name'].'</a>
									</div>
									<div class="gd gd-manufacturer">Производитель: '.(isset($v['manufacturer']) ? $v['manufacturer'] : '&mdash;').'</div>';
									if ($v['prices']){
									$cnt_roz = isset($v['prices']['cnt_roz']) ? $v['prices']['cnt_roz'] : '';
									$cnt_opt = isset($v['prices']['cnt_opt']) ? $v['prices']['cnt_opt'] : '';
									
									$bott .= '
									<div class="gd gd-size">Размер: '.$v['prices']['name'].' ('.$v['prices']['prefix'].')</div>
									<div class="gd gd-packing">Упаковка: '. ($v['options']['type'] ? 'опт - '.($cnt_opt) : 'розница - '.($cnt_roz)) .' шт.</div>';
									unset($cnt_roz);
									unset($cnt_opt);
									}
									
									$bott .= '
									<div class="gd gd-price">Цена:';
									if ($v['discount']*1){
										$bott .= '
										<span class="gd-old-price">'.number_format($v['price'], 2, ',', "'").' грн.</span>
										<span class="gd-new-price">'.number_format(($v['price']-($v['price']*$v['discount']/100)), 2, ',', "'").' грн.</span>';
									}else{
										$bott .= '
										<span class="gd-new-price">'.number_format($v['price'], 2, ',', "'").' грн.</span>';
									}
									$bott .= '
									</div>
									<div class="gd gd-qty">Количество: <b>&times;</b> '.$v['qty'].'</div>
									<div class="gd gd-subtotal">Сумма: '.number_format($v['subtotal'], 2, ',', "'").' грн.</div>

									<a class="gd gd-edit" href="'. htmlspecialchars($v['_url']) . ($v['prices'] ? ('#size='.$v['options']['size'].';packing='.$v['options']['type']) : '').'">Редактировать</a>
									<br>
									<a class="gd gd-delete" href="/cart?del='.$k.'>" data-cart-delete="'.$k.'">Удалить</a>
								</div>
								<div class="cbt-item-left">
									<img src="'.htmlspecialchars($v['image']).'" alt="'.htmlspecialchars($v['name']).'">
								</div>
							</div>
						</div>';
					}
			$bott .= '
					</div>
				</div>
				<div class="cbt-content-right">
					<a class="btn btn-pink" href="/cart">Оформить</a>
				</div>
			</div>';
		}
		# убираем пробелы (сжимаем)
		$cart['html_bottom'] = preg_replace('/\s+/', ' ', $bott);
		
		
		unset($cart['products']);
		
		return $cart;
	}
	
}