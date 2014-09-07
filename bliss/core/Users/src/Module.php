<?php
namespace Users;

use Bliss\Application,
	Bliss\Module\AbstractModule,
	Bliss\Controller\Container as Controller;
#	Assets\Source\ModuleProviderTrait as AssetProviderTrait,
#	UnifiedUI\UI as Layout,
#	Acl\Acl,
#	Acl\Builder\Container as AclBuilder,
#	Acl\Builder\Source\FileArraySource;

class Module extends AbstractModule 
{
#	use AssetProviderTrait;
	
	const NAME = "users";
	const RESOURCE_NAME = "users";
	const RESOURCE_TITLE = "Users";
	
	/**
	 * @var \Users\Module
	 */
	private static $_self;
	
	/**
	 * @var \Users\Session\Handler
	 */
	private static $_sessionHandler;
	
	/**
	 * @var \Acl\Builder\Container
	 */
	private static $_acl;
	
	/**
	 * @var boolean
	 */
	private $requireLogin = false;
	
	/**
	 * @var boolean
	 */
	private $allowSignUp = true;
	
	/**
	 * @var boolean
	 */
	private $navigationEnabled = true;
	
	public function getName() { return self::NAME; }
	
	public function init() 
	{
		self::$_self = $this;
		
#		$this->application->addEvent(Controller::PRE_EXEC, array($this, "setup"));
#		$this->application->addEvent(Application::EVENT_DISPATCH, array($this, "initResources"), 100);
#		$this->application->addEvent(Application::EVENT_DISPATCH, array($this, "initAccount"));
	}
	
	/**
	 * Require the user to login to the application
	 * 
	 * @param boolean $flag
	 */
	public function setRequireLogin($flag = true)
	{
		$this->requireLogin = (boolean) $flag;
	}
	
	/**
	 * Check if users are required to login
	 * 
	 * @return boolean
	 */
	public function requireLogin()
	{
		return $this->requireLogin;
	}
	
	/**
	 * Set whether to allow users to sign up for accounts
	 * 
	 * @param boolean $flag
	 */
	public function setAllowSignUp($flag = true)
	{
		$this->allowSignUp = (boolean) $flag;
	}
	
	/**
	 * Check if users are allowed to sign up for accounts
	 * 
	 * @return boolean
	 */
	public function allowSignUp()
	{
		return $this->allowSignUp;
	}
	
	/**
	 * Set whether the module's navigation is enabled
	 * 
	 * @param boolean $flag
	 */
	public function setNavigationEnabled($flag = true)
	{
		$this->navigationEnabled = (boolean) $flag;
	}
	
	/**
	 * Set up the module
	 */
	public function setup()
	{
		$this->_initView();
		$this->_initSearch();
		$this->_initNavigation();
	}
	
	/**
	 * Initialize the resources provided by the module
	 */
	public function initResources()
	{
		$db = $this->application->getDatabase();
		$resources = \System\Module::resources();
		$resources->register(User::RESOURCE_NAME, [
			"component" => new User(),
			"collection" => new Collection(),
			"loader" => DbStorage::generateLoader($db),
			"saver" => DbStorage::generateSaver($db)
		]);
		$resources->register(Session\Session::RESOURCE_NAME, [
			"component" => new Session\Session(),
			"collection" => new Session\Collection(),
			"loader" => Session\DbStorage::generateLoader($db)
		]);
	}
	
	/**
	 * Add the user session to the view
	 */
	private function _initView()
	{
		\Bliss\Console::log("Adding view partials needed for the Users module");
		
		$view = $this->application->getView();
		$view->setAttributes(array(
			"userSession" => self::session()
		));
		$view->addAreaPartial(
			Layout::AREA_HEADER_MENU,
			$this->resolvePath("views/directives/account-menu.html")
		);
		$view->addAreaPartial(
			Layout::AREA_SCRIPTS,
			$this->resolvePath("layouts/partials/scripts.html")
		);
	}
	
