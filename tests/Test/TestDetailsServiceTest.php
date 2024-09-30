<?php

declare(strict_types=1);

namespace App\Tests\Test;

use App\Entity\Author;
use App\Entity\Test;
use App\Service\TestSourceService;
use App\Test\TestDetailsService;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class TestDetailsServiceTest extends KernelTestCase
{
    public function testGetReturnsCorrectStructure(): void
    {
        // Mock the dependencies
        $testSourceService = $this->createMock(TestSourceService::class);
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);

        // Mock the Test entity
        $test = $this->createMock(Test::class);

        // Set up the expected behavior for Test entity methods
        $test->method('getName')->willReturn('Test Name');
        $test->method('getDescription')->willReturn('Test Description');
        $test->method('getDuration')->willReturn(60);
        $test->method('isFree')->willReturn(false);

        // Mock the authors
        $author = $this->createMock(Author::class);
        $author->method('getName')->willReturn('Author Name');
        $author->method('getSlug')->willReturn('author-slug');

        // Mock the Collection for authors
        $authorsCollection = new ArrayCollection([$author]);

        // Mock the Test to return authors as a Collection
        $test->method('getAuthors')->willReturn($authorsCollection);

        // Set up behavior for TestSourceService methods
        $testSourceService->method('getAll')->willReturn(['Question 1', 'Question 2']);
        $testSourceService->method('getTotalCount')->willReturn(2);
        $testSourceService->method('getInstruction')->willReturn('Test Instructions');

        // Set up URL generation for the author
        $urlGenerator->method('generate')
            ->with('tests.author', ['slug' => 'author-slug'])
            ->willReturn('/authors/author-slug');

        // Create the service
        $testDetailsService = new TestDetailsService($testSourceService, $urlGenerator);

        // Call the method
        $details = $testDetailsService->get($test, 'en', true);

        // Assert the structure of the result
        $this->assertIsArray($details);
        $this->assertArrayHasKey('name', $details);
        $this->assertArrayHasKey('description', $details);
        $this->assertArrayHasKey('instruction', $details);
        $this->assertArrayHasKey('duration', $details);
        $this->assertArrayHasKey('length', $details);
        $this->assertArrayHasKey('paid', $details);
        $this->assertArrayHasKey('questions', $details);
        $this->assertArrayHasKey('authors', $details);

        // Assert values
        $this->assertEquals('Test Name', $details['name']);
        $this->assertEquals('Test Description', $details['description']);
        $this->assertEquals('Test Instructions', $details['instruction']);
        $this->assertEquals(60, $details['duration']);
        $this->assertEquals(2, $details['length']);
        $this->assertTrue($details['paid']);
        $this->assertEquals(['Question 1', 'Question 2'], $details['questions']);
        $this->assertEquals([['name' => 'Author Name', 'url' => '/authors/author-slug']], $details['authors']);
    }
}