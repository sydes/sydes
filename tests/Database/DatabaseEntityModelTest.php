<?php

namespace Sydes\Tests\Database;

use PHPUnit\Framework\TestCase;
use Sydes\Database\Connection;
use Sydes\Database\Entity\Manager;
use Sydes\Database\Entity\Model;
use Mockery as M;

/**
 * @covers Model
 */
final class DatabaseEntityModelTest extends TestCase
{
    /** @var Manager */
    private $em;

    public function setUp()
    {
        /** @var Connection $conn */
        $conn = M::mock(Connection::class);
        $this->em = new Manager($conn);
    }

    public function tearDown()
    {
        M::close();
    }

    public function testSerializable()
    {
        $structure = [
            'table' => 'stub',
            'fields' => [
                'title' => [
                    'type' => 'Text'
                ]
            ]
        ];

        $model = $this->em->make($structure);

        $this->assertInstanceOf(Model::class, $model);
        $this->assertSame($structure, $this->em->getStructure($model));
    }

    public function testTableName()
    {
        $post = new Post();
        $this->assertSame('posts', $post->getTable());

        $model = $this->em->make([]);
        $this->assertSame('models', $model->getTable());

        $model = $this->em->make(['table' => 'stub']);
        $this->assertSame('stub', $model->getTable());

        $model = new Model;
        $model->setTable('test');
        $this->assertSame('test', $model->getTable());
    }

    public function testPrimaryKey()
    {
        $model = $this->em->make([
            'fields' => [
                'title' => [
                    'type' => 'Text'
                ]
            ]
        ]);

        $this->assertSame('id', $model->getKeyName());
        $this->assertTrue($model->hasIncrementing());

        $model = $this->em->make([
            'fields' => [
                'code' => [
                    'type' => 'Primary'
                ]
            ]
        ]);

        $this->assertSame('code', $model->getKeyName());
        $this->assertFalse($model->hasIncrementing());
    }

    public function testFields()
    {
        $model = $this->em->make([
            'fields' => [
                'title' => [
                    'type' => 'Text'
                ]
            ]
        ]);

        $this->assertTrue($model->hasField('title'));
    }

    public function testExtendedFields()
    {
        $model = $this->em->make([
            'fields' => [
                'comments' => [
                    'type' => 'EntityRelation',
                ],
                'title' => [
                    'type' => 'Text',
                    'settings' => [
                        'translatable' => true,
                    ],
                ]
            ]
        ]);

        $this->assertSame(['comments'], $model->getRelationalFields());
        $this->assertSame(['title'], $model->getTranslatableFields());
    }
}

class Post extends Model
{
}
