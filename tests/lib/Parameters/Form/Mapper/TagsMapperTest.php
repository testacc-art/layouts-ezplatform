<?php

namespace Netgen\BlockManager\Ez\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Ez\Parameters\Form\Mapper\TagsMapper;
use Netgen\BlockManager\Ez\Parameters\ParameterType\TagsType as ParameterType;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType;
use Netgen\TagsBundle\API\Repository\TagsService;
use PHPUnit\Framework\TestCase;

final class TagsMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Ez\Parameters\Form\Mapper\TagsMapper
     */
    private $mapper;

    public function setUp()
    {
        $this->mapper = new TagsMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\Form\Mapper\TagsMapper::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(ContentBrowserMultipleType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\Form\Mapper\TagsMapper::mapOptions
     */
    public function testMapOptions()
    {
        $this->assertEquals(
            array(
                'item_type' => 'eztags',
                'min' => 3,
                'max' => 6,
            ),
            $this->mapper->mapOptions(
                new Parameter(
                    array(
                        'type' => new ParameterType($this->createMock(TagsService::class)),
                        'options' => array(
                            'min' => 3,
                            'max' => 6,
                        ),
                    )
                )
            )
        );
    }
}
