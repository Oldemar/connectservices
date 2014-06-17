<?php
App::uses('AppController', 'Controller');
/**
 * Sales Controller
 *
 * @property Sale $Sale
 * @property PaginatorComponent $Paginator
 */
class SalesController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Sale->recursive = 0;
		$this->set('sales', $this->Paginator->paginate());
	}

/**
 * index method
 *
 * @return void
 */
	public function mySales() {
		$this->Sale->recursive = 0;
		$this->Paginator->settings = array(
			'conditions'=>array(
				'Sale.user_id'=>$this->objLoggedUser->getID(),
				'Sale.comissioned'=>false
				));
		$this->set('sales', $this->Paginator->paginate());
	}

/**
 * usersales method
 *
 * @return void
 */
	public function allsales() {
		if (!in_array($this->objLoggedUser->getAttr('role_id'), array('1', '2', '8')))
		{
			if ( $this->objLoggedUser->getAttr('role_id') == '5' ) 
			{
				$childrenIds = Hash::extract($this->User->children($this->objLoggedUser->getID(),true), '{n}.User.id');
			}
			if ($this->objLoggedUser->getAttr('role_id') == '4')
			{
				$childrenIds = Hash::extract($this->User->children($this->objLoggedUser->getID()), '{n}.User.id');
			}
			if ($this->objLoggedUser->getAttr('role_id') == '9')
			{
				$childrenIds = $this->objLoggedUser->getAttr('topleader');
			}

			$this->Paginator->settings = array(
				'conditions'=>array(
					'AND'=>array(
						'Sale.user_id'=>$childrenIds,
						'Sale.comissioned'=>false
					)));
		}
		$this->set('sales', $this->Paginator->paginate());
	}

/**
 * usersales method
 *
 * @return void
 */
	public function allsalesAJAX() {

		$this->Sale->recursive = 1;
		$this->autoRender = false;
		$conditions = array('User.region'=>$this->data['region']);
		if (!empty($this->data['start'])) {
			$conditions['Sale.sales_date >='] = date('Y-m-d H:i:s',strtotime($this->data['start'].' 00:00:00'));
			$conditions['Sale.sales_date <='] = date('Y-m-d H:i:s',strtotime($this->data['end'].' 23:59:59'));
		}
//		debug($conditions);
		$this->Paginator->settings = array(
			'conditions'=>array($conditions));

		$sales = $this->Paginator->paginate();
//		debug($sales);
		$arrReturn[] = $this->element('Sales/allsales',array('sales'=>$sales));
		echo json_encode($arrReturn);
		exit;
	}

/**
 * usersales method
 *
 * @return void
 */
	public function listsales($controllerText) {

		$childrenIds = Hash::extract($this->User->children($this->objLoggedUser->getID()), '{n}.User.id');
		$this->Paginator->settings = array(
			'conditions'=>array(
				'AND'=>array(
					'Sale.user_id'=>$childrenIds,
					'Sale.comissioned'=>0,
					'Sale.advanced'=>0
				)));
		$this->set('sales', $this->Paginator->paginate());
		$this->set('controllerText',$controllerText);
	}

/**
 * listsalesAJAX method
 *
 * @return void
 */
	public function listsalesAJAX($controllerText) {

		$this->Sale->recursive = 1;
		$this->autoRender = false;
		$conditions = array('User.region' => $this->data['region'],'Sale.comissioned' => 0);
		if ($controllerText == 'Advance') $conditions['Sale.advanced'] = 0;
		if (!empty($this->data['start'])) {
			$conditions['Sale.sales_date >='] = date('Y-m-d H:i:s',strtotime($this->data['start'].' 00:00:00'));
			$conditions['Sale.sales_date <='] = date('Y-m-d H:i:s',strtotime($this->data['end'].' 23:59:59'));
		}
		$this->Paginator->settings = array(
			'conditions'=>array($conditions));

		$sales = $this->Paginator->paginate();
		$arrReturn[] = $this->element('Sales/listsales',array('sales'=>$sales));
		echo json_encode($arrReturn);
		exit;
	}

/**
 * listsalesAJAX method
 *
 * @return void
 */
	public function updateInstalled() {

		$this->autoRender = false;

		$this->Sale->id = $this->data['sid'];
		$this->Sale->saveField('installed', $this->data['installed']);

		$conditions = array('User.region'=>$this->data['region'],'Sale.comissioned' => 0);
		if (!empty($this->data['start'])) {
			$conditions['Sale.sales_date >='] = date('Y-m-d H:i:s',strtotime($this->data['start'].' 00:00:00'));
			$conditions['Sale.sales_date <='] = date('Y-m-d H:i:s',strtotime($this->data['end'].' 23:59:59'));
		}
		$this->Paginator->settings = array(
			'conditions'=>array($conditions));

		$sales = $this->Paginator->paginate();
		$arrReturn[] = $this->element('Sales/listsales',array('sales'=>$sales));
		echo json_encode($arrReturn);
		exit;
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Sale->exists($id)) {
			throw new NotFoundException(__('Invalid sale'));
		}
		$options = array('conditions' => array('Sale.' . $this->Sale->primaryKey => $id));
		$this->set('sale', $this->Sale->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Sale->create();
			if ($this->Sale->saveAssociated($this->request->data)) {
				$this->Session->setFlash(__('The sale has been saved.'));
				return $this->redirect(array('controller'=>'users','action' => 'dashboard'));
			} else {
				$this->Session->setFlash(__('The sale could not be saved. Please, try again.'));
			}
		}
		$users = $this->Sale->User->find('list',array(
			'conditions'=> array(
				'User.role_id !='=>array('1','2','8','9'))));
		$customers = $this->Sale->Customer->find('list');
		$carriers = $this->Sale->Customer->Carrier->find('list');
		$this->set(compact('users', 'customers','carriers'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Sale->exists($id)) {
			throw new NotFoundException(__('Invalid sale'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Sale->saveAssociated($this->request->data)) {
				$this->Session->setFlash(__('The sale has been saved.'));
				return $this->redirect(array('controller'=>'users','action' => 'dashboard'));
			} else {
				$this->Session->setFlash(__('The sale could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Sale.' . $this->Sale->primaryKey => $id));
			$this->request->data = $this->Sale->find('first', $options);
		}
		$users = $this->Sale->User->find('list');
		$customers = $this->Sale->Customer->find('list');
		$this->set(compact('users', 'customers'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Sale->id = $id;
		if (!$this->Sale->exists()) {
			throw new NotFoundException(__('Invalid sale'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->Sale->delete()) {
			$this->Session->setFlash(__('The sale has been deleted.'));
		} else {
			$this->Session->setFlash(__('The sale could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('controller'=>'users','action' => 'dashboard'));
	}}
