<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Layout\Resolver\RuleHandler\Doctrine\TargetHandler;

use Netgen\BlockManager\Tests\Layout\Resolver\RuleHandler\Doctrine\TargetHandler\TargetHandlerTest;
use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\RuleHandler\Doctrine\TargetHandler\SemanticPathInfoPrefix;

class SemanticPathInfoPrefixTest extends TargetHandlerTest
{
    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\RuleHandler\Doctrine\TargetHandler\PathInfoPrefix::handleQuery
     */
    public function testLoadRules()
    {
        $expected = array(
            array(
                'layout_id' => 21,
                'conditions' => array(),
            ),
        );

        self::assertEquals($expected, $this->handler->loadRules($this->getTargetIdentifier(), array('/the/answer')));
    }

    /**
     * Returns the target identifier under test.
     *
     * @return \Netgen\BlockManager\Layout\Resolver\RuleHandler\Doctrine\TargetHandler
     */
    protected function getTargetIdentifier()
    {
        return 'semantic_path_info_prefix';
    }

    /**
     * Creates the handler under test.
     *
     * @return \Netgen\BlockManager\Layout\Resolver\RuleHandler\Doctrine\TargetHandler
     */
    protected function getTargetHandler()
    {
        return new SemanticPathInfoPrefix();
    }
}
