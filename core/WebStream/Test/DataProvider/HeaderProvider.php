<?php
namespace WebStream\Test\DataProvider;

/**
 * HeaderProvider
 * @author Ryuichi TANAKA.
 * @since 2013/10/22
 * @version 0.4
 */
trait HeaderProvider
{
    public function contentTypeProvider()
    {
        return [
            ["/test_header/html", "Content-Type: text/html; charset=UTF-8"],
            ["/test_header/xml",  "Content-Type: application/xml; charset=UTF-8"],
            ["/test_header/atom", "Content-Type: application/atom+xml; charset=UTF-8"],
            ["/test_header/rss",  "Content-Type: application/rss+xml; charset=UTF-8"],
            ["/test_header/rdf",  "Content-Type: application/rdf+xml; charset=UTF-8"]
        ];
    }

    public function allowMethodProvider()
    {
        return [
            ["/test_header/get1", "", "get"],
            ["/test_header/get2", "", "get"],
            ["/test_header/post1", [], "post"],
            ["/test_header/post2", [], "post"]
        ];
    }

    public function notAllowMethodProvider()
    {
        return [
            ["/test_header/get1", [], "post"],
            ["/test_header/get2", [], "post"],
            ["/test_header/post1", "", "get"],
            ["/test_header/post2", "", "get"]
        ];
    }
}
