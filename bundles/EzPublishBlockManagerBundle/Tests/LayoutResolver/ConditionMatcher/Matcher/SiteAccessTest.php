<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Layout\Resolver\ConditionMatcher\Matcher;

use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\ConditionMatcher\Matcher\SiteAccess;
use eZ\Publish\Core\MVC\Symfony\SiteAccess as EzPublishSiteAccess;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class SiteAccessTest extends \PHPUnit_Framework_TestCase
{
    use RequestStackAwareTrait;

    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\ConditionMatcher\Matcher\SiteAccess
     */
    protected $conditionMatcher;

    /**
     * Sets up the route target tests.
     */
    public function setUp()
    {
        $request = Request::create('/');
        $request->attributes->set('siteaccess', new EzPublishSiteAccess('eng'));

        $requestStack = new RequestStack();
        $requestStack->push($request);
        $this->setRequestStack($requestStack);

        $this->conditionMatcher = new SiteAccess();
        $this->conditionMatcher->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\ConditionMatcher\Matcher\SiteAccess::getConditionIdentifier
     */
    public function testGetConditionIdentifier()
    {
        self::assertEquals('siteaccess', $this->conditionMatcher->getConditionIdentifier());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\ConditionMatcher\Matcher\SiteAccess::matches
     *
     * @param array $parameters
     * @param bool $matches
     *
     * @dataProvider matchesProvider
     */
    public function testMatches($parameters, $matches)
    {
        self::assertEquals($matches, $this->conditionMatcher->matches($parameters));
    }

    /**
     * Provider for {@link self::testMatches}.
     *
     * @return array
     */
    public function matchesProvider()
    {
        return array(
            array(array(), false),
            array(array('eng'), true),
            array(array('cro'), false),
            array(array('eng', 'cro'), true),
            array(array('cro', 'eng'), true),
            array(array('cro', 'fre'), false),
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\ConditionMatcher\Matcher\SiteAccess::matches
     */
    public function testMatchesWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        self::assertFalse($this->conditionMatcher->matches(array()));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\ConditionMatcher\Matcher\SiteAccess::matches
     */
    public function testMatchesWithNoSiteAccess()
    {
        // Make sure we have no siteaccess
        $this->requestStack->getCurrentRequest()->attributes->remove('siteaccess');

        self::assertFalse($this->conditionMatcher->matches(array()));
    }
}
