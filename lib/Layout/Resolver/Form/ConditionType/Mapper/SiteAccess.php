<?php

namespace Netgen\BlockManager\Ez\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SiteAccess extends Mapper
{
    /**
     * @var array
     */
    protected $siteAccessList;

    /**
     * Constructor.
     *
     * @param array $siteAccessList
     */
    public function __construct(array $siteAccessList)
    {
        // We want the array to have the same
        // list for keys as well as values
        $this->siteAccessList = array_combine(
            $siteAccessList,
            $siteAccessList
        );
    }

    /**
     * Returns the form type that will be used to edit the value of this condition type.
     *
     * @return string
     */
    public function getFormType()
    {
        return ChoiceType::class;
    }

    /**
     * Returns the form options.
     *
     * @return array
     */
    public function getFormOptions()
    {
        return array(
            'choices' => $this->siteAccessList,
            'choice_translation_domain' => false,
            'choices_as_values' => true,
            'multiple' => true,
            'expanded' => true,
        );
    }
}
