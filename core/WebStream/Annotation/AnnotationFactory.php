<?php
namespace WebStream\Annotation;

use WebStream\Module\ClassLoader;

/**
 * AnnotationFactory
 * @author Ryuichi TANAKA.
 * @since 2013/09/17
 * @version 0.4
 */
abstract class AnnotationFactory
{
    /** クラスローダ */
    protected $classLoader;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->classLoader = new ClassLoader();
        $this->classLoad();
    }

    /**
     * インスタンスを返却する
     * @param string クラスパス
     * @param array 引数リスト
     * @return object インスタンス
     */
    public function create($classpath, $arguments = [])
    {
        return $this->createInstance($classpath, $arguments);
    }

    /**
     * インスタンスを返却する抽象メソッド
     * @param string クラスパス
     * @param array 引数リスト
     */
    abstract protected function createInstance($classpath, $arguments);

    /**
     * クラスローダを実行する抽象メソッド
     */
    abstract protected function classLoad();
}
