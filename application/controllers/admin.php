<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {
	public $data = array(
		'act'		=>'',
		'action'	=>'',
		'parent'	=>0,
		'new_orders'=> array()
	);
	
	public function __construct()
	{
		parent::__construct();
		
		# helpers
		$this->load->helpers('functions');

		$this->load->model(
			array(
				'admin/settingsModel',
				'admin/adminModel',
				'admin/categoryModel',
				'admin/pageModel',
				'admin/productModel',
				'admin/filterModel',
				'admin/manufacturerModel',
				'admin/partnershipsModel',
				'admin/reviewsModel',
				'admin/userModel',
				'admin/ordersModel',
				'admin/cartsModel',
				'admin/filesModel',
				'admin/novaposhtaModel',
				'admin/waitlistModel',
				'admin/newsletterModel'
			)
		);
		
		$this->load->library('my_imagemagic');
		

		# =============================== VALID ADMIN
		$this->adminModel->VALID_ADMIN();
		# ============================END VALID ADMIN
		
		# Получить настройки
		$this->data['settings'] = $this->settingsModel->getSettings();
		
		
		# НОВЫЕ ЗАКАЗЫ
		$this->data['new_count_orders'] = $this->ordersModel->getCountNewOrders();
		
		# НОВЫЕ ОТЗЫВЫ
		$this->data['new_count_comments'] = $this->reviewsModel->getCountNewReviews();
		
		# ЖДУТ ТОВАРЫ (уведомление)
		$this->data['count_waitlist'] = $this->waitlistModel->getCountWaitList();
	}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// _VIEW
	private function _view($type = 'a_home', $data = array())
	{
		$this->load->view('admin/parts/a_header.php', $data);
		$this->load->view('admin/'.$type.'.php');
		$this->load->view('admin/parts/a_footer.php');
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// _CRUMBS	
	private function _crumbs($data = array(), $parent = 0)
	{
		static $j = 0;
		
		$crumbs = array();
		do{
			$terac = false;
			foreach ($data as  $item){
				foreach ($item as $i){
					if ($i->id == $parent){
						$crumbs[] = array('id'=>$i->id, 'name'=>$i->name);
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
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// URL (NEW)
	private function _getParents($parents, $not = 0, $parent_id = 0, $parent_name = '', $level = -1) 
	{
		$output = array();
		$level++;
		
		if (array_key_exists($parent_id, $parents)) {
			if ($parent_name != '') {
				$parent_name .= ' > ';
			}

			foreach ($parents[$parent_id] as $parent) {
				# избегаем зацыкливания
				if($parent->id == $not) continue;
				
				$output[$parent->id] = array(
					'id' => $parent->id,
					'name' => $parent_name . $parent->name,
					'_name'=>$parent->name,
					'level'=>$level
				);
				
				$output += $this->_getParents($parents, $not, $parent->id, $parent_name . $parent->name, $level);
			}
		}
		return $output;
	}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// LOGOUT	
	public function Logout()
	{
		session_destroy();
		redirect('/admin');
		exit;
	}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// INDEX
	public function Index()
	{
		redirect('/admin/orders/new');
		return;

		$data = &$this->data;
		$data['h1'] = 'Админ';

		$this->_view('a_index', $data);
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// CATEGORY
	public function Category()
	{
		$data = &$this->data;
		
		$data['path']	= '/admin/category/';
		$data['action'] = 'category';
		$data['act']    = 'all';
		$data['h1']		= 'Редактирование категорий';
		
		$data['parent']	= isset($_GET['parent']) ? abs((int)$_GET['parent']) : 0;

		
		if (METHOD == 'POST'){
			if (isset($_POST['delete'])){
				$res = $this->categoryModel->delCategory($_POST['delete']);
				echo json_encode($res);
				exit;
			}
			if (isset($_POST['toggle']) ){
				echo json_encode($this->categoryModel->setVisibilityCategory()); 
				exit;
			}
			if (isset($_POST['add'])){
				$this->categoryModel->addCategory(); 
				redirect('/admin/category/?parent='. (isset($_POST['parent']) ? $_POST['parent'] : 0));
				exit;
			}
			if (isset($_POST['edit'])){
				$res = $this->categoryModel->updateCategory();
				redirect('/admin/category/?parent='. (isset($_POST['parent']) ? $_POST['parent'] : 0));
				exit;
			}
			if (isset($_POST['category_order'])){
				$this->categoryModel->sortOrderCategory();
				exit;
			}
			
			exit;
		}
		
		/*
		if (isset($_GET['delete'])){
			$this->categoryModel->delCategory($_GET['delete']);
			redirect('/admin/category/?parent='.$data['parent']);
			exit;
		}
		*/
		
		if (isset($_GET['update'])){
			$data['category'] = $this->categoryModel->getCategory((int)$_GET['update']);
			if ( !$data['category']){redirect('/admin/category/?parent='.$data['parent']);exit;}

			$data['act']	= 'update';
			$data['h1'] 	= 'Редактирование категории: <span class="c-red">'.$data['category']->name.'</span>';
			
			$data['categories'] = $this->categoryModel->sortCategories($this->categoryModel->getCategories());
			$data['parents']	= $this->_getParents($data['categories'], $data['category']->id);
		}
		if (isset($_GET['add'])){
			$data['act']  = 'add';
			$data['h1'] = 'Создание категории';
			
			$data['categories']	= $this->categoryModel->sortCategories($this->categoryModel->getCategories());
			$data['parents']	= $this->_getParents($data['categories']);
		}

		
		$data['categories']	= $this->categoryModel->sortCategories($this->categoryModel->getCategories());
		$data['crumbs']		= $this->_crumbs($data['categories'], $data['parent']);
		
		$this->_view('a_category', $data);
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// PRODUCTS
	public function Products()
	{
		$data = &$this->data;
		
		$data['path']	= '/admin/products/';
		$data['action']	= 'products';
		$data['act']	= 'all';
		$data['h1']		= 'Продукты';
		
		$data['parent']	= isset($_GET['parent']) ? abs((int)$_GET['parent']) : 0;


		if (isset($_POST['getProductOfCategory'])){
			$products = $this->productModel->getProducts($_POST['getProductOfCategory']);
			echo json_encode($products); 
			exit;
		}
		
		if ( isset($_POST['getFiltersOfCategory']) ){
			echo json_encode($this->filterModel->getFiltersOfCategory($_POST['getFiltersOfCategory'])); 
			exit;
		}
		
		if (isset($_POST['product_order'])){
			$this->productModel->sortOrderProduct();
			exit;
		}
		
		if (isset($_POST['toggle']) ){
			switch($_POST['toggle']){
				case 'visibility':
					echo json_encode($this->productModel->setVisibilityProduct()); 
					exit;
				case 'new':
					echo json_encode($this->productModel->setNewProduct()); 
					exit;
				case 'hit':
					echo json_encode($this->productModel->setHitProduct()); 
					exit;
				default:exit;
			}
		}
		
		if (isset($_POST['add'])){
			error_reporting(E_ALL);
			$this->productModel->addProduct(); 
			redirect($data['path'].'?parent='.$data['parent']);
			exit;
		}
		
		if (isset($_POST['edit'])){
			error_reporting(E_ALL);
			$res = $this->productModel->updateProduct();
			redirect($data['path'].'?parent='.$data['parent']);
			exit;
		}

		if (isset($_GET['delete'])){
			$this->productModel->delProduct($_GET['delete']);
			redirect($data['path'].'?parent='.$data['parent']);
			exit;
		}
		
		$data['categories']	= $this->categoryModel->sortCategories($this->categoryModel->getCategories());
		
		if (isset($_GET['update'])){
			$data['product'] = $this->productModel->getProduct((int)$_GET['update']);
			if ($data['product']){
				$data['act']  = 'update';
				$data['h1'] = 'Редактирование продукта: <span class="c-red">'.$data['product']->name.'</span>';
				
				$data['parents']		= $this->_getParents($data['categories']);
				$data['manufacturer'] 	= $this->manufacturerModel->getManufacturers();
				$data['filters']			= $this->filterModel->getFiltersOfCategory($data['parent']);
				$data['filter_item_pricing']= $this->filterModel->getFilterItemPricing();
				
				$this->_view('a_products', $data);
				return;
			}
		}
		
		if (isset($_GET['add'])){
			$data['act']  = 'add';
			$data['h1'] = 'Создание продукта';
			
			$data['parents']		= $this->_getParents($data['categories']);
			$data['manufacturer'] 	= $this->manufacturerModel->getManufacturers();
			$data['filters']			= $this->filterModel->getFiltersOfCategory($data['parent']);
			$data['filter_item_pricing']= $this->filterModel->getFilterItemPricing();
				
			$this->_view('a_products', $data);
			return;
		}
		
		$data['parents']		= $this->_getParents($data['categories']);
		$data['products']		= $this->productModel->getProducts($data['parent']);
		$data['manufacturer'] 	= $this->manufacturerModel->getManufacturers();
		
		$this->_view('a_products', $data);
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// FILTER
	public function Filter()
	{
		$data = &$this->data;
		
		$data['parent']	= isset($_GET['parent']) ? abs((int)$_GET['parent']) : 0;
		$data['path']	= '/admin/filter/';
		$data['action']	= 'filter';
		$data['act']	= 'all';
		$data['h1']		= 'Фильтры';

		if (METHOD == 'POST'){
			if ( isset($_POST['filter_order']) ){
				$this->filterModel->setOrderFilter();
				exit;
			}
			if ( isset($_POST['toggle']) ){
				switch($_POST['toggle']){
					case 'visibility':
						echo json_encode($this->filterModel->setVisibilityFilter());
						exit;
					default:exit;
				}
			}
			if ( isset($_POST['add']) ){
				$this->filterModel->addFilter(); 
				redirect($data['path']);
				exit;
			}
			if ( isset($_POST['edit']) ){
				$res = $this->filterModel->updateFilter();
				redirect($data['path']);
				exit;
			}
			
			exit;
		}
		
		
		if (isset($_GET['delete'])){
			$this->filterModel->deleteFilter($_GET['delete']);
			redirect($data['path'].'?parent=' . $data['parent']);
			exit;
		}
		
		if (isset($_GET['update'])){
			$data['filter'] = $this->filterModel->getFilter((int)$_GET['update']);
			if ( ! $data['filter'] ){
				redirect($data['path'].'?parent='.$data['parent']);
				exit;
			}
			
			$data['act']		= 'update';
			$data['h1']			= 'Редактирование фильтра: <span style="color:red;">'.$data['filter']->name.'</span>';
			
			$data['categories'] = $this->categoryModel->sortCategories($this->categoryModel->getCategories(0));
			$data['categories']	= $this->_getParents($data['categories']);

			$this->_view('a_filter', $data);
			return;
		}
		
		if (isset($_GET['add'])){
			$data['act']  = 'add';
			$data['h1']	= 'Создание фильтра';
			
			$data['categories'] = $this->categoryModel->sortCategories($this->categoryModel->getCategories(0));
			$data['categories']	= $this->_getParents($data['categories']);
			
			$this->_view('a_filter', $data);
			return;
		}
		
		$data['filters'] = $this->filterModel->getFilters();

		$this->_view('a_filter', $data);
		return;
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// PAGES
	public function Pages()
	{
		$data = &$this->data;
		
		$data['path']		= '/admin/pages/';
		$data['action']   	= 'pages';
		$data['act']      	= 'all';
		$data['h1']     	= 'Редактирование страниц';
		
		$data['parent']		= isset($_GET['parent']) ? abs((int)$_GET['parent']) : 0;

		if(METHOD == 'POST'){
			
			if (isset($_POST['page_order']) ){
				echo json_encode($this->pageModel->sortOrderPages()); 
				exit;
			}
			if (isset($_POST['toggle']) ){
				echo json_encode($this->pageModel->setVisibilityPage()); 
				exit;
			}
			if (isset($_POST['add'])){
				$this->pageModel->addPage(); 
				redirect('/admin/pages/?parent='.$data['parent']);
				exit;
			}
			if (isset($_POST['edit'])){
				$res = $this->pageModel->updatePage();
				redirect('/admin/pages/?parent='.$data['parent']);
				exit;
			}
			
			exit;
		}
		
		
		if (isset($_GET['delete'])){
			$this->pageModel->delPage($_GET['delete']);
			redirect('/admin/pages/?parent='.$data['parent']);
			exit;
		}
		
		if (isset($_GET['update'])){
			$data['page'] = $this->pageModel->getPage($_GET['update']);
			if ($data['page']){
				$data['act']	= 'update';
				$data['h1']		= 'Редактирование страницы: <span class="c-red">'.$data['page']->name.'</span>';

				$this->_view('a_pages', $data);
				return;
			}
		}
		
		if (isset($_GET['add'])){
			$data['act']	= 'add';
			$data['h1']		= 'Создание страницы';
			
			$this->_view('a_pages', $data);
			return;
		}
		
		$data['pages']		= $this->pageModel->getPages();
		
		$this->_view('a_pages', $data);
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// HOME
	public function Home()
	{
		$data = &$this->data;
		
		$data['path']		= '/admin/home/';
		$data['action']   	= 'home';
		$data['act']      	= 'all';
		$data['h1']     	= 'Главная страница';
		
		$data['parent']		= isset($_GET['parent']) ? abs((int)$_GET['parent']) : 0;

		if (isset($_POST['edit'])){
			$this->pageModel->updateHomePage();
			exit;
		}
		
		$data['home'] = $this->pageModel->getPageSystem('home');
		
		$this->_view('a_home', $data);
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// ABOUT
	public function About()
	{
		$data = &$this->data;
		
		$data['path']		= '/admin/about/';
		$data['action']   	= 'about';
		$data['act']      	= 'all';
		$data['h1']     	= 'О Нас';
		
		$data['parent']		= isset($_GET['parent']) ? abs((int)$_GET['parent']) : 0;

		if (isset($_POST['edit'])){
			$this->pageModel->updateAboutPage();
			exit;
		}
		
		$data['about'] = $this->pageModel->getPageSystem('about');
		
		$this->_view('a_about', $data);
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// OPLATA
	public function Oplata()
	{
		$data = &$this->data;
		
		$data['path']		= '/admin/oplata/';
		$data['action']   	= 'oplata';
		$data['act']      	= 'all';
		$data['h1']     	= 'Оплата и доставка';
		
		$data['parent']		= isset($_GET['parent']) ? abs((int)$_GET['parent']) : 0;

		if (isset($_POST['edit'])){
			$this->pageModel->updateOplataPage();
			exit;
		}
		
		$data['oplata'] = $this->pageModel->getPageSystem('oplata');
		
		$this->_view('a_oplata', $data);
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// BIZNES
	public function Biznes()
	{
		$data = &$this->data;
		
		$data['path']		= '/admin/biznes/';
		$data['action']   	= 'biznes';
		$data['act']      	= 'all';
		$data['h1']     	= 'Бизнес предложение';
		
		$data['parent']		= isset($_GET['parent']) ? abs((int)$_GET['parent']) : 0;

		if (isset($_POST['edit'])){
			$this->pageModel->updateBiznesPage();
			exit;
		}
		
		$data['biznes'] = $this->pageModel->getPageSystem('biznes');
		
		$this->_view('a_biznes', $data);
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// MANUFACTURER
	public function Manufacturer()
	{
		$data = &$this->data;

		$data['path']	= '/admin/manufacturer/';
		$data['action'] = 'manufacturer';
		$data['act']	= 'all';
		$data['h1']		= 'Производители';
		
		if(METHOD == 'POST'){
			if (isset($_POST['toggle']) ){
				echo json_encode($this->manufacturerModel->setVisibilityManufacturer()); 
				exit;
			}
			if (isset($_POST['add'])){
				$this->manufacturerModel->addManufacturer(); 
				redirect('/admin/manufacturer');
				exit;
			}
			if (isset($_POST['edit'])){
				$res = $this->manufacturerModel->updateManufacturer();
				redirect('/admin/manufacturer');
				exit;
			}
			if (isset($_POST['manufacturer_order'])){
				$this->manufacturerModel->sortOrderManufacturer();
				exit;
			}
			
			exit;
		}
		
		
		if (isset($_GET['delete'])){
			$this->manufacturerModel->delManufacturer($_GET['delete']);
			redirect('/admin/manufacturer/?parent='.$data['parent']);
			exit;
		}
		
		if (isset($_GET['update'])){
			$data['manufacturer'] = $this->manufacturerModel->getManufacturer($_GET['update']);
			if ($data['manufacturer']){
				$data['act']= 'update';
				$data['h1']	= 'Редактирование производителя: <span class="c-red">'.$data['manufacturer']->name.'</span>';
				
				$this->_view('a_manufacturer', $data);
				return;
			}	
		}
		if (isset($_GET['add'])){
			$data['act']= 'add';
			$data['h1']	= 'Создание производителя';
			
			$this->_view('a_manufacturer', $data);
			return;
		}	
		
		$data['manufacturer'] = $this->manufacturerModel->getManufacturers();
		
		$this->_view('a_manufacturer', $data);
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// PARTNERSHIPS
	public function Partnerships()
	{
		$data = &$this->data;

		$data['path']	= '/admin/partnerships/';
		$data['action'] = 'partnerships';
		$data['act']	= 'all';
		$data['h1']		= 'Партнеры';
		
		if (METHOD == 'POST'){
			if (isset($_POST['toggle']) ){
				echo json_encode($this->partnershipsModel->setVisibilityPartnership()); 
				exit;
			}
			if (isset($_POST['add'])){
				$this->partnershipsModel->addPartnerships(); 
				redirect('/admin/partnerships');
				exit;
			}
			if (isset($_POST['edit'])){
				$this->partnershipsModel->updatePartnerships();
				redirect('/admin/partnerships');
				exit;
			}
			
			exit;
		}
		
		
		if (isset($_GET['delete'])){
			$this->partnershipsModel->delPartnership($_GET['delete']);
			redirect('/admin/partnerships');
			exit;
		}
		if (isset($_GET['update'])){
			$data['partnership'] = $this->partnershipsModel->getPartnership($_GET['update']);
			if ($data['partnership']){
				$data['act']= 'update';
				$data['h1'] = 'Редактирование: <span class="c-red">'.$data['partnership']->name.'</span>';
				
				$this->_view('a_partnerships', $data);
				return;
			}
		}
		if (isset($_GET['add'])){
			$data['act']= 'add';
			$data['h1']	= 'Добавить партнера';
			
			$this->_view('a_partnerships', $data);
			return;
		}
		
		$data['partnerships'] = $this->partnershipsModel->getPartnerships();
		
		$this->_view('a_partnerships', $data);
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// SETTINGS
	public function Settings()
	{
		$data = &$this->data;

		$data['path']		= '/admin/settings/';
		$data['action']   	= 'settings';
		$data['act']      	= 'all';
		$data['h1']     	= 'Настройки';
		
		# обновление данных
		if (isset($_POST['edit'])){
			$this->settingsModel->editSettings();
			exit;
		}

		$data['socials']	= $this->settingsModel->getSocial();
		$data['admin']		= $this->adminModel->getAdmin();

		$this->output->set_header("Cache-Control: no-store");
		$this->_view('a_settings', $data);
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// PRICE-LIST (NEW)
	public function Pricelist()
	{
		# загрузка PRICE-LIST
		if (isset($_FILES['pricelist'])){
			$pricelist = $_FILES['pricelist'];
			if ($pricelist['error'] == 0){
				$info = @pathinfo($pricelist['name']);
				$mime = isset($info['extension']) ? $info['extension'] : '';
				
				$this->db->query('DELETE FROM pricelist');
				
				$f = file_get_contents($pricelist['tmp_name']);
				
				$this->db->query('INSERT INTO pricelist (pricelist, mime) VALUES ("'.mysql_real_escape_string($f).'", "'.$mime.'")');
			}
		}
		exit;
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// USER
	public function User()
	{
		$data = &$this->data;
		
		$data['action']	= 'user';
		$data['path']	= '/admin/user/';
		$data['act']	= 'all';
		$data['h1']		= 'Клиенты';
		
		$data['sort'] = isset($_GET['sort']) ? (int)$_GET['sort'] : 0;
		
		# устанавливаем СКИДКУ
		if (METHOD == 'POST'){
			# установить скидку для User
			if (isset($_POST['setdiscount'])){
				$this->userModel->setUserDiscount();
				exit;
			}
			# Добавление USER
			if (isset($_POST['add'])){
				$this->userModel->addUser();
				redirect('/admin/user');
				exit;
			}
			# Редактирование данных USER
			if (isset($_POST['edit'])){
				$this->userModel->editUser();
				redirect('/admin/user');
				exit;
			}
			# Новая-Почта (офисы выбранного города)
			if (isset($_POST['getWarenListNovaPoshta'])){
				$warenList = $this->novaposhtaModel->getWarenListNovaPoshta($_POST['getWarenListNovaPoshta']);
				echo json_encode($warenList);
				exit;
			}
			
			exit;
		}
		
		# СКИДКИ
		$data['discounts'] = $this->adminModel->getDiscounts();

		# редактирование USER
		if (isset($_GET['edit'])){
			$data['user'] = $this->userModel->getUser($_GET['edit']);
			if ($data['user']){
				$data['h1']		= 'Редактирование данных: <span class="c-red">'.$data['user']->name.'</span>';
				$data['act']	= 'edit';
				$data['cities']	= $this->novaposhtaModel->getCitiesNovaPoshta();
				$data['warenList']	= $this->novaposhtaModel->getWarenListNovaPoshta($data['user']->city);

				$this->_view('a_user', $data);
				return;
			}
		}
		
		# Добавление USER
		if (isset($_GET['add'])){
			$data['h1']		= 'Добавить клиента';
			$data['act']	= 'add';
			$data['cities']	= $this->novaposhtaModel->getCitiesNovaPoshta();

			$this->_view('a_user', $data);
			return;
		}
		
		# редактирование USER
		if (isset($_GET['delete'])){
			$this->userModel->delUsers($_GET['delete']);
			redirect('/admin/user');
			exit;
		}
		
		$data['users'] = $this->userModel->getUsers();
		
		$this->_view('a_user', $data);
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// ORDERS
	public function Orders($act = '', $user_id = 0)
	{
		$data = &$this->data;
		
		$data['path']	= '/'.$this->uri->uri_string();
		$data['action'] = 'orders';
		$data['act']    = $act;
		$data['h1']		= 'Заказы';
		
		# статус заказа ()
		if (isset($_POST['set_status_order'])){
			$this->ordersModel->setStatusOrder();
			exit;
		}
		# получить данные о заказе
		if (isset($_POST['getorder'])){
			$order = $this->ordersModel->getOrder($_POST['getorder']);
			echo json_encode($order);
			exit;
		}
		
		
		
		# Удалить заказ
		if (isset($_GET['delete'])){
			$this->ordersModel->delOrder($_GET['delete']);
			redirect($data['path']);
			exit;
		}
		# Новые заказы
		if ($data['act'] == 'new'){
			$data['new_orders'] = $this->ordersModel->getNewOrders();
			if ($data['new_orders']){
				$data['h1']		= 'Новые заказы';

				$this->_view('a_orders', $data);
				return;
			}
		}
		# User заказы
		if ($data['act'] == 'user'){
			$data['user'] = $this->ordersModel->getOrdersOfUser($user_id);
			if ($data['user']){
				$data['h1']	= 'История заказов: <span class="c-red">'.$data['user']->name.'</span>';
				
				$this->_view('a_orders', $data);
				return;
			}
		}

		$this->_view('a_orders', $data);
	}	
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// CARTS DISCOUNT
	public function Carts()
	{
		$data = &$this->data;
		
		$data['action']	= 'carts';
		$data['path']	= '/'.$this->uri->uri_string();
		$data['act']	= 'all';
		$data['h1']		= 'Скидочные карты';
		
		# Добавление USER
		if (isset($_POST['add'])){
			$res = $this->cartsModel->addCart();
			echo json_encode($res);
			exit;
		}
		# Редактирование данных USER
		if (isset($_POST['edit'])){
			$res = $this->cartsModel->editCart();
			echo json_encode($res);
			exit;
		}
			
		
		# СКИДКИ
		$data['discounts'] = $this->adminModel->getDiscounts();

		# редактирование USER
		if (isset($_GET['edit'])){
			$data['cart'] = $this->cartsModel->getCart($_GET['edit']);
			if ($data['cart']){
				$data['h1']		= 'Редактировать карту: <span class="c-red">'.$data['cart']->code.'</span>';
				$data['act']	= 'edit';
				
				$this->_view('a_carts', $data);
				return;
			}
		}
		
		# Добавление USER
		if (isset($_GET['add'])){
			$data['h1']		= 'Добавить карту';
			$data['act']	= 'add';

			$this->_view('a_carts', $data);
			return;
		}
		
		# редактирование USER
		if (isset($_GET['delete'])){
			$this->cartsModel->delCart($_GET['delete']);
			redirect($data['path']);
			exit;
		}
		
		$data['carts'] = $this->cartsModel->getCarts();
		
		$this->_view('a_carts', $data);
	}	
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// REVIEWS	
	public function Reviews()
	{
		$data = &$this->data;
	
		$data['path']		= '/admin/reviews/';
		$data['action']   	= 'reviews';
		$data['act']      	= 'all';
		$data['h1']     	= 'Отзывы';
		
		if (METHOD == 'POST'){
			if( isset($_POST['edit']) ){
				$this->reviewsModel->updateReview();
				redirect($data['path']);
				exit;
			}
			if( isset($_POST['add']) ){
				$this->reviewsModel->addReview();
				redirect($data['path']);
				exit;
			}
			if ( isset($_POST['toggle']) ){
				switch($_POST['toggle']){
					case 'visibility':
						echo json_encode($this->reviewsModel->setVisibilityReview()); 
						exit;
					default:exit;
				}
			}
		}
		
		if (isset($_GET['delete'])){
			$this->reviewsModel->deleteReview($_GET['delete']);
			redirect($data['path']);
			exit;
		}
		
		if (isset($_GET['update'])){
			$data['review'] = $this->reviewsModel->getReview($_GET['update']);
			if ( ! $data['review']) redirect($data['path']);
			
			$data['h1'] = 'Редактирование отзыва';
			$data['act'] = 'update';
			$this->_view('a_reviews', $data);
			return;
		}elseif(isset($_GET['add'])){
			$data['act'] = 'add';
			$data['h1'] = 'Добавление отзыва';
			$this->_view('a_reviews', $data);
			return;
		}
		
		$data['reviews'] = $this->reviewsModel->getReviews();
		
		$this->_view('a_reviews', $data);
	}	
	
	
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// NOVA-POSHTA	
	public function Novaposhta()
	{
		$data = &$this->data;
	
		$data['path']	= '/admin/novaposhta/';
		$data['action']	= 'novaposhta';
		$data['act']	= 'all';
		$data['h1']		= 'Новая-Почта';
		
		# Новая-Почта (офисы выбранного города)
		if (isset($_POST['getWarenListNovaPoshta'])){
			$warenList = $this->novaposhtaModel->getWarenListNovaPoshta($_POST['getWarenListNovaPoshta']);
			echo json_encode($warenList);
			exit;
		}
		# Новая-Почта (офисы выбранного города)
		if (isset($_POST['refresh'])){
			$res = $this->novaposhtaModel->refreshNovaPoshta();
			echo json_encode($res);
			exit;
		}
		
		//$this->novaposhtaModel->refreshNovaPoshta();
		
		$data['cities']	= $this->novaposhtaModel->getCitiesNovaPoshta();
		
		$this->_view('a_novaposhta', $data);
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// WAITLIST	
	public function Waitlist()
	{
		$data = &$this->data;
	
		$data['path']	= '/admin/waitlist/';
		$data['action']	= 'waitlist';
		$data['act']	= 'all';
		$data['h1']		= 'Ждут появление товара';
		
		if (isset($_GET['delete'])){
			$product_id = $_GET['delete'];
			$id_size = isset($_GET['id_size']) ? $_GET['id_size'] : 0;
			$this->waitlistModel->delWaitList($product_id, $id_size);
			redirect($data['path']);
			exit;
		}
		
		if (isset($_POST['edit'])){
			$this->waitlistModel->sendEmailWaitList();
			exit;
		}
		
		$data['waitListProducts'] = $this->waitlistModel->getWaitList();
		
		$this->_view('a_waitlist', $data);
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// NEWSLETTER	
	public function Newsletter()
	{
		$data = &$this->data;
	
		$data['path']	= '/admin/newsletter/';
		$data['action'] = 'newsletter';
		$data['act']	= 'all';
		$data['h1']		= 'Рассылка новостией';
		
		if (isset($_POST['send_newsletter'])){
			$this->newsletterModel->sendNewsLetter();
			exit;
		}
		
		$this->_view('a_newsletter', $data);
	}	
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// FILE_MANAGER
	public function Filesmanager()
	{
		# Подключение самой модели
		$this->load->model('admin/filesModel');
		
		# Подключение стилей
		if (isset($_GET['css'])){$this->filesModel->getCss();}
		
		if (METHOD == 'POST'){
			# если нет $_POST['action']
			if(!isset($_POST['action'])){show_404();exit;}
			
			# если нет метода в $this->filesModel
			if(!in_array($_POST['action'], get_class_methods($this->filesModel))){show_404();exit;}
			
			$res = $this->filesModel->$_POST['action']();
			
			echo json_encode($res);
			exit;
			
		}else{
			show_404();
			exit;
		}
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// TEST
	public function Test()
	{
		
		// $to			= 'hamet@ua.fm';
		// $to			= 'vladexsite@gmail.com';
		// $tema		= 'Обратная связь';	
		// $headers	= "From: ".strtoupper($_SERVER['SERVER_NAME'])." <webmaster@".strtoupper($_SERVER['SERVER_NAME']).">\r\n";
		// $headers	.= "Content-type: text/html; charset=\"utf-8\"";
		// $msg = 'den den den den den den den den den den den';
		
		// $res = mail($to, $tema, $msg, $headers);
		// var_dump($res);
		// exit;
		
		// $res2 = mail('hamet@ua.fm', 'TEST', "den den den den den den den den den den den ");
		// $res = mail('vladexsite@gmail.com', 'TEST', "den den den den den den den den den den den ");
		// echo '<pre>';
		// var_dump($res);
		// var_dump($res2);
		// exit;
		
		// $this->load->library('email');
		// $config['protocol'] = 'sendmail';
		// $config['mailpath'] = '/usr/sbin/sendmail';
		// $this->email->initialize($config);
		
		/*
		// $config['protocol'] = 'mail'; # mail, sendmail, или smtp
		// $this->email->initialize($config);
		
		$this->email->from('webmaster@crystalline.in.ua', 'DEN');
		// $this->email->to('vladexsite@gmail.com');
		$this->email->to('hamet@ua.fm');
		$this->email->subject('ТEST');
		$this->email->message('Тестирование класса отправки сообщений');
		$this->email->send();

		echo $this->email->print_debugger();
		# fdAIEmOT
		*/
		
		//$this->_view('a_test', $data);
	}

}
