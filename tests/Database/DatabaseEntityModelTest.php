<?php

namespace Sydes\Tests\Database;

use PHPUnit\Framework\TestCase;
use Sydes\Database\Entity\Model;

/**
 * @covers Model
 */
final class DatabaseEntityModelTest extends TestCase
{
    public function testCanBeCreatedWithStructure()
    {
        $model = Model::unserialize([
            'table' => 'stub',
            'fields' => [
                'title' => [
                    'type' => 'Text'
                ]
            ]
        ]);

        $this->assertInstanceOf(Model::class, $model);

        $this->assertEquals('stub', $model->getTable());

        $this->assertEquals('id', $model->getKeyName());
        $this->assertTrue($model->hasIncrementing());

        $this->assertTrue(isset($model->title));

        $model = Model::unserialize([
            'fields' => [
                'code' => [
                    'type' => 'Primary'
                ]
            ]
        ]);

        $this->assertEquals('models', $model->getTable());

        $this->assertEquals('code', $model->getKeyName());
        $this->assertFalse($model->hasIncrementing());

        $this->assertEquals([
            'table' => 'models',
            'fields' => [
                'code' => [
                    'type' => 'Primary'
                ]
            ]
        ], $model->serialize());
    }
}
