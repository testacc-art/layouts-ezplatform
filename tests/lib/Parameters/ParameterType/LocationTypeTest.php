<?php

namespace Netgen\BlockManager\Ez\Tests\Parameters\ParameterType;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Values\Content\Location;
use Netgen\BlockManager\Ez\Parameters\ParameterType\LocationType;
use Netgen\BlockManager\Ez\Tests\Validator\RepositoryValidatorFactory;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Tests\Parameters\ParameterType\ParameterTypeTestTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class LocationTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $repositoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $locationServiceMock;

    public function setUp()
    {
        $this->locationServiceMock = $this->createMock(LocationService::class);
        $this->repositoryMock = $this->createPartialMock(Repository::class, array('sudo', 'getLocationService'));

        $this->repositoryMock
            ->expects($this->any())
            ->method('sudo')
            ->with($this->anything())
            ->will($this->returnCallback(function ($callback) {
                return $callback($this->repositoryMock);
            }));

        $this->repositoryMock
            ->expects($this->any())
            ->method('getLocationService')
            ->will($this->returnValue($this->locationServiceMock));

        $this->type = new LocationType($this->repositoryMock);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\LocationType::__construct
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\LocationType::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('ezlocation', $this->type->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\LocationType::configureOptions
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
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\LocationType::configureOptions
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
                array(),
                array(
                    'allow_invalid' => false,
                ),
            ),
            array(
                array(
                    'allow_invalid' => false,
                ),
                array(
                    'allow_invalid' => false,
                ),
            ),
            array(
                array(
                    'allow_invalid' => true,
                ),
                array(
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
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\LocationType::export
     */
    public function testExport()
    {
        $this->locationServiceMock
            ->expects($this->once())
            ->method('loadLocation')
            ->with($this->equalTo(42))
            ->will($this->returnValue(new Location(array('remoteId' => 'abc'))));

        $this->assertEquals('abc', $this->type->export($this->getParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\LocationType::export
     */
    public function testExportWithNonExistingLocation()
    {
        $this->locationServiceMock
            ->expects($this->once())
            ->method('loadLocation')
            ->with($this->equalTo(42))
            ->will($this->throwException(new NotFoundException('location', 42)));

        $this->assertNull($this->type->export($this->getParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\LocationType::import
     */
    public function testImport()
    {
        $this->locationServiceMock
            ->expects($this->once())
            ->method('loadLocationByRemoteId')
            ->with($this->equalTo('abc'))
            ->will($this->returnValue(new Location(array('id' => 42))));

        $this->assertEquals(42, $this->type->import($this->getParameterDefinition(), 'abc'));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\LocationType::import
     */
    public function testImportWithNonExistingLocation()
    {
        $this->locationServiceMock
            ->expects($this->once())
            ->method('loadLocationByRemoteId')
            ->with($this->equalTo('abc'))
            ->will($this->throwException(new NotFoundException('location', 'abc')));

        $this->assertNull($this->type->import($this->getParameterDefinition(), 'abc'));
    }

    /**
     * @param mixed $value
     * @param bool $required
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\LocationType::getValueConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $required, $isValid)
    {
        if ($value !== null) {
            $this->locationServiceMock
                ->expects($this->once())
                ->method('loadLocation')
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

        $parameter = $this->getParameterDefinition(array(), $required);
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new RepositoryValidatorFactory($this->repositoryMock))
            ->getValidator();

        $errors = $validator->validate($value, $this->type->getConstraints($parameter, $value));
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
            array(12, false, true),
            array(24, false, false),
            array(-12, false, false),
            array(0, false, false),
            array('12', false, false),
            array('', false, false),
            array(null, false, true),
            array(12, true, true),
            array(24, true, false),
            array(-12, true, false),
            array(0, true, false),
            array('12', true, false),
            array('', true, false),
            array(null, true, false),
        );
    }

    /**
     * @param mixed $value
     * @param bool $isEmpty
     *
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\LocationType::isValueEmpty
     * @dataProvider emptyProvider
     */
    public function testIsValueEmpty($value, $isEmpty)
    {
        $this->assertEquals($isEmpty, $this->type->isValueEmpty(new ParameterDefinition(), $value));
    }

    /**
     * Provider for testing if the value is empty.
     *
     * @return array
     */
    public function emptyProvider()
    {
        return array(
            array(null, true),
            array(new Location(), false),
        );
    }
}
