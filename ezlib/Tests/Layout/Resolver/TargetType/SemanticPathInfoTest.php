<?php

namespace Netgen\BlockManager\Ez\Tests\Layout\Resolver\TargetType;

use Netgen\BlockManager\Ez\Layout\Resolver\TargetType\SemanticPathInfo;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class SemanticPathInfoTest extends TestCase
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    /**
     * @var \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\SemanticPathInfo
     */
    protected $targetType;

    public function setUp()
    {
        $request = Request::create('/the/answer');
        $request->attributes->set('semanticPathinfo', '/the/answer');

        $this->requestStack = new RequestStack();
        $this->requestStack->push($request);

        $this->targetType = new SemanticPathInfo();
        $this->targetType->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\SemanticPathInfo::getType
     */
    public function testGetType()
    {
        self::assertEquals('ez_semantic_path_info', $this->targetType->getType());
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\SemanticPathInfo::getConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $isValid)
    {
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->targetType->getConstraints());
        self::assertEquals($isValid, $errors->count() == 0);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\SemanticPathInfo::provideValue
     */
    public function testProvideValue()
    {
        self::assertEquals(
            '/the/answer',
            $this->targetType->provideValue()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\SemanticPathInfo::provideValue
     */
    public function testProvideValueWithEmptySemanticPathInfo()
    {
        $this->requestStack->getCurrentRequest()->attributes->set('semanticPathinfo', false);

        self::assertEquals(
            '/',
            $this->targetType->provideValue()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\SemanticPathInfo::provideValue
     */
    public function testProvideValueWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        self::assertNull($this->targetType->provideValue());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\SemanticPathInfo::provideValue
     */
    public function testProvideValueWithNoSemanticPathInfo()
    {
        // Make sure we have no semantic path info attribute
        $this->requestStack->getCurrentRequest()->attributes->remove('semanticPathinfo');

        self::assertNull($this->targetType->provideValue());
    }

    /**
     * Provider for testing target type validation.
     *
     * @return array
     */
    public function validationProvider()
    {
        return array(
            array('/some/route', true),
            array('/', true),
            array('', false),
            array(null, false),
        );
    }
}