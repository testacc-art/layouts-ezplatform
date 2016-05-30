<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Persistence\Doctrine\Handler\LayoutResolver\TargetHandler;

use Netgen\BlockManager\Tests\Persistence\Doctrine\Handler\LayoutResolver\TargetHandler\AbstractTargetHandlerTest;
use Netgen\Bundle\EzPublishBlockManagerBundle\Persistence\Doctrine\QueryHandler\LayoutResolver\TargetHandler\SemanticPathInfo;

class SemanticPathInfoTest extends AbstractTargetHandlerTest
{
    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::matchRules
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::matchRules
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Persistence\Doctrine\QueryHandler\LayoutResolver\TargetHandler\SemanticPathInfo::handleQuery
     */
    public function testMatchRules()
    {
        $rules = $this->handler->matchRules($this->getTargetIdentifier(), '/the/answer');

        self::assertCount(1, $rules);
        self::assertEquals(20, $rules[0]->id);
    }

    /**
     * Returns the target identifier under test.
     *
     * @return string
     */
    protected function getTargetIdentifier()
    {
        return 'semantic_path_info';
    }

    /**
     * Creates the handler under test.
     *
     * @return \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolver\TargetHandler
     */
    protected function getTargetHandler()
    {
        return new SemanticPathInfo();
    }
}