<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Entity\Plugins\Fields;

use Module\Entity\Api\Concerns\HasRelationships;
use Module\Entity\Api\Entity;
use Module\Entity\Api\Field;
use Module\Entity\Api\Relations\Relation;
use Sydes\Database\Connection;
use Sydes\Database\Schema\Blueprint;

class EntityRelationField extends Field
{
    use HasRelationships;

    protected $settings = [
        'relations' => ['has_one', 'has_many', 'belongs_to', 'belongs_to_many'],
        'input_widgets' => ['select', 'autocomplete'],

        'relation' => 'has_one',
        'target'   => '', // class name of entity // TODO for reference use search token
        'on_key'   => 'id',
        'filter'   => [], // TODO ['type' => 'category', 'status' => '1']
        'title'    => 'title',

        'count_in_table' => 3,
    ];

    protected $related;
    /** @var Entity */
    protected $entity;

    private $localTable;
    private $pivotTable;
    private $relatedTable;

    public function init(Entity $entity)
    {
        $this->query = $entity->getBuilder();
        $this->entity = $entity;

        if ($this->settings['relation'] == 'belongs_to_many') {
            $this->localTable = $this->entity->getTable();
            $this->relatedTable = (new $this->settings['target'])->getTable();
            $this->pivotTable = $this->joiningTable($this->localTable, $this->relatedTable);
        }
    }

    public function input($wrapper = null)
    {
        if (in_array($this->settings['relation'], ['has_one', 'has_many'])) {
            return '';
        }

        return parent::input($wrapper);
    }

    public function defaultInput()
    {
        if ($this->settings['relation'] == 'belongs_to_many' && $this->query) {
            $results = [];
            foreach ($this->relation()->getResults()->all() as $item) {
                $results[] = $item->id;
            }
            $this->value = implode(',', $results);
            // TODO change
        }

        return \H::textInput($this->name, $this->value, ['required' => $this->settings['required']]);
        // TODO $settings input_widgets
    }

    public function tableOutput()
    {
        // TODO with link to editing
        if (in_array($this->settings['relation'], ['has_one', 'belongs_to'])) {
            if (!$result = $this->relation()->getResults()) {
                return '';
            }

            return $result->{$this->settings['title']};
        }

        if (!$results = $this->relation()->getResults()->all()) {
            return '';
        }

        $items = [];
        foreach ($results as $item) {
            $items[] = $item->{$this->settings['title']};
        }

        $and = '';
        if (($count = count($items)) > $this->settings['count_in_table']) {
            $items = array_slice($items, 0, $this->settings['count_in_table']);
            $and = ' '.t('and_more', ['count' => $count - $this->settings['count_in_table']]);
        }

        return implode(', ', $items).$and;
    }

    public function defaultOutput()
    {
        if (in_array($this->settings['relation'], ['has_one', 'belongs_to'])) {
            return $this->relation()->getResults()->{$this->settings['title']};
        } else {
            return $this->relation()->getResults();
        }
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
        if ($this->settings['relation'] != 'belongs_to') {
            return null;
        }

        return parent::toString();
    }

    public function onCreate(Blueprint $t, Connection $db)
    {
        if ($this->settings['relation'] == 'belongs_to') {
            $t->string($this->name);

            $t->foreign($this->name)->references($this->settings['on_key'])
                ->on($this->relatedTable)->onDelete('cascade');
        } elseif ($this->settings['relation'] == 'belongs_to_many') {
            $schema = $db->getSchemaBuilder();

            if ($schema->hasTable($this->pivotTable)) {
                return;
            }

            $schema->create($this->pivotTable, function (Blueprint $t) {
                $t->integer($this->localTable.'_id')->unsigned();
                $t->integer($this->relatedTable.'_id')->unsigned();
                $t->integer('position');

                $t->foreign($this->localTable.'_id')->references('id')->on($this->localTable)
                    ->onDelete('cascade');
                $t->foreign($this->relatedTable.'_id')->references('id')->on($this->relatedTable)
                    ->onDelete('cascade');

                $t->primary([$this->localTable.'_id', $this->relatedTable.'_id']);
            });
        }
    }

    public function onDrop(Connection $db)
    {
        if ($this->settings['relation'] == 'belongs_to_many') {
            $db->getSchemaBuilder()->drop($this->pivotTable);
        }
    }

    public function created(Connection $db)
    {
        if ($this->settings['relation'] == 'belongs_to_many') {
            $db->table($this->pivotTable)->insert($this->makePivotAttrs());
        }
    }

    public function updated(Connection $db)
    {
        if ($this->settings['relation'] == 'belongs_to_many') {
            $db->table($this->pivotTable)->where($this->localTable.'_id', $this->entity->id)->delete();
            $db->table($this->pivotTable)->insert($this->makePivotAttrs());
        }
    }

    protected function makePivotAttrs()
    {
        $attrs = [];
        $local = $this->entity->id;
        $count = 1;

        foreach ($this->value as $id) {
            $attrs[] = [
                $this->localTable.'_id' => $local,
                $this->relatedTable.'_id' => $id,
                'position' => $count++,
            ];
        }

        return $attrs;
    }

    /**
     * @return Relation
     */
    public function relation()
    {
        if (!$this->related) {
            $relation = camel_case($this->settings['relation']);

            switch ($relation) {
                case 'hasOne':
                case 'hasMany':
                    $this->related = $this->{$relation}(
                        $this->settings['target'],
                        $this->settings['on_key'],
                        $this->name,
                        $this->entity->id
                    );
                    break;
                case 'belongsTo':
                    $this->related = $this->{$relation}(
                        $this->settings['target'],
                        $this->name,
                        $this->settings['on_key'],
                        $this->value
                    );
                    break;
                default: //belongsToMany
                    $this->related = $this->{$relation}(
                        $this->settings['target'],
                        $this->entity
                    )->orderBy($this->pivotTable.'.position', 'asc');
            }
        }

        return $this->related;
    }

    /**
     * Access to fields of related entity
     *
     * @param $key
     * @param $params
     * @return mixed
     */
    public function __call($key, $params)
    {
        return call_user_func_array([$this->relation()->getResults(), $key], $params);
    }

    /**
     * Access to related entity's fields output
     *
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->relation()->getResults()->$key;
    }

}

/**
 * has_one, belongs_to - returns entity by saved id
 * has_many, belongs_to_many - returns repo with ent_id from other table or pivot
 *
 * belongs_to_many - require pivot table with user_id and role_id
 *
 * pivot name in alphabetical order of the related model names
 *
 * $table->foreign('todolist_id')
->references('id')->on('todolists')
->onDelete('cascade');
 *
 *
 * on init send repo to here
 *
 * load relations lazy and eager for collections
 *
 * $repo->forEntity('Product')->with(['category.parent'])->find(1)
 */
