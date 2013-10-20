<?php
namespace WebStream\Core;

use WebStream\Annotation\Inject;
use WebStream\Annotation\Filter;
use WebStream\Annotation\FilterReader;
use WebStream\Annotation\AutowiredReader;
use WebStream\Annotation\TemplateReader;
use WebStream\Module\Container;

use WebStream\Exception\ClassNotFoundException;
use WebStream\Exception\MethodNotFoundException;
use WebStream\Exception\CsrfException;

/**
 * CoreControllerクラス
 * @author Ryuichi TANAKA.
 * @since 2011/09/11
 * @version 0.4.1
 */
class CoreController extends CoreBase
{
    /** view */
    private $view;
    /** セッション */
    protected $session;
    /** リクエスト */
    protected $request;
    /** レスポンス */
    private $response;

    /**
     * Controllerクラス全体の初期化
     * @param Object DIコンテナ
     */
    final public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->request  = $container->request;
        $this->response = $container->response;
        $this->session  = $container->session;
        $this->view = $this->__getView();
    }

    /**
     * Controller起動時の初期処理
     * @param string メソッド名
     * @param array 引数
     * @param object コンテナオブジェクト
     */
    final public function __callInitialize($action, $params, Container $container)
    {
        $refClass = new \ReflectionClass($this);
        // autowired
        $autowired = new AutowiredReader();
        $self = $autowired->read($refClass, null, $container);

        // filter
        $reader = new FilterReader();
        $reader->setReceiver($self);
        $filterComponent = $reader->read($refClass);

        // initialize filter
        $filterComponent->initialize();
        // before filter
        $filterComponent->before();

        // action
        $template = new TemplateReader();
        $template->setTemplateDir($self->__pageName);
        $templateInfo = $template->read($refClass, $action);

        if (!method_exists($self, $action)) {
            $class = $this->__toString($self);
            throw new MethodNotFoundException("${class}#${action} is not defined.");
        }

        $data = $self->{$action}($params);
        if ($data === null) {
            $data = [];
        }
        if (!empty($templateInfo["embed"])) {
            $data = array_merge($data, $templateInfo["embed"]);
        }

        // draw template
        $self->view->draw($templateInfo["base"], $data);
        // after filter
        $filterComponent->after();

        $cachefile = STREAM_ROOT . "/" . STREAM_APP_DIR . "/views/" . STREAM_VIEW_CACHE . "/" .
                     STREAM_CACHE_PREFIX . $this->camel2snake($this->__pageName) . "-" . $this->camel2snake($action);
        $self->view->cache($cachefile);
    }

    /**
     * Controllerで使用する処理の初期化
     * @Inject
     * @Filter("Initialize")
     */
    final public function __initialize()
    {
        $this->__csrfCheck();
        $this->__load();
    }

    /**
     * CSRFトークンをチェックする
     */
    final private function __csrfCheck()
    {
        $token = $this->getCsrfTokenKey();
        $session_token = $this->session->get($token);
        $request_token = null;
        $isExistParams = false;

        // セッションにCSRFトークンがセットされている場合、チェックを実行する
        if (isset($session_token)) {
            // CSRFトークンはワンタイムなので削除する
            $this->session->delete($token);
            if ($this->request->isPost()) {
                $request_token = $this->request->post($token);
                $isExistParams = count($this->request->post()) >= 1;
            } elseif ($this->request->isGet()) {
                $request_token = $this->request->get($token);
                $isExistParams = count($this->request->get()) >= 1;
            }
            // POSTパラメータが存在し、かつ、CSRFトークンが一致しない場合はCSRFエラーとする
            if ($session_token !== $request_token && $isExistParams) {
                throw new CsrfException("Sent invalid CSRF token");
            }
        }
    }

    /**
     * Serviceクラスのインスタンスをロードする
     * @param String Serviceクラス名
     */
    final private function __load()
    {
        // Serviceクラスインスタンスを取得
        $service = $this->__getService();
        // Modelクラスインスタンスを取得
        $model = $this->__getModel();

        if ($service) {
            $this->{$this->__pageName} = $service;
        } elseif ($model) {
            $this->{$this->__pageName} = $model;
        } else {
            $serviceClass = $this->__page() . 'Service';
            $modelClass = $this->__page() . 'Model';
            $errorMsg = "$serviceClass and $modelClass is not defined.";
            $this->{$this->__pageName} = new ClassNotFoundException($errorMsg);
        }
    }
}
