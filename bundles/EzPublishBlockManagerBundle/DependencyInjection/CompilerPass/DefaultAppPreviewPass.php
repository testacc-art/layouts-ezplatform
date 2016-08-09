<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DefaultAppPreviewPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('ezpublish.siteaccess.list')) {
            return;
        }

        $defaultRule = array(
            'template' => $container->getParameter(
                'netgen_block_manager.app.ezpublish.item_preview_template'
            ),
            'match' => array(),
            'params' => array(),
        );

        $scopes = array_merge(
            array('default'),
            $container->getParameter('ezpublish.siteaccess.list')
        );

        foreach ($scopes as $scope) {
            $scopeParams = array(
                "ezsettings.{$scope}.content_view",
                "ezsettings.{$scope}.location_view",
            );

            foreach ($scopeParams as $scopeParam) {
                if (!$container->hasParameter($scopeParam)) {
                    continue;
                }

                $scopeRules = $container->getParameter($scopeParam);
                $scopeRules = $this->addDefaultPreviewRule($scopeRules, $defaultRule);
                $container->setParameter($scopeParam, $scopeRules);
            }
        }
    }

    /**
     * Adds the default eZ content preview template to default scope as a fallback
     * when no preview rules are defined.
     *
     * @param array $scopeRules
     * @param array $defaultRule
     *
     * @return array
     */
    protected function addDefaultPreviewRule($scopeRules, $defaultRule)
    {
        $scopeRules = is_array($scopeRules) ? $scopeRules : array();

        $blockManagerRules = isset($scopeRules['ngbm_app_preview']) ?
            $scopeRules['ngbm_app_preview'] :
            array();

        $blockManagerRules += array(
            '___ngbm_app_preview_default___' => $defaultRule,
        );

        $scopeRules['ngbm_app_preview'] = $blockManagerRules;

        return $scopeRules;
    }
}
