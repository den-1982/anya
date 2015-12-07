<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

	public $data = array(
		'h1'=>'',
		'name'=>'',
		'title'=>'',
		'metadesc'=>'',
		'metakey'=>'',
		'text'=>'',
		'spam'=>'',
		
		'error'=>array(),
		'crumbs'=>array()
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

		# проверка USER
		$data['user'] = $this->userModel->getUser();
		if ( ! $data['user']){redirect('/user/logout');	exit;}
		
		# получить заказы USER
		if (isset($_POST['getorders'])){
			echo json_encode($this->userModel->getOrders($data['user']->id));
			exit;
		}
		
		# получить историю заказа
		if (isset($_POST['gethistoryorder'])){
			echo json_encode($this->userModel->getHistoryOrder($_POST['gethistoryorder']));
			exit;
		}
		
		# получить инф. СКИДОК
		if (isset($_POST['getdiscounts'])){
			echo json_encode($this->clientModel->getDiscounts());
			exit;
		}

		$this->_info('', 'Личный кабинет');
		
		# крошки
		$data['crumbs'] = array(
			array('id'=>0, 'name'=>'Личный кабинет', '_url'=>'')
		);
		
		$data['categories'] = $this->categoryModel->sortCategories($this->categoryModel->getCategories());
		$data['pages'] 		= $this->pageModel->sortPages($this->pageModel->getPages());
		$data['partners'] 	= $this->partnerModel->getPartners();
		
		$data['cities'] 	= $this->novaposhtaModel->getCitiesNovaPoshta();
		
		$this->_view('user', $data);
	}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// LOGIN
	public function Login()
	{
		$data = &$this->data;
		
		# Аутификация USER
		if (isset($_POST['auth'])){
			$phone		= isset($_POST['phone'])	? $_POST['phone'] : '';
			$password	= isset($_POST['password']) ? $_POST['password'] : '';
			echo $this->userModel->authUser($phone, $password);
			exit;
		}
		
		# проверка USER
		$data['user'] = $this->userModel->getUser();
		if ( $data['user'] ){
			redirect('/user');
			exit;
		}else{
			redirect('/#auth');
			exit;
		}
	}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// LOGOUT
	public function Logout()
	{
		$data = &$this->data;
		$this->userModel->logoutUser();
		redirect('/user/login');
		return;
	}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// ADD USER	
	public function Add()
	{
		$data = &$this->data;
		
		# проверка USER
		$data['user'] = $this->userModel->getUser();
		if ( $data['user'] ){
			redirect('/user');
			exit;
		}

		# добавление USER
		if (isset($_POST['add_user'])){
			$data['error'] = $this->userModel->addUser();
			if ( ! $data['error'] ){
				redirect('/user');
				exit;
			}
		}
		
		$this->_info('', 'Регистрация');
		
		$data['categories'] = $this->categoryModel->sortCategories($this->categoryModel->getCategories());
		$data['pages'] 		= $this->pageModel->sortPages($this->pageModel->getPages());
		$data['partners'] 	= $this->partnerModel->getPartners();
		
		$data['cities'] 		= $this->novaposhtaModel->getCitiesNovaPoshta();
		
		# captcha
		$this->load->helper('captcha');
		$data['captcha'] = create_captcha(
			array(
				'img_width'=>120,
				'img_height'=>35,
				'font_path' => '/css/fonts/arial.ttf'
			)
		);
		$this->session->set_userdata('captcha', $data['captcha']);
		
		# крошки
		$data['crumbs'] = array(
			array('id'=>0, 'name'=>'регистрация', '_url'=>'')
		);
		
		$this->output->set_header("Cache-Control: no-store");
		$this->_view('user_add', $data);
	}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// EDIT USER (NEW)
	public function Edit()
	{
		$data = &$this->data;
		
		# проверка USER
		$data['user'] = $this->userModel->getUser();
		if ( ! $data['user'] ){
			redirect('/user/logout');
			exit;
		}
		
		# добавление USER
		if (isset($_POST['edit_user'])){
			$data['error'] = $this->userModel->editUser();
			if ( ! $data['error'] ){
				redirect('/user');
				exit;
			}
		}
		
		$this->_info('', 'Редактирование данных');
		
		$data['categories'] = $this->categoryModel->sortCategories($this->categoryModel->getCategories());
		$data['pages'] 		= $this->pageModel->sortPages($this->pageModel->getPages());
		$data['partners'] 	= $this->partnerModel->getPartners();
		
		$data['cities'] 		= $this->novaposhtaModel->getCitiesNovaPoshta();
		$data['warenList']		= $this->novaposhtaModel->getWarenListNovaPoshta($data['user']->city);

		# крошки
		$data['crumbs'] = array(
			array('id'=>0, 'name'=>'Личный кабинет', '_url'=>'/user'),
			array('id'=>0, 'name'=>'Редактирование личных данных', '_url'=>'recover')
		);
		
		$this->output->set_header("Cache-Control: no-store");
		$this->_view('user_edit', $data);
	}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// EDIT PASSWORD
	public function Password()
	{
		$data = &$this->data;
		
		# проверка USER
		$data['user'] = $this->userModel->getUser();
		if ( ! $data['user'] ){
			redirect('/user/logout');
			exit;
		}
		
		# смена пароля
		if (isset($_POST['edit_user_pass'])){
			$data['error'] = $this->userModel->editUserPass();
			if ( ! $data['error'] ){
				redirect('/user');
				exit;
			}
		}
		
		$this->_info('', 'Смена пароля');
		
		$data['categories'] = $this->categoryModel->sortCategories($this->categoryModel->getCategories());
		$data['pages'] 		= $this->pageModel->sortPages($this->pageModel->getPages());
		$data['partners'] 	= $this->partnerModel->getPartners();
		
		# крошки
		$data['crumbs'] = array(
			array('id'=>0, 'name'=>'Личный кабинет', '_url'=>'/user'),
			array('id'=>0, 'name'=>'Смена пароля', '_url'=>'password')
		);
		
		$this->output->set_header("Cache-Control: no-store");
		$this->_view('user_edit_pass', $data);
	}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// RECOVER PASSWORD
	public function Recover()
	{
		$data = &$this->data;
		
		if (METHOD != 'POST'){redirect('/user'); exit;}

		# если user зарегился (гоним его прочь)
		$data['user'] = $this->userModel->getUser();
		if ( $data['user'] ){
			redirect('/user');
			exit;
		}
		
		# востановление пароля
		if (isset($_POST['recover'])){
			$data['error'] = $this->userModel->recoverPassword();

			redirect('/user/login');
			exit;
		}
	}	
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 404
	public function _404()
	{	
		header("HTTP/1.0 404 Not Found");
		$data = &$this->data;
		
		$data['categories'] = $this->categoryModel->sortCategories($this->categoryModel->getCategories());
		$data['pages'] 		= $this->pageModel->sortPages($this->pageModel->getPages());
		$data['partners'] 	= $this->partnerModel->getPartners();
		
		$this->_view('404', $data);
	}

}