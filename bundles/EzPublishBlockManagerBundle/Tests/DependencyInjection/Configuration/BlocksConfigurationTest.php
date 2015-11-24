<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\DependencyInjection\Configuration;

class BlocksConfigurationTest extends ConfigurationTest
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilderClosure
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPreProcessor
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPostProcessor
     */
    public function testBlockSettings()
    {
        $config = array(
            array(
                'blocks' => array(
                    'block' => array(
                        'name' => 'block',
                        'view_types' => array(
                            'default' => array(
                                'name' => 'Default',
                            ),
                            'large' => array(
                                'name' => 'Large',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'blocks' => array(
                'block' => array(
                    'name' => 'block',
                    'view_types' => array(
                        'default' => array(
                            'name' => 'Default',
                        ),
                        'large' => array(
                            'name' => 'Large',
                        ),
                    ),
                ),
            )
        );

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilderClosure
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPreProcessor
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPostProcessor
     */
    public function testBlockSettingsWithSystemNode()
    {
        $config = array(
            array(
                'blocks' => array(
                    'block' => array(
                        'name' => 'block',
                        'view_types' => array(
                            'default' => array(
                                'name' => 'Default',
                            ),
                            'large' => array(
                                'name' => 'Large',
                            ),
                        ),
                    ),
                ),
                'system' => array(
                    'default' => array(
                        'blocks' => array(
                            'other_block' => array(
                                'name' => 'other_block',
                                'view_types' => array(
                                    'small' => array(
                                        'name' => 'Small',
                                    ),
                                ),
                            ),
                        ),
                    )
                )
            ),
        );

        $expectedConfig = array(
            'blocks' => array(
                'block' => array(
                    'name' => 'block',
                    'view_types' => array(
                        'default' => array(
                            'name' => 'Default',
                        ),
                        'large' => array(
                            'name' => 'Large',
                        ),
                    ),
                ),
                'other_block' => array(
                    'name' => 'other_block',
                    'view_types' => array(
                        'small' => array(
                            'name' => 'Small',
                        ),
                    ),
                ),
            )
        );

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        // Other block should not appear in original config, but only in siteaccess aware one
        unset($expectedConfig['blocks']['other_block']);

        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilderClosure
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPreProcessor
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPostProcessor
     */
    public function testBlockSettingsNoViewTypesMerge()
    {
        $config = array(
            array(
                'blocks' => array(
                    'block' => array(
                        'name' => 'block',
                        'view_types' => array(
                            'default' => array(
                                'name' => 'Default',
                            ),
                            'large' => array(
                                'name' => 'Large',
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'blocks' => array(
                    'block' => array(
                        'name' => 'block',
                        'view_types' => array(
                            'title' => array(
                                'name' => 'Title',
                            ),
                            'image' => array(
                                'name' => 'Image',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'blocks' => array(
                'block' => array(
                    'name' => 'block',
                    'view_types' => array(
                        'title' => array(
                            'name' => 'Title',
                        ),
                        'image' => array(
                            'name' => 'Image',
                        ),
                    ),
                ),
            )
        );

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }
}
