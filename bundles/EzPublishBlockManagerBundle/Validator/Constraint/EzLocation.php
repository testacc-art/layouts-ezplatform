<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class EzLocation extends Constraint
{
    /**
     * @var string
     */
    public $message = 'netgen_block_manager.ezlocation.location_not_found';

    /**
     * Returns the name of the class that validates this constraint.
     *
     * @return string
     */
    public function validatedBy()
    {
        return 'ngbm_ezlocation';
    }
}