	/**
	 * Initialize the account session
	 */
	public function initAccount()
	{
		$this->_populateAcl();
		
		$request = $this->application->getRequest();
		$response = $this->application->getResponse();
		$module = $request->getModuleName();
		$controller = $request->getControllerName();
		$action = $request->getParam("action");
		$session = self::session();
		$user = $session->getUser();
		$aclContainer = self::acl()->build();
		$user->setAcl(
			$aclContainer->getRole($user->getRole())
		);
		
		$user->getAcl()->setDefaultPermissions(array(
			array(
				"action" => Acl::UPDATE,
				"params" => ["userId"=>$user->getId()],
				"isAllowed" => true
			),
			array(
				"action" => Acl::DELETE,
				"params" => ["userId"=>$user->getId()],
				"isAllowed" => true
			)
		));
		
		if (!$user->isAllowed($module, \Acl\Acl::READ, ["controller" => $controller, "action" => $action])) {
			$canSignIn = $user->isAllowed(self::RESOURCE_NAME, [Acl::READ], ["controller"=>"account","action"=>"sign-in"]);
			
			if ($session->isValid() || $request->getParam("format") !== null) {
				throw new \Exception("You do not have permission to access this page", \Bliss\Request::FORBIDDEN);
			} else if ($canSignIn) {
				$response->redirect(
					$request->uri("account/sign-in")
				);
			} else {
				$response->setCode(\Bliss\Request::NOT_FOUND);
			}
		}
		
		$timezone = $user->get("timezone");
		if ($timezone) {
			date_default_timezone_set($timezone);
		}

		\Logs\Module::logger()->setUser($user);
	}
	
	/**
	 * Collect all modules that are registered as a \Users\Acl\ModuleTrait
	 * and apply their ACL properties
	 */
	private function _populateAcl()
	{
		$acl = self::acl();
		foreach ($this->application->getModules() as $module) {
			$uses = class_uses($module);
			if (in_array("Users\\Acl\\ModuleTrait", $uses)) {
				$module->applyUserAcl($acl, $module);
			}
			
		}
	}
	
	/**
	 * Initialize the global server provider for users
	 */
	private function _initSearch()
	{
		if ($this->application->hasModule("search")) {
			$container = \Search\Module::container();
			$container->registerProvider(
				new Search\DbProvider($this->application->getDatabase())
			);
		}
	}
	
	/**
	 * Add the module's assets to the application
	 */
	private function _initAssets()
	{
		$assets = \Assets\Module::container();
		$assets->addModuleSource($this, new JsSource("assets/js/module.js"));
		$assets->addModuleSource($this, new CssSource("assets/css/module.css"));
	}
	
	/**
	 * Add the user navigation if it's enabled
	 */
	private function _initNavigation()
	{
		if ($this->navigationEnabled === true) {
			$nav = \System\Module::navigation();
			$nav->addPages(
				include $this->resolvePath("config/navigation.php")
			);
		}
	}
	
	/**
	 * Get the user's session
	 * 
	 * @return \Users\Session\Session
	 */
	public static function session()
	{
		return self::sessionHandler()->getSession();
	}
	
	/**
	 * Get the user session handler
	 * 
	 * @return \Users\Session\Handler
	 * @throws \UnexpectedValueException
	 */
	public static function sessionHandler()
	{
		if (!isset(self::$_sessionHandler)) {
			$db = self::$_self->getApplication()->getDatabase();
			$sessionStorage = new Session\DbStorage($db);
			$userLoader = DbStorage::generateLoader($db);
			$sessionHandler = new Session\Handler($sessionStorage, $userLoader);
			$sessionHandler->setSessionLifetime(self::$_self->config()->get("sessionLifetime", 0));

			try {
				$sessionHandler->load();
			} catch (Session\InvalidSessionIdException $e) {}

			self::$_sessionHandler = $sessionHandler;
		}
		
		return self::$_sessionHandler;
	}
	
	/**
	 * Get the user ACL builder
	 * 
	 * @return \Acl\Builder\Container
	 */
	public static function acl()
	{
		if (!isset(self::$_acl)) {
			self::$_acl = new AclBuilder();
			self::$_acl->addSource(
				new FileArraySource(self::$_self->resolvePath("config/acl.php"))
			);
		}
		
		return self::$_acl;
	}
}