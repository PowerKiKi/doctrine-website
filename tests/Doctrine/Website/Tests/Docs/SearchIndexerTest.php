<?php

namespace Doctrine\Website\Tests\Docs;

use AlgoliaSearch\Client;
use AlgoliaSearch\Index;
use Doctrine\Website\Docs\RSTBuilder;
use Doctrine\Website\Docs\SearchIndexer;
use Doctrine\Website\Projects\Project;
use Doctrine\Website\Projects\ProjectVersion;
use Gregwar\RST\Environment;
use Gregwar\RST\HTML\Document;
use Gregwar\RST\HTML\Nodes\ParagraphNode;
use Gregwar\RST\HTML\Nodes\TitleNode;
use PHPUnit\Framework\TestCase;

class SearchIndexerTest extends TestCase
{
    /** @var Client */
    private $client;

    /** @var RSTBuilder */
    private $rstBuilder;

    /** @var SearchIndexer */
    private $searchIndexer;

    protected function setUp()
    {
        $this->client = $this->createMock(Client::class);
        $this->rstBuilder = $this->createMock(RSTBuilder::class);

        $this->searchIndexer = new SearchIndexer(
            $this->client,
            $this->rstBuilder
        );
    }

    public function testInitSearchIndex()
    {
        $index = $this->createMock(Index::class);

        $this->client->expects($this->once())
            ->method('initIndex')
            ->with(SearchIndexer::INDEX_NAME)
            ->willReturn($index);

        $index->expects($this->once())
            ->method('setSettings')
            ->with([
                'attributesToIndex' => [
                    'unordered(projectName)',
                    'unordered(h1)',
                    'unordered(h2)',
                    'unordered(h3)',
                    'unordered(h4)',
                    'unordered(h5)',
                    'unordered(content)',
                ],
                'attributesToIndex' => ['projectName', 'h1', 'h2', 'h3', 'h4', 'h5', 'content'],
                'customRanking' => ['asc(rank)'],
                'ranking' => ['words', 'typo', 'attribute', 'proximity', 'custom'],
                'minWordSizefor1Typo' => 3,
                'minWordSizefor2Typos' => 7,
                'allowTyposOnNumericTokens' => false,
                'minProximity' => 2,
                'ignorePlurals' => true,
                'advancedSyntax' => true,
                'removeWordsIfNoResults' => 'allOptional',
            ]);

        $index->expects($this->once())
            ->method('clearIndex');

        $this->searchIndexer->initSearchIndex();
    }

