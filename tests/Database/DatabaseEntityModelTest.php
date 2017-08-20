<?php

namespace Sydes\Tests\Database;

use PHPUnit\Framework\TestCase;
use Sydes\Database\Entity\Model;

/**
 * @covers Model
 */
final class DatabaseEntityModelTest extends TestCase
{
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

        $model = Model::unserialize($structure);

        $this->assertInstanceOf(Model::class, $model);
        $this->assertSame($structure, $model->serialize());
    }

    public function testTableName()
    {
        $post = new Post();
        $this->assertSame('posts', $post->getTable());

        $model = Model::unserialize([]);
        $this->assertSame('models', $model->getTable());

        $model = Model::unserialize(['table' => 'stub']);
        $this->assertSame('stub', $model->getTable());

        $model = new Model;
        $model->setTable('test');
        $this->assertSame('test', $model->getTable());
    }

    public function testPrimaryKey()
    {
        $model = Model::unserialize([
            'fields' => [
                'title' => [
                    'type' => 'Text'
                ]
            ]
        ]);

        $this->assertSame('id', $model->getKeyName());
        $this->assertTrue($model->hasIncrementing());

        $model = Model::unserialize([
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
        $model = Model::unserialize([
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
        $model = Model::unserialize([
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
