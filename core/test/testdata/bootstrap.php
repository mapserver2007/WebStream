<?php
namespace WebStream;

require_once '../../../core/WebStream/Module/ClassLoader.php';
//require '../../../core/AutoImport.php';
require_once '../../../core/Functions.php';

function __autoload($class_name) {
    import("core/test/testdata/config/" . $class_name);
}

register_shutdown_function('WebStream\shutdownHandler');

// core以下のファイル、ルーティングルール、バリデーションルールをロード
importAll("core");
import("core/test/testdata/config/routes");
import("core/test/testdata/config/validates");

// ログ出力ディレクトリ、ログレベルをテスト用に変更
Logger::init("core/test/testdata/config/log.ini");

// サービスロケータをロード
$container = ServiceLocator::getContainer();
$controller_test_dir = "core/test/testdata/app";
$class = new \ReflectionClass("WebStream\Application");
$instance = $class->newInstance($container);
$property = $class->getProperty("app_dir");
$property->setAccessible(true);
$property->setValue($instance, $controller_test_dir);
$method = $class->getMethod("run");
$method->invoke($instance);
