<?php
namespace WebStream\Delegate;

use WebStream\Core\CoreView;
use WebStream\Module\Utility;
use WebStream\Module\Logger;
use WebStream\Module\Container;
use WebStream\Module\ClassLoader;
use WebStream\Module\FileSearchIterator;
use WebStream\Annotation\DatabaseReader;

/**
 * CoreDelegator
 * @author Ryuichi TANAKA.
 * @since 2011/11/30
 * @version 0.4
 */
class CoreDelegator
{
    use Utility
    {
        Utility::getNamespace as getDefinedNamespace;
    }

    /** DIコンテナ */
    private $container;

    /** CoreContainer */
    private $coreContainer;

    /** Routerオブジェクト */
    private $router;

    /**
     * Constructor
     * @param object DIContainer
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->coreContainer = new Container();
        $this->router = $container->router;
        $this->initialize();
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->coreContainer->remove("view");
        $this->coreContainer->remove("service");
        $this->coreContainer->remove("model");
        $this->coreContainer->remove("helper");
        Logger::debug("CoreDelegator container is clear.");
    }

    /**
     * 各レイヤのオブジェクトをコンテナに設定する
     */
    private function initialize()
    {
        $classLoader = new ClassLoader();
        $container = $this->container;
        $pageName = $this->getPageName();
        $serviceClassName = $pageName . "Service";
        $modelClassName   = $pageName . "Model";
        $helperClassName  = $pageName . "Helper";
        $serviceNamespace = $this->getNamespace($serviceClassName);
        $modelNamespace   = $this->getNamespace($modelClassName);
        $helperNamespace  = $this->getNamespace($helperClassName);

        // View
        $this->coreContainer->view = function() use (&$container) {
            return new CoreView($container);
        };
        // Service
        if ($serviceNamespace !== null) {
            $serviceClassPath = $serviceNamespace . "\\" . $serviceClassName;
            $this->coreContainer->service = function() use (&$container, &$classLoader, &$serviceClassPath, &$serviceClassName) {
                if ($classLoader->import(STREAM_APP_DIR . "/services/" . $serviceClassName . ".php")) {
                    return new $serviceClassPath($container);
                }
            };
        } else {
            $this->coreContainer->service = function() {};
        }

        // Model
        if ($modelNamespace !== null) {
            $modelClassPath = $modelNamespace . "\\" . $modelClassName;
            $this->coreContainer->model = function() use (&$container, &$classLoader, &$modelClassPath, &$modelClassName) {
                if ($classLoader->import(STREAM_APP_DIR . "/models/" . $modelClassName . ".php")) {
                    $refClass = new \ReflectionClass($modelClassPath);
                    $reader = new DatabaseReader();
                    $reader->read($refClass, null, $container);

                    return $reader->getInstance();
                }
            };
        } else {
            $this->coreContainer->model = function() {};
        }

        // Helper
        if ($helperNamespace !== null) {
            $helperClassPath = $helperNamespace . "\\" . $helperClassName;
            $this->coreContainer->helper = function() use (&$container, &$classLoader, &$helperClassPath, &$helperClassName) {
                if ($classLoader->import(STREAM_APP_DIR . "/helpers/" . $helperClassName . ".php")) {
                    return new $helperClassPath($container);
                }
            };
        } else {
            $this->coreContainer->helper = function() {};
        }
    }

    /**
     * 名前空間を返却する
     * @param string クラス名
     * @return string 名前空間
     */
    public function getNamespace($className)
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(STREAM_APP_ROOT . "/app"),
            \RecursiveIteratorIterator::LEAVES_ONLY,
            \RecursiveIteratorIterator::CATCH_GET_CHILD // for Permission deny
        );
        foreach ($iterator as $filepath => $fileObject) {
            if (strpos($filepath, $className . ".php") !== false) {
                return $this->getDefinedNamespace($filepath);
            }
        }

        return null;
    }

    /**
     * ページ名を返却する
     * @return string ページ名
     */
    public function getPageName()
    {
        $params = $this->router->routingParams();

        return $this->snake2ucamel($params['controller']);
    }

    /**
     * Viewインスタンスを返却する
     * @return object Viewインスタンス
     */
    public function getView()
    {
        return $this->coreContainer->view;
    }

    /**
     * Serviceインスタンスを返却する
     * @return object Serviceインスタンス
     */
    public function getService()
    {
        return $this->coreContainer->service;
    }

    /**
     * Modelインスタンスを返却する
     * @return object Modelインスタンス
     */
    public function getModel()
    {
        return $this->coreContainer->model;
    }

    /**
     * Helperインスタンスを返却する
     * @return object Helperインスタンス
     */
    public function getHelper()
    {
        return $this->coreContainer->helper;
    }
}
