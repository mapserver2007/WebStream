<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Template;

class TestHelperController extends CoreController
{
    /**
     * @Inject
     * @Template("base1.tmpl")
     */
    public function help1()
    {
    }

    /**
     * @Inject
     * @Template("base2.tmpl")
     */
    public function help2()
    {
        $this->TestHelper->setName("μ's");
    }

    /**
     * @Inject
     * @Template("base3.tmpl")
     */
    public function help3()
    {
        $this->TestHelper->setName("LilyWhite");
    }

    /**
     * @Inject
     * @Template("base4.tmpl")
     */
    public function help4()
    {
        $this->TestHelper->setName("BiBi");
    }

    /**
     * @Inject
     * @Template("base5.tmpl")
     */
    public function help5()
    {
    }

    /**
     * @Inject
     * @Template("base6.tmpl")
     */
    public function help6()
    {
        $this->TestHelper->setMap([
            "name" => "honoka",
            "age" => 16
        ]);
    }
}