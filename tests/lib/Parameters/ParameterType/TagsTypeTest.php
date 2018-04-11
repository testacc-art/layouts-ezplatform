<?php

namespace Netgen\BlockManager\Ez\Tests\Parameters\ParameterType;

use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use Netgen\BlockManager\Ez\Parameters\ParameterType\TagsType;
use Netgen\BlockManager\Ez\Tests\Validator\TagsServiceValidatorFactory;
use Netgen\BlockManager\Tests\Parameters\ParameterType\ParameterTypeTestTrait;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;
use Netgen\TagsBundle\Core\Repository\TagsService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class TagsTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $tagsServiceMock;

    public function setUp()
    {
        $this->tagsServiceMock = $this->createPartialMock(TagsService::class, array('loadTag', 'loadTagByRemoteId'));

        $this->type = new TagsType($this->tagsServiceMock);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\TagsType::__construct
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\TagsType::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('eztags', $this->type->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\TagsType::configureOptions
     * @dataProvider validOptionsProvider
     *
     * @param array $options
     * @param array $resolvedOptions
     */
    public function testValidOptions($options, $resolvedOptions)
    {
        $parameter = $this->getParameterDefinition($options);
        $this->assertEquals($resolvedOptions, $parameter->getOptions());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\TagsType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidArgumentException
     * @dataProvider invalidOptionsProvider
     *
     * @param array $options
     */
    public function testInvalidOptions($options)
    {
        $this->getParameterDefinition($options);
    }

    /**
     * Provider for testing valid parameter attributes.
     *
     * @return array
     */
    public function validOptionsProvider()
    {
        return array(
            array(
                array(
                ),
                array(
                    'max' => null,
                    'min' => null,
                    'allow_invalid' => false,
                ),
            ),
            array(
                array(
                    'max' => 5,
                ),
                array(
                    'max' => 5,
                    'min' => null,
                    'allow_invalid' => false,
                ),
            ),
            array(
                array(
                    'max' => null,
                ),
                array(
                    'max' => null,
                    'min' => null,
                    'allow_invalid' => false,
                ),
            ),
            array(
                array(
                    'min' => 5,
                ),
                array(
                    'min' => 5,
                    'max' => null,
                    'allow_invalid' => false,
                ),
            ),
            array(
                array(
                    'min' => null,
                ),
                array(
                    'max' => null,
                    'min' => null,
                    'allow_invalid' => false,
                ),
            ),
            array(
                array(
                    'min' => 5,
                    'max' => 10,
                    'allow_invalid' => false,
                ),
                array(
                    'min' => 5,
                    'max' => 10,
                    'allow_invalid' => false,
                ),
            ),
            array(
                array(
                    'min' => 5,
                    'max' => 3,
                ),
                array(
                    'min' => 5,
                    'max' => 5,
                    'allow_invalid' => false,
                ),
            ),
            array(
                array(
                    'allow_invalid' => false,
                ),
                array(
                    'min' => null,
                    'max' => null,
                    'allow_invalid' => false,
                ),
            ),
            array(
                array(
                    'allow_invalid' => true,
                ),
                array(
                    'min' => null,
                    'max' => null,
                    'allow_invalid' => true,
                ),
            ),
        );
    }

    /**
     * Provider for testing invalid parameter attributes.
     *
     * @return array
     */
    public function invalidOptionsProvider()
    {
        return array(
            array(
                array(
                    'min' => '0',
                ),
                array(
                    'min' => -5,
                ),
                array(
                    'min' => 0,
                ),
                array(
                    'max' => '0',
                ),
                array(
                    'max' => -5,
                ),
                array(
                    'max' => 0,
                ),
                array(
                    'allow_invalid' => 'false',
                ),
                array(
                    'allow_invalid' => 'true',
                ),
                array(
                    'allow_invalid' => 0,
                ),
                array(
                    'allow_invalid' => 1,
                ),
                array(
                    'undefined_value' => 'Value',
                ),
            ),
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\TagsType::export
     */
    public function testExport()
    {
        $this->tagsServiceMock
            ->expects($this->once())
            ->method('loadTag')
            ->with($this->equalTo(42))
            ->will($this->returnValue(new Tag(array('remoteId' => 'abc'))));

        $this->assertEquals('abc', $this->type->export($this->getParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\TagsType::export
     */
    public function testExportWithNonExistingTag()
    {
        $this->tagsServiceMock
            ->expects($this->once())
            ->method('loadTag')
            ->with($this->equalTo(42))
            ->will($this->throwException(new NotFoundException('tag', 42)));

        $this->assertNull($this->type->export($this->getParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\TagsType::import
     */
    public function testImport()
    {
        $this->tagsServiceMock
            ->expects($this->once())
            ->method('loadTagByRemoteId')
            ->with($this->equalTo('abc'))
            ->will($this->returnValue(new Tag(array('id' => 42))));

        $this->assertEquals(42, $this->type->import($this->getParameterDefinition(), 'abc'));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\TagsType::import
     */
    public function testImportWithNonExistingTag()
    {
        $this->tagsServiceMock
            ->expects($this->once())
            ->method('loadTagByRemoteId')
            ->with($this->equalTo('abc'))
            ->will($this->throwException(new NotFoundException('tag', 'abc')));

        $this->assertNull($this->type->import($this->getParameterDefinition(), 'abc'));
    }

    /**
     * @param mixed $values
     * @param bool $required
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\TagsType::getValueConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($values, $required, $isValid)
    {
        if ($values !== null) {
            foreach ($values as $i => $value) {
                if ($value !== null) {
                    $this->tagsServiceMock
                        ->expects($this->at($i))
                        ->method('loadTag')
                        ->with($this->equalTo($value))
                        ->will(
                            $this->returnCallback(
                                function () use ($value) {
                                    if (!is_int($value) || $value > 20) {
                                        throw new NotFoundException('tag', $value);
                                    }
                                }
                            )
                        );
                }
            }
        }

        $parameter = $this->getParameterDefinition(array('min' => 1, 'max' => 3), $required);
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new TagsServiceValidatorFactory($this->tagsServiceMock))
            ->getValidator();

        $errors = $validator->validate($values, $this->type->getConstraints($parameter, $values));
        $this->assertEquals($isValid, $errors->count() === 0);
    }

    /**
     * Provider for testing valid parameter values.
     *
     * @return array
     */
    public function validationProvider()
    {
        return array(
            array(array(12), false, true),
            array(array(12, 13, 14, 15), false, false),
            array(array(24), false, false),
            array(array(-12), false, false),
            array(array(0), false, false),
            array(array('12'), false, false),
            array(array(''), false, false),
            array(array(null), false, false),
            array(array(), false, false),
            array(null, false, true),
            array(array(12), true, true),
            array(array(12, 13, 14, 15), true, false),
            array(array(24), true, false),
            array(array(-12), true, false),
            array(array(0), true, false),
            array(array('12'), true, false),
            array(array(''), true, false),
            array(array(null), true, false),
            array(array(), true, false),
            array(null, true, false),
        );
    }
}
