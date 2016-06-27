<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Parameters\Parameter;

use eZ\Publish\API\Repository\Repository;
use Netgen\Bundle\EzPublishBlockManagerBundle\Validator\LocationValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactory;

class ValidatorFactory extends ConstraintValidatorFactory
{
    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    protected $repository;

    /**
     * Constructor.
     *
     * @param \eZ\Publish\API\Repository\Repository $repository
     */
    public function __construct(Repository $repository)
    {
        parent::__construct();

        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function getInstance(Constraint $constraint)
    {
        $name = $constraint->validatedBy();

        if ($name === 'ngbm_ezlocation') {
            return new LocationValidator($this->repository);
        } else {
            return parent::getInstance($constraint);
        }
    }
}
