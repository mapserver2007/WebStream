<?php
namespace WebStream\Test;

trait TestConstant
{
    private function getDocumentRootURL()
    {
        return "http://localhost/WebStream/core/test/testdata";
    }

    private function getProjectRootPath()
    {
        return "/Users/stay/workspace2/WebStream";
    }

    private function getSampleAppPath()
    {
        return "/core/WebStream/Test/Sample";
    }

    private function getLogFilePath()
    {
        return $this->getSampleAppPath() . "/log/webstream.test.log";
    }

    private function getLogConfigPath()
    {
        return $this->getSampleAppPath() . "/config/log_config";
    }

    private function getCacheDir777()
    {
        return $this->getSampleAppPath() . "/cache777";
    }
}
