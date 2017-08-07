<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Entity\Plugins\Fields;

use Sydes\Database\Connection;
use Sydes\Database\Entity\Collection;
use Sydes\Database\Entity\Event;
use Sydes\Database\Entity\Field;
use Sydes\Database\Entity\Model;
use Sydes\Database\Entity\Relations\Relation;
use Sydes\Database\Schema\Blueprint;

class EntityRelationField extends Field
{
    /**
     * @var Collection|Model|null
     */
    protected $value;

    /** @var Relation */
    protected $relation;

    protected $settings = [
        // Settings form
        'relations' => ['has_one', 'has_many', 'belongs_to', 'belongs_to_many'],
        'input_widgets' => ['select', 'autocomplete', 'tree'],

        // Relationship
        'relation' => '', // One of relations
        'target'   => '', // Entity class name / Node code in NodeRelationField
        'on_key'   => '', // Primary or other column
        'filter'   => [], // Predefined filter TODO ['type' => 'category', 'status' => '1']
        'title'    => 'title', // Which field should use as title in output
        'standalone' => 1, // Can exist without a parent

        // Listing
        'max_in_table' => 3, // How many previews show in cell
        'hold_max'     => 0, // Can have a maximum of n elements
    ];

    public function getRelated()
    {
        return new $this->settings['target'];
    }

    /**
     * @return Relation
     */
    public function relation()
    {
        return $this->relation;
    }

    public function setRelation(Relation $relation)
    {
        $this->relation = $relation;
    }

    public function value($key = null)
    {
        if ($this->value === false) {
            $this->value = $this->relation->getResults();
        }

        return $this->value;
    }

    public function defaultInput()
    {
        if (in_array($this->settings['relation'], ['has_one', 'has_many'])) {
            return $this->widgetHasMany($this->settings['relation'] == 'has_many');
        }

        if ($this->settings['relation'] == 'belongs_to_many') {
            return $this->widgetBelongsToMany();
        }

        return $this->widgetBelongsTo();
    }

    protected function widgetHasMany($many)
    {
        return $many ?
            'has '.count($this->value()).'. [add] more' :
            ($this->value() ? '[edit] this' : '[create] one');
    }

    protected function widgetBelongsToMany()
    {
        if ($this->value()) {
            $results = [];
            foreach ($this->value()->all() as $item) {
                $results[] = $item->getKey();
            }
            $value = implode(',', $results);
        } else {
            $value = '';
        }

        return \H::textInput($this->name, $value, ['required' => $this->settings['required']]);
        // TODO multiple select, can create?
    }

    protected function widgetBelongsTo()
    {
        $related = $this->getRelated();
        $all = $this->relation->getQuery()->newQuery()->setModel($related)->get();
        $values = $all->pluck($this->settings['title'], $related->getKeyName())->all();

        if ($all->count() > 50) {
            // TODO
        } else {
            return \H::select($this->name, $this->value() ? $this->value()->getKey() : '', $values, [
                'required' => $this->settings['required']
            ]);
        }







        return \H::textInput($this->name, $this->value() ? $this->value()->getKey() : '', ['required' => $this->settings['required']]);
    }

    public function tableOutput()
    {
        // TODO with link to editing in NODES
        if (in_array($this->settings['relation'], ['has_one', 'belongs_to'])) {
            if (!$result = $this->value()) {
                return '';
            }

            return $result->{$this->settings['title']};
        }

        if (!$results = $this->value()->all()) {
            return '';
        }

        $items = [];
        foreach ($results as $item) {
            $items[] = $item->{$this->settings['title']};
        }

        $and = '';
        if (($count = count($items)) > $this->settings['max_in_table']) {
            $items = array_slice($items, 0, $this->settings['max_in_table']);
            $and = ' '.t('and_more', ['count' => $count - $this->settings['max_in_table']]);
        }

        return implode(', ', $items).$and;
    }

    public function defaultOutput()
    {
        if (!in_array($this->settings['relation'], ['has_one', 'belongs_to'])) {
            return $this->value();
        }

        $result = $this->value();

        return $result ? $result->{$this->settings['title']} : '';
    }

    public function set($value)
    {
        if ($this->settings['relation'] == 'belongs_to_many' && is_string($value)) {
            // TODO change
            $value = explode(',', $value);
        }

        $this->value = $value;

        return $this;
    }

    public function toString()
    {
        if ($this->settings['relation'] == 'belongs_to') {
            return $this->relation->getParent()->getAttribute($this->name);
        }

        return null;
    }

    public function getEventListeners(Event $events)
    {
        if ($this->settings['relation'] == 'belongs_to') {

            $events->on('create', function (Blueprint $t) {
                $t->string($this->name)->nullable();

                $onDelete = $this->settings['standalone'] ? 'set null' : 'cascade';

                $t->foreign($this->name)->references($this->settings['on_key'])
                    ->on($this->relation->getRelated()->getTable())->onDelete($onDelete);
            });

        } elseif ($this->settings['relation'] == 'belongs_to_many') {

            $pivotTable = $this->relation->getTable();
            $localForeign = $this->relation->getParent()->getForeignKey();

            $events->on('create', function ($t, Connection $db) use ($pivotTable, $localForeign) {
                $localTable = $this->relation->getParent()->getTable();
                $relatedTable = $this->relation->getRelated()->getTable();
                $relatedForeign = $this->relation->getRelated()->getForeignKey();
                $schema = $db->getSchemaBuilder();

                if ($schema->hasTable($pivotTable)) {
                    return;
                }

                $schema->create($this->relation->getTable(),
                    function (Blueprint $t) use ($localForeign, $relatedForeign, $localTable, $relatedTable) {
                    $t->integer($localForeign)->unsigned();
                    $t->integer($relatedForeign)->unsigned();
                    $t->integer('position');

                    $t->foreign($localForeign)->references('id')->on($localTable)
                        ->onDelete('cascade');
                    $t->foreign($relatedForeign)->references('id')->on($relatedTable)
                        ->onDelete('cascade');

                    $t->primary([$localForeign, $relatedForeign]);
                });
            });

            $events->on('drop', function (Connection $db) {
                $db->getSchemaBuilder()->drop($this->relation->getTable());
            });

            $events->on('inserted', function (Connection $db) {
                $db->table($this->relation->getTable())->insert($this->makePivotAttrs($this->relation->getParent()));
            });

            $events->on('updated', function (Connection $db) use ($pivotTable, $localForeign) {
                $parent = $this->relation->getParent();
                $db->table($pivotTable)->where($localForeign, $parent->getKey())->delete();
                $db->table($pivotTable)->insert($this->makePivotAttrs($parent));
            });

        }
    }

    protected function makePivotAttrs(Model $parent)
    {
        $attrs = [];
        $count = 1;
        $values = explode(',', $this->relation->getParent()->getAttribute($this->name));

        foreach ($values as $id) {
            $attrs[] = [
                $parent->getForeignKey() => $parent->getKey(),
                $this->relation->getRelated()->getForeignKey() => $id,
                'position' => $count++,
            ];
        }

        return $attrs;
    }
}
