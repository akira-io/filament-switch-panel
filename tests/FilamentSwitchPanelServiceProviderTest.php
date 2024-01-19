<?php

namespace Akira\FilamentSwitchPanel\Tests;

use Akira\FilamentSwitchPanel\FilamentSwitchPanelServiceProvider;
use PHPUnit\Framework\TestCase;
use Spatie\LaravelPackageTools\Package;

class FilamentSwitchPanelServiceProviderTest extends TestCase
{
    /**
     * Test case for method configurePackage in FilamentSwitchPanelServiceProvider class
     *
     * Here, we ensure that the required configurations are set on the package during its initialization.
     */
    public function testConfigurePackage(): void
    {
        $packageMock = $this->getMockBuilder(Package::class)
            ->disableOriginalConstructor()
            ->getMock();

        $packageMock->expects($this->once())
            ->method('name')
            ->with(FilamentSwitchPanelServiceProvider::$name)
            ->willReturnSelf();

        $packageMock->expects($this->once())
            ->method('hasTranslations')
            ->willReturnSelf();

        $packageMock->expects($this->once())
            ->method('hasViews')
            ->with(FilamentSwitchPanelServiceProvider::$name)
            ->willReturnSelf();

        $provider = new FilamentSwitchPanelServiceProvider(app());

        $reflectObject = new \ReflectionObject($provider);
        $method = $reflectObject->getMethod('configurePackage');
        $method->setAccessible(true);

        $method->invoke($provider, $packageMock);
    }
}
