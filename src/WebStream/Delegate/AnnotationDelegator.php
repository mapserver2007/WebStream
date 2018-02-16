<?php
namespace WebStream\Delegate;

use WebStream\Core\CoreInterface;
use WebStream\Core\CoreController;
use WebStream\Core\CoreService;
use WebStream\Core\CoreModel;
use WebStream\Core\CoreView;
use WebStream\Core\CoreHelper;
use WebStream\Container\Container;
use WebStream\Annotation\Attributes\Alias;
use WebStream\Annotation\Attributes\Database;
use WebStream\Annotation\Attributes\ExceptionHandler;
use WebStream\Annotation\Attributes\Filter;
use WebStream\Annotation\Attributes\Header;
use WebStream\Annotation\Attributes\Query;
use WebStream\Annotation\Attributes\Template;
use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Reader\AnnotationReader;
use WebStream\Annotation\Reader\Extend\FilterExtendReader;
use WebStream\Annotation\Reader\Extend\QueryExtendReader;
use WebStream\Annotation\Container\AnnotationContainer;
use WebStream\Template\Basic;
use WebStream\Template\Twig;

/**
 * AnnotationDelegator
 * @author Ryuichi TANAKA.
 * @since 2015/02/11
 * @version 0.4
 */
class AnnotationDelegator
{
    /**
     * @var Container コンテナ
     */
    private $container;

    /**
     * @var Logger ロガー
     */
    private $logger;

    /**
     * Constructor
     * @param CoreInterface インスタンス
     * @param Container DIContainer
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->logger = $container->logger;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->logger->debug("AnnotationDelegator container is clear.");
    }

    /**
     * アノテーション情報をロードする
     * @param object インスタンス
     * @param string メソッド
     * @param string アノテーションクラスパス
     * @return Container コンテナ
     */
    public function read($instance, $method = null, $classpath = null)
    {
        if (!$instance instanceof IAnnotatable) {
            $this->logger->warn("Annotation is not available this class: " . get_class($instance));
            return;
        }

        $this->container->executeMethod = $method ?: "";

        if ($instance instanceof CoreController) {
            return $this->readController($instance, $classpath);
        } elseif ($instance instanceof CoreService) {
            return $this->readService($instance, $classpath);
        } elseif ($instance instanceof CoreModel) {
            return $this->readModel($instance, $classpath);
        } elseif ($instance instanceof CoreView) {
            return $this->readView($instance, $classpath);
        } elseif ($instance instanceof CoreHelper) {
            return $this->readHelper($instance, $classpath);
        } else {
            return $this->readModule($instance, $classpath);
        }
    }

    /**
     * Controllerのアノテーション情報をロードする
     * @param CoreController インスタンス
     * @param string アノテーションクラスパス
     * @return Container コンテナ
     */
    private function readController(CoreController $instance, $classpath)
    {
        $reader = new AnnotationReader($instance);
        $reader->setActionMethod($this->container->executeMethod);

        // @Header
        $container = new Container();
        $container->requestMethod = $this->container->request->requestMethod;
        $container->contentType = 'html';
        $container->logger = $this->container->logger;
        $reader->readable(Header::class, $container);

        // @Filter
        $container = new Container();
        $container->action = $this->container->executeMethod;
        $container->logger = $this->container->logger;
        $reader->readable(Filter::class, $container);
        $reader->useExtendReader(Filter::class, FilterExtendReader::class);

        // @Template
        $container = new Container();
        $container->action = $this->container->executeMethod;
        $container->engine = [
            'basic' => Basic::class,
            'twig' => Twig::class
        ];
        $container->logger = $this->container->logger;
        $reader->readable(Template::class, $container);

        // @ExceptionHandler
        $container = new Container();
        $container->logger = $this->container->logger;
        $reader->readable(ExceptionHandler::class, $container);

        // @Alias
        $container = new Container();
        $container->action = $this->container->executeMethod;
        $container->logger = $this->container->logger;
        $reader->readable(Alias::class, $container);

        $reader->readMethod();

        $annotationContainer = new AnnotationContainer();
        $annotationContainer->annotationInfoList = $reader->getAnnotationInfoList();
        $annotationContainer->exception = $reader->getException();
        $annotationContainer->customAnnotationInfoList = array_filter($annotationContainer->annotationInfoList, function ($key) {
            return !in_array($key, [Filter::class, Header::class, ExceptionHandler::class, Template::class, Alias::class], true);
        }, ARRAY_FILTER_USE_KEY);

        return $annotationContainer;
    }

    /**
     * Serviceのアノテーション情報をロードする
     * @param CoreService インスタンス
     * @param string アノテーションクラスパス
     * @return Container コンテナ
     */
    private function readService(CoreService $instance, $classpath)
    {
        $reader = new AnnotationReader($instance);
        $reader->setActionMethod($this->container->executeMethod);

        // @Filter
        $container = new Container();
        $container->action = $this->container->executeMethod;
        $container->logger = $this->container->logger;
        $reader->readable(Filter::class, $container);
        $reader->useExtendReader(Filter::class, FilterExtendReader::class);

        // @ExceptionHandler
        $container = new Container();
        $container->logger = $this->container->logger;
        $reader->readable(ExceptionHandler::class, $container);

        // @Alias
        $container = new Container();
        $container->action = $this->container->executeMethod;
        $container->logger = $this->container->logger;
        $reader->readable(Alias::class, $container);

        $reader->readMethod();

        $annotationContainer = new AnnotationContainer();
        $annotationContainer->annotationInfoList = $reader->getAnnotationInfoList();
        $annotationContainer->exception = $reader->getException();
        $annotationContainer->customAnnotationInfoList = array_filter($annotationContainer->annotationInfoList, function ($key) {
            return !in_array($key, [Filter::class, ExceptionHandler::class, Alias::class], true);
        }, ARRAY_FILTER_USE_KEY);


        return $annotationContainer;
    }

