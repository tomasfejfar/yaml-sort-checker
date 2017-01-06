<?php declare(strict_types = 1);

namespace Mhujer\YamlSortChecker;

use PHPUnit\Framework\TestCase;

class SortCheckerTest extends TestCase
{

	public function testSortedFile(): void
	{
		$checker = new SortChecker();
		$result = $checker->isSorted(__DIR__ . '/fixture/ok.yml', 10);

		$this->assertTrue($result->isOk());
		$this->assertCount(0, $result->getMessages());
	}

	public function testInvalidYamlIsInvalid(): void
	{
		$checker = new SortChecker();
		$result = $checker->isSorted(__DIR__ . '/fixture/invalid-yaml.yml', 1);

		$this->assertFalse($result->isOk());
		$this->assertCount(1, $result->getMessages());
		$this->assertStringStartsWith('Unable to parse the YAML string', $result->getMessages()[0]);
	}

	public function testInvalidSortInFirstLevel(): void
	{
		$checker = new SortChecker();
		$result = $checker->isSorted(__DIR__ . '/fixture/first-level.yml', 1);

		$this->assertFalse($result->isOk());
		$this->assertCount(1, $result->getMessages());
		$this->assertSame('"bar" should be before "foo"', $result->getMessages()[0]);
	}

	public function testInvalidSortInSecondLevel(): void
	{
		$checker = new SortChecker();
		$result = $checker->isSorted(__DIR__ . '/fixture/second-level.yml', 2);

		$this->assertFalse($result->isOk());
		$this->assertCount(1, $result->getMessages());
		$this->assertSame('"foo.car" should be before "foo.dar"', $result->getMessages()[0]);
	}

	public function testInvalidSortInFirstAndSecondLevel(): void
	{
		$checker = new SortChecker();
		$result = $checker->isSorted(__DIR__ . '/fixture/first-and-second-level.yml', 2);

		$this->assertFalse($result->isOk());
		$this->assertCount(2, $result->getMessages());
		$this->assertSame('"foo" should be before "zoo"', $result->getMessages()[0]);
		$this->assertSame('"foo.car" should be before "foo.dar"', $result->getMessages()[1]);
	}

	public function testInvalidSortInFirstSecondAndThirdLevel(): void
	{
		$checker = new SortChecker();
		$result = $checker->isSorted(__DIR__ . '/fixture/first-second-and-third-level.yml', 3);

		$this->assertFalse($result->isOk());
		$this->assertCount(3, $result->getMessages());
		$this->assertSame('"foo" should be before "zoo"', $result->getMessages()[0]);
		$this->assertSame('"foo.car" should be before "foo.dar"', $result->getMessages()[1]);
		$this->assertSame('"foo.car.c" should be before "foo.car.d"', $result->getMessages()[2]);
	}

}
