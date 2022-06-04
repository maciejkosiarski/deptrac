<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Layer\Collector;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Ast\AstMap\File\FileReferenceBuilder;
use Qossmic\Deptrac\Layer\Collector\GlobCollector;

final class GlobCollectorTest extends TestCase
{
    private GlobCollector $collector;

    protected function setUp(): void
    {
        parent::setUp();

        $this->collector = new GlobCollector(__DIR__);
    }

    public function dataProviderSatisfy(): iterable
    {
        yield [['value' => 'foo/layer1/*'], 'foo/layer1/bar.php', true];
        yield [['value' => 'foo/*/*.php'], 'foo/layer1/bar.php', true];
        yield [['value' => 'foo/**/*'], 'foo/layer1/dir/bar.php', true];
        yield [['value' => 'foo/layer1/*'], 'foo/layer2/bar.php', false];
        yield [['value' => 'foo/layer2/*'], 'foo\\layer2\\bar.php', true];
    }

    /**
     * @dataProvider dataProviderSatisfy
     */
    public function testSatisfy(array $configuration, string $filePath, bool $expected): void
    {
        $fileReferenceBuilder = FileReferenceBuilder::create($filePath);
        $fileReferenceBuilder->newClassLike('Test');
        $fileReference = $fileReferenceBuilder->build();

        $actual = $this->collector->satisfy(
            $configuration,
            $fileReference->getClassLikeReferences()[0],
            new AstMap([])
        );

        self::assertSame($expected, $actual);
    }
}
