<?php

namespace SensioLabs\DeprecationDetector\Tests\Visitor\Usage;

use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage;
use SensioLabs\DeprecationDetector\Visitor\Usage\FindClasses;

class FindClassesTest extends FindTestCase
{
    public function testNewStatement()
    {
        $source = <<<'EOC'
<?php

namespace X;

$foo = new Foo();
$bar = new \Bar;

EOC;

        $splFileInfo = $this->prophesize('Symfony\Component\Finder\SplFileInfo');
        $phpFileInfo = $this->parsePhpFileFromStringAndTraverseWithVisitor(
            $file = PhpFileInfo::create($splFileInfo->reveal()),
            $source,
            new FindClasses()
        );

        $this->assertEquals(
            array(
                new ClassUsage('X\Foo', 5),
                new ClassUsage('Bar', 6),
            ),
            $phpFileInfo->classUsages()
        );
    }

    public function testNoNewStatement()
    {
        $source = <<<'EOC'
<?php

$foo = 'hello';
$bar = Bar::bazinga();

EOC;
        $splFileInfo = $this->prophesize('Symfony\Component\Finder\SplFileInfo');
        $usageCollection = $this->parsePhpFileFromStringAndTraverseWithVisitor(
            $file = PhpFileInfo::create($splFileInfo->reveal()),
            $source,
            new FindClasses()
        );

        $this->assertEquals(
            array(),
            $usageCollection->classUsages()
        );
    }
}