    /**
     * Modelのアノテーション情報をロードする
     * @param CoreModel インスタンス
     * @param string アノテーションクラスパス
     * @return Container コンテナ
     */
    private function readModel(CoreModel $instance, $classpath)
    {
        $reader = new AnnotationReader($instance);
        $reader->setActionMethod($this->container->executeMethod);

        // @Filter
        $container = new Container();
        $container->action = $this->container->executeMethod;
        $container->logger = $this->container->logger;
        $reader->readable(Filter::class, $container);
        $reader->useExtendReader(Filter::class, FilterExtendReader::class);

        // @ExceptionHandler
        $container = new Container();
        $container->logger = $this->container->logger;
        $reader->readable(ExceptionHandler::class, $container);

        // @Database
        $container = new Container();
        $container->rootPath = $this->container->applicationInfo->applicationRoot;
        $container->logger = $this->container->logger;
        $reader->readable(Database::class, $container);

        // @Query
        $container = new Container();
        $container->rootPath = $this->container->applicationInfo->applicationRoot;
        $container->logger = $this->container->logger;
        $reader->readable(Query::class, $container);
        $reader->useExtendReader(Query::class, QueryExtendReader::class);

        // @Alias
        $container = new Container();
        $container->action = $this->container->executeMethod;
        $container->logger = $this->container->logger;
        $reader->readable(Alias::class, $container);

        $reader->readClass();
        $reader->readMethod();

        $annotationContainer = new AnnotationContainer();
        $annotationContainer->annotationInfoList = $reader->getAnnotationInfoList();
        $annotationContainer->exception = $reader->getException();
        $annotationContainer->customAnnotationInfoList = array_filter($annotationContainer->annotationInfoList, function ($key) {
            return !in_array($key, [Filter::class, ExceptionHandler::class, Database::class, Query::class, Alias::class], true);
        }, ARRAY_FILTER_USE_KEY);

        return $annotationContainer;
    }

    /**
     * Viewのアノテーション情報をロードする
     * @param CoreView インスタンス
     * @param string アノテーションクラスパス
     * @return Container コンテナ
     */
    private function readView(CoreView $instance, $classpath)
    {
        $reader = new AnnotationReader($instance);

        // @Filter
        $container = new Container();
        $container->action = $this->container->executeMethod;
        $container->logger = $this->container->logger;
        $reader->readable(Filter::class, $container);
        $reader->useExtendReader(Filter::class, FilterExtendReader::class);

        $reader->readMethod();

        $annotationContainer = new AnnotationContainer();
        $annotationContainer->annotationInfoList = $reader->getAnnotationInfoList();
        $annotationContainer->exception = $reader->getException();

        return $annotationContainer;
    }

    /**
     * Helperのアノテーション情報をロードする
     * @param CoreHelper インスタンス
     * @param string アノテーションクラスパス
     * @return Container コンテナ
     */
    private function readHelper(CoreHelper $instance, $classpath)
    {
        $reader = new AnnotationReader($instance);
        $reader->setActionMethod($this->container->executeMethod);

        // @Filter
        $container = new Container();
        $container->action = $this->container->executeMethod;
        $container->logger = $this->container->logger;
        $reader->readable(Filter::class, $container);
        $reader->useExtendReader(Filter::class, FilterExtendReader::class);

        // @ExceptionHandler
        $container = new Container();
        $container->logger = $this->container->logger;
        $reader->readable(ExceptionHandler::class, $container);

        // @Alias
        $container = new Container();
        $container->action = $this->container->executeMethod;
        $container->logger = $this->container->logger;
        $reader->readable(Alias::class, $container);

        $reader->readMethod();

        $annotationContainer = new AnnotationContainer();
        $annotationContainer->annotationInfoList = $reader->getAnnotationInfoList();
        $annotationContainer->exception = $reader->getException();
        $annotationContainer->customAnnotationInfoList = array_filter($annotationContainer->annotationInfoList, function ($key) {
            return !in_array($key, [Filter::class, ExceptionHandler::class, Alias::class], true);
        }, ARRAY_FILTER_USE_KEY);

        return $annotationContainer;
    }

    /**
     * 他のモジュールのアノテーション情報をロードする
     * @param object インスタンス
     * @param string アノテーションクラスパス
     * @return Container コンテナ
     */
    private function readModule(IAnnotatable $instance, $classpath)
    {
        $container = $this->container;
        $reader = new AnnotationReader($instance, $container);
        $reader->read($classpath);
        $injectedAnnotation = $reader->getInjectedAnnotationInfo();

        $factory = new AnnotationDelegatorFactory($injectedAnnotation, $container);
        $annotationContainer = new AnnotationContainer();

        // @Filter
        $annotationContainer->filter = $factory->createAnnotationCallable("filter");

        // @Alias
        $annotationContainer->alias = $factory->createAnnotationCallable("alias");

        // custom annotation
        $annotationContainer->customAnnotations = $factory->createCustomAnnotationCallable();

        return $annotationContainer;
    }
}
