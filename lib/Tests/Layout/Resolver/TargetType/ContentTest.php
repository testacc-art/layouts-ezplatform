<?php

namespace Netgen\BlockManager\Ez\Tests\Layout\Resolver\TargetType;

use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Values\Content\Content as EzContent;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Content;
use Netgen\BlockManager\Ez\Tests\Validator\RepositoryValidatorFactory;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class ContentTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $repositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentServiceMock;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    /**
     * @var \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Content
     */
    protected $targetType;

    public function setUp()
    {
        $this->contentServiceMock = $this->createMock(ContentService::class);
        $this->repositoryMock = $this->createPartialMock(Repository::class, array('getContentService'));

        $this->repositoryMock
            ->expects($this->any())
            ->method('getContentService')
            ->will($this->returnValue($this->contentServiceMock));

        $view = new ContentView();
        $view->setContent(
            new EzContent(
                array(
                    'versionInfo' => new VersionInfo(
                        array(
                            'contentInfo' => new ContentInfo(
                                array(
                                    'id' => 42,
                                )
                            ),
                        )
                    ),
                )
            )
        );

        $request = Request::create('/');
        $request->attributes->set('view', $view);

        $this->requestStack = new RequestStack();
        $this->requestStack->push($request);

        $this->targetType = new Content();
        $this->targetType->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Content::getType
     */
    public function testGetType()
    {
        $this->assertEquals('ezcontent', $this->targetType->getType());
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Content::getConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $isValid)
    {
        if ($value !== null) {
            $this->contentServiceMock
                ->expects($this->once())
                ->method('loadContentInfo')
                ->with($this->equalTo($value))
                ->will(
                    $this->returnCallback(
                        function () use ($value) {
                            if (!is_int($value) || $value > 20) {
                                throw new NotFoundException('location', $value);
                            }
                        }
                    )
                );
        }

        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new RepositoryValidatorFactory($this->repositoryMock))
            ->getValidator();

        $errors = $validator->validate($value, $this->targetType->getConstraints());
        $this->assertEquals($isValid, $errors->count() == 0);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Content::provideValue
     */
    public function testProvideValue()
    {
        $this->assertEquals(42, $this->targetType->provideValue());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Content::provideValue
     */
    public function testProvideValueWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        $this->assertNull($this->targetType->provideValue());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Children::provideValue
     */
    public function testProvideValueWithNoView()
    {
        // Make sure we have no view attribute
        $this->requestStack->getCurrentRequest()->attributes->remove('view');

        $this->assertNull($this->targetType->provideValue());
    }

    /**
     * Provider for testing valid parameter values.
     *
     * @return array
     */
    public function validationProvider()
    {
        return array(
            array(12, true),
            array(24, false),
            array(-12, false),
            array(0, false),
            array('12', false),
            array('', false),
            array(null, false),
        );
    }
}
