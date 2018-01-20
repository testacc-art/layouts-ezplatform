<?php

namespace Netgen\BlockManager\Ez\Tests\Item\ValueLoader;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Repository\Values\Content\Location;
use Netgen\BlockManager\Exception\Item\ItemException;
use Netgen\BlockManager\Ez\Item\ValueLoader\LocationValueLoader;
use PHPUnit\Framework\TestCase;

final class LocationValueLoaderTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $locationServiceMock;

    /**
     * @var \Netgen\BlockManager\Ez\Item\ValueLoader\LocationValueLoader
     */
    private $valueLoader;

    public function setUp()
    {
        $this->locationServiceMock = $this->createMock(LocationService::class);

        $this->valueLoader = new LocationValueLoader($this->locationServiceMock);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\LocationValueLoader::__construct
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\LocationValueLoader::load
     */
    public function testLoad()
    {
        $location = new Location(
            array(
                'id' => 52,
                'contentInfo' => new ContentInfo(
                    array(
                        'published' => true,
                    )
                ),
            )
        );

        $this->locationServiceMock
            ->expects($this->any())
            ->method('loadLocation')
            ->with($this->isType('int'))
            ->will($this->returnValue($location));

        $this->assertEquals($location, $this->valueLoader->load(52));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\LocationValueLoader::load
     * @expectedException \Netgen\BlockManager\Exception\Item\ItemException
     * @expectedExceptionMessage Location with ID "52" could not be loaded.
     */
    public function testLoadThrowsItemException()
    {
        $this->locationServiceMock
            ->expects($this->any())
            ->method('loadLocation')
            ->with($this->isType('int'))
            ->will($this->throwException(new ItemException()));

        $this->valueLoader->load(52);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\LocationValueLoader::load
     * @expectedException \Netgen\BlockManager\Exception\Item\ItemException
     * @expectedExceptionMessage Location with ID "52" has unpublished content and cannot be loaded.
     */
    public function testLoadThrowsItemExceptionWithNonPublishedContent()
    {
        $this->locationServiceMock
            ->expects($this->any())
            ->method('loadLocation')
            ->with($this->isType('int'))
            ->will(
                $this->returnValue(
                    new Location(
                        array(
                            'contentInfo' => new ContentInfo(
                                array(
                                    'published' => false,
                                )
                            ),
                        )
                    )
                )
            );

        $this->valueLoader->load(52);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\LocationValueLoader::loadByRemoteId
     */
    public function testLoadByRemoteId()
    {
        $location = new Location(
            array(
                'remoteId' => 'abc',
                'contentInfo' => new ContentInfo(
                    array(
                        'published' => true,
                    )
                ),
            )
        );

        $this->locationServiceMock
            ->expects($this->any())
            ->method('loadLocationByRemoteId')
            ->with($this->isType('string'))
            ->will($this->returnValue($location));

        $this->assertEquals($location, $this->valueLoader->loadByRemoteId('abc'));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\LocationValueLoader::loadByRemoteId
     * @expectedException \Netgen\BlockManager\Exception\Item\ItemException
     * @expectedExceptionMessage Location with remote ID "abc" could not be loaded.
     */
    public function testLoadByRemoteIdThrowsItemException()
    {
        $this->locationServiceMock
            ->expects($this->any())
            ->method('loadLocationByRemoteId')
            ->with($this->isType('string'))
            ->will($this->throwException(new ItemException()));

        $this->valueLoader->loadByRemoteId('abc');
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\LocationValueLoader::loadByRemoteId
     * @expectedException \Netgen\BlockManager\Exception\Item\ItemException
     * @expectedExceptionMessage Location with remote ID "abc" has unpublished content and cannot be loaded.
     */
    public function testLoadByRemoteIdThrowsItemExceptionWithNonPublishedContent()
    {
        $this->locationServiceMock
            ->expects($this->any())
            ->method('loadLocationByRemoteId')
            ->with($this->isType('string'))
            ->will(
                $this->returnValue(
                    new Location(
                        array(
                            'contentInfo' => new ContentInfo(
                                array(
                                    'published' => false,
                                )
                            ),
                        )
                    )
                )
            );

        $this->valueLoader->loadByRemoteId('abc');
    }
}
