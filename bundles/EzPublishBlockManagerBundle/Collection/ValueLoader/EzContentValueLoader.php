<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Collection\ValueLoader;

use Netgen\BlockManager\Collection\ValueLoaderInterface;
use Netgen\BlockManager\API\Exception\NotFoundException;
use eZ\Publish\API\Repository\ContentService;
use Exception;

class EzContentValueLoader implements ValueLoaderInterface
{
    /**
     * @var \eZ\Publish\API\Repository\ContentService
     */
    protected $contentService;

    /**
     * Constructor.
     *
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     */
    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    /**
     * Returns the value type this loader loads.
     *
     * @return string
     */
    public function getValueType()
    {
        return 'ezcontent';
    }

    /**
     * Loads the value from provided ID.
     *
     * @param int|string $id
     *
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If value cannot be loaded
     *
     * @return mixed
     */
    public function load($id)
    {
        try {
            return $this->contentService->loadContentInfo($id);
        } catch (Exception $e) {
            throw new NotFoundException('item', $id, $e);
        }
    }
}