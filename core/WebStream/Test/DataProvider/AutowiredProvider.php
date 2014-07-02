<?php
namespace WebStream\Test\DataProvider;

/**
 * AutowiredProvider
 * @author Ryuichi TANAKA.
 * @since 2013/09/18
 * @version 0.4.1
 */
trait AutowiredProvider
{
    public function autowiredProvider()
    {
        return [
            ["kotori@lovelive.com", 17]
        ];
    }

    public function autowiredForConstantValueProvider()
    {
        return [
            ["honoka", 9]
        ];
    }
}