    public function testBuildSearchIndexes()
    {
        $project = new Project([
            'shortName' => 'ORM',
            'docsSlug' => 'doctrine-orm',
            'slug' => 'orm',
        ]);
        $version = new ProjectVersion([
            'slug' => '1.0',
        ]);

        $index = $this->createMock(Index::class);

        $this->client->expects($this->once())
            ->method('initIndex')
            ->with(SearchIndexer::INDEX_NAME)
            ->willReturn($index);

        $document = $this->createMock(Document::class);
        $environment = $this->createMock(Environment::class);

        $document->expects($this->once())
            ->method('getEnvironment')
            ->willReturn($environment);

        $environment->expects($this->once())
            ->method('getUrl')
            ->willReturn('index');

        $h1Node = new TitleNode('Test 1', 1, 'title.1');
        $h2Node = new TitleNode('Test 2', 2, 'title.1.1');
        $h3Node = new TitleNode('Test 3', 3, 'title.1.2');
        $h4Node = new TitleNode('Test 4', 4, 'title.1.3');
        $h5Node = new TitleNode('Test 5', 5, 'title.1.4');

        $paragraph1Node = new ParagraphNode('Paragraph 1');
        $paragraph2Node = new ParagraphNode('Paragraph 2');
        $paragraph3Node = new ParagraphNode('Paragraph 3');
        $paragraph4Node = new ParagraphNode('Paragraph 4');
        $paragraph5Node = new ParagraphNode('Paragraph 5');

        $nodes = [
            $h1Node,
            $paragraph1Node,
            $h2Node,
            $paragraph2Node,
            $h3Node,
            $paragraph3Node,
            $h4Node,
            $paragraph4Node,
            $h5Node,
            $paragraph5Node,
        ];

        $document->expects($this->once())
            ->method('getNodes')
            ->willReturn($nodes);

        $documents = [$document];

        $this->rstBuilder->expects($this->once())
            ->method('getDocuments')
            ->willReturn($documents);

        $expectedRecords = [
            [
                'objectID' => '1.0-index.html#title.1-206a9b642b3e16c89a61696ab28f3d5c',
                'rank' => 0,
                'h1' => 'Test 1',
                'h2' => null,
                'h3' => null,
                'h4' => null,
                'h5' => null,
                'url' => '/projects/doctrine-orm/en/1.0/index.html#title.1',
                'content' => null,
                'projectName' => 'ORM',
                '_tags' => ['1.0', 'orm'],
            ],
            [
                'objectID' => '1.0-index.html#title.1-1d2b10371e2bd1744379389efd2dd8ee',
                'rank' => 5,
                'h1' => 'Test 1',
                'h2' => null,
                'h3' => null,
                'h4' => null,
                'h5' => null,
                'url' => '/projects/doctrine-orm/en/1.0/index.html#title.1',
                'content' => 'Paragraph 1',
                'projectName' => 'ORM',
                '_tags' => ['1.0', 'orm'],
            ],
            [
                'objectID' => '1.0-index.html#title.1.1-605e79544a68819ce664c088aba92658',
                'rank' => 1,
                'h1' => 'Test 1',
                'h2' => 'Test 2',
                'h3' => null,
                'h4' => null,
                'h5' => null,
                'url' => '/projects/doctrine-orm/en/1.0/index.html#title.1.1',
                'content' => null,
                'projectName' => 'ORM',
                '_tags' => ['1.0', 'orm'],
            ],
            [
                'objectID' => '1.0-index.html#title.1.1-9bd0adbe2a6bcfba0f571b1b60fdecc3',
                'rank' => 5,
                'h1' => 'Test 1',
                'h2' => 'Test 2',
                'h3' => null,
                'h4' => null,
                'h5' => null,
                'url' => '/projects/doctrine-orm/en/1.0/index.html#title.1.1',
                'content' => 'Paragraph 2',
                'projectName' => 'ORM',
                '_tags' => ['1.0', 'orm'],
            ],
            [
                'objectID' => '1.0-index.html#title.1.2-863f29a9ae33b0a6e8f02d9d17ce8ea1',
                'rank' => 2,
                'h1' => 'Test 1',
                'h2' => 'Test 2',
                'h3' => 'Test 3',
                'h4' => null,
                'h5' => null,
                'url' => '/projects/doctrine-orm/en/1.0/index.html#title.1.2',
                'content' => null,
                'projectName' => 'ORM',
                '_tags' => ['1.0', 'orm'],
            ],
            [
                'objectID' => '1.0-index.html#title.1.2-ab51e222bb465867a494cac86ee3d069',
                'rank' => 5,
                'h1' => 'Test 1',
                'h2' => 'Test 2',
                'h3' => 'Test 3',
                'h4' => null,
                'h5' => null,
                'url' => '/projects/doctrine-orm/en/1.0/index.html#title.1.2',
                'content' => 'Paragraph 3',
                'projectName' => 'ORM',
                '_tags' => ['1.0', 'orm'],
            ],
            [
                'objectID' => '1.0-index.html#title.1.3-9fe74bb46baed663321329a1fc479e8b',
                'rank' => 3,
                'h1' => 'Test 1',
                'h2' => 'Test 2',
                'h3' => 'Test 3',
                'h4' => 'Test 4',
                'h5' => null,
                'url' => '/projects/doctrine-orm/en/1.0/index.html#title.1.3',
                'content' => null,
                'projectName' => 'ORM',
                '_tags' => ['1.0', 'orm'],
            ],
            [
                'objectID' => '1.0-index.html#title.1.3-5bba714e83ac2a57bc1a60eb2b336197',
                'rank' => 5,
                'h1' => 'Test 1',
                'h2' => 'Test 2',
                'h3' => 'Test 3',
                'h4' => 'Test 4',
                'h5' => null,
                'url' => '/projects/doctrine-orm/en/1.0/index.html#title.1.3',
                'content' => 'Paragraph 4',
                'projectName' => 'ORM',
                '_tags' => ['1.0', 'orm'],
            ],
            [
                'objectID' => '1.0-index.html#title.1.4-ce03a4296e564386d37eb22a7dce0623',
                'rank' => 4,
                'h1' => 'Test 1',
                'h2' => 'Test 2',
                'h3' => 'Test 3',
                'h4' => 'Test 4',
                'h5' => 'Test 5',
                'url' => '/projects/doctrine-orm/en/1.0/index.html#title.1.4',
                'content' => null,
                'projectName' => 'ORM',
                '_tags' => ['1.0', 'orm'],
            ],
            [
                'objectID' => '1.0-index.html#title.1.4-9b6bd277e5c781811b4123f260fe3380',
                'rank' => 5,
                'h1' => 'Test 1',
                'h2' => 'Test 2',
                'h3' => 'Test 3',
                'h4' => 'Test 4',
                'h5' => 'Test 5',
                'url' => '/projects/doctrine-orm/en/1.0/index.html#title.1.4',
                'content' => 'Paragraph 5',
                'projectName' => 'ORM',
                '_tags' => ['1.0', 'orm'],
            ],
        ];

        $index->expects($this->once())
            ->method('addObjects')
            ->with($expectedRecords);

        $this->searchIndexer->buildSearchIndexes($project, $version);
    }
}
