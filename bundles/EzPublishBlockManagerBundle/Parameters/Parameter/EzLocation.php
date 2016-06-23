<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Parameters\Parameter;

use Netgen\BlockManager\Parameters\Parameter;
use Symfony\Component\Validator\Constraints;
use Netgen\Bundle\EzPublishBlockManagerBundle\Validator\Constraint\EzLocation as EzLocationConstraint;

class EzLocation extends Parameter
{
    /**
     * Returns the parameter type.
     *
     * @return string
     */
    public function getType()
    {
        return 'ezlocation';
    }

    /**
     * Returns constraints that are specific to parameter.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getParameterConstraints()
    {
        $constraints = array(
            new Constraints\Type(array('type' => 'numeric')),
            new Constraints\GreaterThan(array('value' => 0)),
            new EzLocationConstraint(),
        );

        return $constraints;
    }
}
