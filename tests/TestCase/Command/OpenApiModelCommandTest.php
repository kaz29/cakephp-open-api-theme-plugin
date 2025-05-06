<?php
declare(strict_types=1);

namespace OpenApiTheme\Test\TestCase\Command;

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;
use OpenApiTheme\Command\OpenApiModelCommand;
use Cake\ORM\TableRegistry;
use Cake\Database\Schema\TableSchema;
use Cake\ORM\Table;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\AssociationCollection;
use PHPUnit\Framework\MockObject\MockObject;

class OpenApiModelCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * @var \OpenApiTheme\Command\OpenApiModelCommand
     */
    protected $command;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->command = new OpenApiModelCommand();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->command);
        parent::tearDown();
    }

    /**
     * Test getEntityPropertySchema method
     *
     * @return void
     */
    public function testGetEntityPropertySchema(): void
    {
        /** @var Table&MockObject $table */
        $table = $this->getMockBuilder(Table::class)
            ->onlyMethods(['getSchema', 'associations'])
            ->getMock();

        $schema = new TableSchema('test_table', [
            'id' => ['type' => 'integer', 'null' => false, 'comment' => 'Primary key'],
            'name' => ['type' => 'string', 'null' => false, 'comment' => 'Name field'],
        ]);

        $associationCollection = new AssociationCollection();

        $table->expects($this->once())
            ->method('getSchema')
            ->willReturn($schema);

        $table->expects($this->once())
            ->method('associations')
            ->willReturn($associationCollection);

        $result = $this->command->getEntityPropertySchema($table);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);

        $this->assertEquals('column', $result['id']['kind']);
        $this->assertEquals('integer', $result['id']['type']);
        $this->assertEquals(false, $result['id']['null']);
        $this->assertEquals('Primary key', $result['id']['comment']);

        $this->assertEquals('column', $result['name']['kind']);
        $this->assertEquals('string', $result['name']['type']);
        $this->assertEquals(false, $result['name']['null']);
        $this->assertEquals('Name field', $result['name']['comment']);
    }

    /**
     * Test getEntityPropertySchema method with associations
     *
     * @return void
     */
    public function testGetEntityPropertySchemaWithAssociations(): void
    {
        /** @var Table&MockObject $table */
        $table = $this->getMockBuilder(Table::class)
            ->onlyMethods(['getSchema', 'associations'])
            ->getMock();

        $schema = new TableSchema('test_table', [
            'id' => ['type' => 'integer', 'null' => false],
        ]);

        /** @var BelongsTo&MockObject $association */
        $association = $this->getMockBuilder(BelongsTo::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getProperty', 'getTarget'])
            ->getMock();

        /** @var Table&MockObject $targetTable */
        $targetTable = $this->getMockBuilder(Table::class)
            ->onlyMethods(['getEntityClass', 'getRegistryAlias', 'getAlias'])
            ->getMock();

        $association->expects($this->any())
            ->method('getProperty')
            ->willReturn('associated');

        $association->expects($this->any())
            ->method('getTarget')
            ->willReturn($targetTable);

        $targetTable->expects($this->any())
            ->method('getEntityClass')
            ->willReturn('App\Model\Entity\Associated');

        $targetTable->expects($this->any())
            ->method('getRegistryAlias')
            ->willReturn('Associated');

        $targetTable->expects($this->any())
            ->method('getAlias')
            ->willReturn('Associated');

        $associationCollection = new AssociationCollection();
        $associationCollection->add('Associated', $association);

        $table->expects($this->once())
            ->method('getSchema')
            ->willReturn($schema);

        $table->expects($this->once())
            ->method('associations')
            ->willReturn($associationCollection);

        $result = $this->command->getEntityPropertySchema($table);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('associated', $result);

        $this->assertEquals('column', $result['id']['kind']);
        $this->assertEquals('integer', $result['id']['type']);

        $this->assertEquals('association', $result['associated']['kind']);
        $this->assertEquals('\App\Model\Entity\Associated', $result['associated']['type']);
    }
} 