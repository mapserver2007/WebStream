<?php
namespace WebStream\Annotation;

use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Base\IRead;
use WebStream\Annotation\Base\IClass;
use WebStream\Annotation\Container\AnnotationContainer;
use WebStream\Module\Container;
use WebStream\Log\Logger;
use WebStream\Exception\Extend\DatabaseException;

/**
 * Database
 * @author Ryuichi TANAKA.
 * @since 2013/12/07
 * @version 0.4
 *
 * @Annotation
 * @Target("CLASS")
 */
class Database extends Annotation implements IClass, IRead
{
    /**
     * @var AnnotationContainer アノテーションコンテナ
     */
    private $annotaion;

    /**
     * @var AnnotationContainer 注入結果
     */
    private $injectedContainer;

    /**
     * {@inheritdoc}
     */
    public function onInject(AnnotationContainer $annotation)
    {
        $this->annotation = $annotation;
        $this->injectedContainer = new AnnotationContainer();
        Logger::debug("@Database injected.");
    }

    /**
     * {@inheritdoc}
     */
    public function onInjected()
    {
        return $this->injectedContainer;
    }

    /**
     * {@inheritdoc}
     */
    public function onClassInject(IAnnotatable &$instance, Container $container, \ReflectionClass $class)
    {
        $driver = $this->annotation->driver;
        $config = $this->annotation->config;

        if (!class_exists($driver)) {
            throw new DatabaseException("Database driver is undefined：" . $driver);
        }

        $configPath = STREAM_APP_ROOT . "/" . $config;
        $configRealPath = realpath($configPath);
        if (!file_exists($configRealPath)) {
            throw new DatabaseException("Database config file is not found: " . $configPath);
        }

        $this->injectedContainer->filepath = $class->getFileName();
        $this->injectedContainer->configPath = $configRealPath;
        $this->injectedContainer->driverClassPath = $driver;
    }
}
