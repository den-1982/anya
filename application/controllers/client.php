<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Client extends CI_Controller 
{
	public $data = array(
		'h1'=>'',
		'name'=>'',
		'title'=>'',
		'metadesc'=>'',
		'metakey'=>'',
		'text'=>'',
		'spam'=>'',

		'crumbs'=>array(),
		'products_discount'=>array()
	);
						 
	public function __construct()
	{
		parent::__construct();
		
		$this->load->helpers('functions');
		
		$this->load->model(
			array(
				'client/settingsModel',
				
				'client/clientModel',
				'client/userModel',
				
				'client/pageModel',
				'client/categoryModel',
				'client/productModel',
				'client/filterModel',
				'client/cartModel',
				'client/partnerModel',
				'client/reviewsModel',
				'client/novaposhtaModel'
			)
		);

		
		# Получить настройки
		$this->data['settings'] = $this->settingsModel->getSettings();	
		
		# USER - данные о клиенте (если он авторизовался)
		$this->data['user'] = $this->userModel->getUser();

		# CART - корзина покупок
		$this->data['cart'] = $this->cartModel->getCartHtml();
	}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// __VIEW
	private function _view($type = 'index', $data = array())
	{
		$this->load->view('client/parts/header.php', $data);
		$this->load->view('client/'.$type.'.php', $data);
		$this->load->view('client/parts/footer.php');
	}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// __INFO
	private function _info($obj = null, $default = '')
	{
		if ( ! $obj) $obj = new stdClass();

		$this->data['h1']		= isset($obj->h1)		? $obj->h1 : $default;
		$this->data['name']		= isset($obj->name)		? $obj->name : $default;
		$this->data['title']	= isset($obj->title)	? $obj->title : $default;
		$this->data['metadesc']	= isset($obj->metadesc) ? $obj->metadesc : $default;
		$this->data['metakey']	= isset($obj->metakey)	? $obj->metakey : $default;
		$this->data['text']		= isset($obj->text)		? $obj->text : '';
		$this->data['spam']		= isset($obj->spam)		? $obj->spam : '';
	}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// __CRUMBS
	private function _crumbs($data = array(), $parent = 0)
	{
		static $j = 0;
		
		$crumbs = array();
		do{
			$terac = false;
			foreach ($data as  $item){
				foreach ($item as $i){
					if ($i->id == $parent){
						$crumbs[] = array('id'=>$i->id, 'name'=>$i->name, '_url'=>$i->_url);
						$parent = $i->parent;
						$terac = true;
						break;
					}
				}	
			}
			
			# предохранитель (избегаем зацыкливания)
			$j++;
			if ($j > 100000)return;
			# =====================================
			
		}while ($terac);

		return array_reverse($crumbs);
	}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// INDEX	
	public function Index()
	{
		$data = &$this->data;
		
		$data['home'] = $this->pageModel->getSystemPage('home');
		$this->_info($data['home']);
		
		# products discount (карусель)
		$data['products_discount'] = $this->productModel->getProductsDiscount();
		
		$data['categories'] = $this->categoryModel->sortCategories($this->categoryModel->getCategories());
		$data['pages'] 		= $this->pageModel->sortPages($this->pageModel->getPages());
		$data['partners'] 	= $this->partnerModel->getPartners();
		
		$this->_view('home', $data);
	}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// REVIEWS
	public function Reviews($action = '')
	{
		$data = &$this->data;
		
		$data['action'] = $action;

		if (isset($_POST['add-review'])){
			$res = $this->reviewsModel->addReview();
			echo json_encode($res);
			exit;
		}
		
		if ($data['action'] == 'add-review'){
		
			$this->_info(null, 'Добавить отзыв'); 
			
			$data['crumbs'] = array(
				array(
					'id'=>0, 
					'_url'=>'/reviews/',
					'name'=>'Отзывы'
				),
				array(
					'id'=>0, 
					'name'=>'Добавить отзыв'
				)
			);
		
		}else{
			$this->_info(null, 'Отзывы'); 
			$data['crumbs'] = array(array('id'=>0, 'name'=>'Отзывы'));
			$data['reviews'] = $this->reviewsModel->getReviews();
		}
		
		$data['categories'] 	= $this->categoryModel->sortCategories($this->categoryModel->getCategories());
		$data['pages'] 			= $this->pageModel->sortPages($this->pageModel->getPages());
		$data['partners'] 		= $this->partnerModel->getPartners();
		
		$this->_view('reviews', $data);
	}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// CATEGORY
	public function Category($url = '', $id = 0, $filter = '')
	{
		$data = &$this->data;
		
		$data['category'] = $this->categoryModel->getCategory($id);
		if ( ! $data['category']) {$this->_404();return;}
		if ($data['category']->url != $url){ redirect($data['category']->_url, 'location', 301);return;}
		
		
		# если нет такого
		if ( ! $data['category']){$this->_404();return;}
		
		# filter
		$data['filter']['size'] = isset($_GET['size']) ? abs((int)$_GET['size']) : 0;
		
		# мета тэги
		$this->_info($data['category']);

		# продукты
		$data['products'] = $this->productModel->getProducts($data['category']->id, $data['filter']);	
		
		# фильтр размеры (select)
		//$data['filter_items_size'] = $this->filterModel->getFilterItemSize($data['category']->id);
		
		# products discount (карусель)
		$data['products_discount'] = $this->productModel->getProductsDiscount();
		
		
		$data['categories'] 	= $this->categoryModel->sortCategories($this->categoryModel->getCategories());
		$data['pages'] 			= $this->pageModel->sortPages($this->pageModel->getPages());
		$data['partners'] 		= $this->partnerModel->getPartners();
		
		# крошки
		$data['crumbs'] = $this->_crumbs($this->data['categories'], $data['category']->id);
		
		$this->_view('category', $data);
	}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// PRODUCT
	public function Product($url = '', $id = 0)
	{
		$data = &$this->data;
		
		if (METHOD == 'POST'){
			# сообщить когда появится
			if (isset($_POST['towaitlist'])){
				echo json_encode($this->productModel->towaitlist());
				exit;
			}
			
			# товар под заказ
			if (isset($_POST['sendunderorder'])){
				$this->clientModel->sendUnderOrder();
				echo json_encode(0);
				exit;
			}
			
			# очистка просмотренных товаров
			if (isset($_POST['remove-viewed'])){
				$this->session->set_userdata('viewed', array());
				exit;
			}
			
			# getSizeMap
			if (isset($_POST['getSizeMap'])){
				$id_category = isset($_POST['id_category']) ? abs((int)$_POST['id_category']) : 0;
				# такая же фунн. как на фильтр размеров (select)
				echo json_encode($this->filterModel->getFilterItemSize($id_category));
				exit;
			}
		}
		
		$data['product'] = $this->productModel->getProduct($id);
		
		if ( ! $data['product']) {$this->_404();return;}
		if ($data['product']->url != $url){ redirect($data['product']->_url, 'location', 301);return;}

		$this->_info($data['product']);

		# вы смотрели этот продукт
		$viewed = $this->session->userdata('viewed');
		$viewed = is_array($viewed) ? $viewed : array();
		$viewed[$data['product']->id] = '';
		$this->session->set_userdata('viewed', $viewed);
		
		# просмотренные товары (карусель)
		$data['product_viewed'] = $this->productModel->getViewedProducts($data['product']->id);
		
		# products discount (карусель)
		$data['products_discount'] = $this->productModel->getProductsDiscount();
		
		$data['categories'] = $this->categoryModel->sortCategories($this->categoryModel->getCategories());
		$data['pages'] 		= $this->pageModel->sortPages($this->pageModel->getPages());
		$data['partners'] 	= $this->partnerModel->getPartners();
		
		# крошки
		$data['crumbs'] = $this->_crumbs($data['categories'], $data['product']->category_id);
		$data['crumbs'][] = array('name'=>$data['product']->name);
		
		$this->_view('product', $data);
	}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// PAGE (надо удалить)
	public function Page($url = '', $id = 0)
	{
		$data = &$this->data;
		
		$data['page'] = $this->pageModel->getPage($url);
		if ( ! $data['page']) {$this->_404();return;}
		if ($data['page']->url != $url){ redirect($data['page']->_url, 'location', 301);return;}
	
		
		# если нет такого
		if( ! $data['page']){$this->_404();return;}	
		
		# мета тэги
		$this->_info($data['page']);
		
		$data['categories'] = $this->categoryModel->sortCategories($this->categoryModel->getCategories());
		$data['pages'] 		= $this->pageModel->sortPages($this->pageModel->getPages());
		$data['partners'] 	= $this->partnerModel->getPartners();
		
		# крошки
		$data['crumbs'] = $this->_crumbs($this->data['pages'], $data['page']->id);
		
		$this->_view('page', $data);
	}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// OPLATA
	public function Oplata()
	{
		$data = &$this->data;
		
		$data['oplata'] = $this->pageModel->getSystemPage('oplata');
		$this->_info($data['oplata']);
		
		$data['categories'] 	= $this->categoryModel->sortCategories($this->categoryModel->getCategories());
		$data['pages'] 			= $this->pageModel->sortPages($this->pageModel->getPages());
		$data['partners'] 		= $this->partnerModel->getPartners();
		
		$this->_view('oplata', $data);
	}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// CONTACTS
	public function Contacts()
	{
		$data = &$this->data;
		
		if(isset($_POST['callback'])){	
			$this->clientModel->callback();
			echo json_encode(0);
			exit;
		}

		$this->_info('', 'Контактная информация');
		
		$data['categories'] 	= $this->categoryModel->sortCategories($this->categoryModel->getCategories());
		$data['pages'] 			= $this->pageModel->sortPages($this->pageModel->getPages());
		$data['partners'] 		= $this->partnerModel->getPartners();

		$this->_view('contacts', $data);
	}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// BUSINESS
	public function Business()
	{
		$data = &$this->data;
		
		# получить инф. СКИДОК
		if (isset($_POST['getdiscounts'])){
			echo json_encode($this->clientModel->getDiscounts());
			exit;
		}
		
		# Заявка на сотрудничество
		if(METHOD == 'POST'){
			$this->clientModel->sendBusiness();
			echo json_encode(0);
			exit;
		}
		
		$data['biznes'] = $this->pageModel->getSystemPage('biznes');
		$this->_info($data['biznes']);
		
		$data['categories'] 	= $this->categoryModel->sortCategories($this->categoryModel->getCategories());
		$data['pages'] 			= $this->pageModel->sortPages($this->pageModel->getPages());
		$data['partners'] 		= $this->partnerModel->getPartners();

		$this->_view('business', $data);
	}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// ABOUT-US
	public function About()
	{
		$data = &$this->data;
		
		$data['home'] = $this->pageModel->getSystemPage('about');
		$this->_info($data['home']);
		
		$data['categories'] 	= $this->categoryModel->sortCategories($this->categoryModel->getCategories());
		$data['pages'] 			= $this->pageModel->sortPages($this->pageModel->getPages());
		$data['partners'] 		= $this->partnerModel->getPartners();

		$this->_view('about', $data);
	}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// PARTNERSHIPS
	public function Partners()
	{
		$data = &$this->data;
		
		$this->_info('', 'Наши партнеры');
		
		$data['categories'] 	= $this->categoryModel->sortCategories($this->categoryModel->getCategories());
		$data['pages'] 			= $this->pageModel->sortPages($this->pageModel->getPages());
		$data['partners'] 		= $this->partnerModel->getPartners();

		$this->_view('partner', $data);
	}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// SEARCH
	public function Search()
	{
		$data = &$this->data;
		
		$this->_info('', 'Поиск');
		$data['search'] = isset($_GET['w']) ? $_GET['w'] : '';
		
		$data['categories'] 	= $this->categoryModel->sortCategories($this->categoryModel->getCategories());
		$data['pages'] 			= $this->pageModel->sortPages($this->pageModel->getPages());
		$data['partners'] 		= $this->partnerModel->getPartners();
		
		$data['search_categories'] = $this->categoryModel->searchCategories($data['search']);
		$data['search_products'] = $this->productModel->searchProducts($data['search']);

		$this->_view('search', $data);
	}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// NOVA-POSHTA
	public function Novaposhta()
	{
		$data = &$this->data;

		if (isset($_POST['getNovaPoshta'])){
			echo json_encode($this->novaposhtaModel->getInvoiceNovaPoshta($_POST['getNovaPoshta']));
			exit;
		}

		# все отделения
		if (isset($_POST['getAllWarenList'])){
			echo json_encode((array)$this->novaposhtaModel->getAllWarenListNovaPoshta());
			exit;
		}
		# отделения города
		if (isset($_POST['getWarenList'])){
			echo json_encode((array)$this->novaposhtaModel->getWarenListNovaPoshta($_POST['getWarenList']));
			exit;
		}
		# города
		if (isset($_POST['getCities'])){
			echo json_encode($this->novaposhtaModel->getCitiesNovaPoshta());
			exit;
		}
		
		redirect('/');
	}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 404
	public function _404()
	{	
		header("HTTP/1.0 404 Not Found");
		$data = &$this->data;
		
		$data['categories'] 	= $this->categoryModel->sortCategories($this->categoryModel->getCategories());
		$data['pages'] 			= $this->pageModel->sortPages($this->pageModel->getPages());
		$data['partners'] 		= $this->partnerModel->getPartners();
		
		$this->_view('404', $data);
	}

}