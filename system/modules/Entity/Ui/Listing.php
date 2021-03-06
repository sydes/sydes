<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Entity\Ui;

use Sydes\Database\Entity\Model;
use Sydes\Contracts\Http\Request;

class Listing
{
    private $request;
    private $options;
    private $items;
    /** @var Model */
    private $entity;

    public function __construct(Request $r)
    {
        $this->request = $r;
    }

    /**
     * @param Model   $entity
     * @param Model[] $items
     * @param array    $options
     * @return $this
     */
    public function init(Model $entity, $items, array $options = [])
    {
        $this->options = array_merge_recursive([
            'table' => [],
            'show' => [],
            'columns_limit' => 3,
            'pagination' => [
                'max_links' => 7,
                'arrows' => false,
                'text' => [],
            ],
            'actions' => ['edit', 'delete'],
        ], $options);

        $this->items = $items;
        $this->entity = $entity;

        return $this;
    }

    public function filter()
    {
        $filter = $this->request->input('filter', []);
        $path = $this->request->getUri()->getPath();
        $html = '';

        foreach ($this->entity->getFields() as $key => $field) {
            if (!empty($field->label())) {
                $html .= '<div class="col-sm-3">'.$field->output('filter', ifsetor($filter[$key], '')).'</div>';
            }
        }

        $html .= '<div class="col-sm-3"><label>&nbsp;</label><br>'.\H::submitButton(t('apply')).
            ' '.\H::a(t('clear'), $path, ['button' => 'secondary']).'</div>';

        $html = '<form class="row" action="'.$path.'">'.$html.'</form>';

        return $html;
    }

    public function table()
    {
        $html = $this->open($this->options['table']);

        $html .= $this->head($this->entity, $this->options['show']);

        $html .= '<tbody>';

        if (empty($this->items)) {
            $span = empty($this->options['show']) ? count($this->entity->getFieldList()) : count($this->options['show']);
            $html .= '<tr><td colspan="'.($span+2).'" class="empty-result">'.t('empty_result').'</td></tr>';
        } else {
            foreach ($this->items as $item) {
                $html .= $this->row($item, $this->options['show'], $this->options['actions']);
            }
        }

        $html .= '</tbody>';

        $html .= $this->close();

        return $html;
    }

    public function nav()
    {
        $o = $this->options['pagination'];
        parse_str($this->request->getUri()->getQuery(), $query);
        array_forget($query, 'page');

        $sizes = [];
        foreach ([10, 20, 50, 100, 200] as $num) {
            $sizes[] = [
                'label' => $num,
                'url' => '?'.http_build_query(['per' => $num] + $query)
            ];
        }

        $perPage = \H::dropdown($sizes,
            ['label' => t('per_page'), 'attr' => ['size' => 'sm']],
            ['right', 'up']
        );


        $page = $this->request->input('page', 1);
        $to = $page * $o['perPage'] > $o['count'] ? $o['count'] : $page * $o['perPage'];
        $from = ($page - 1) * $o['perPage'] + 1;
        $count = $from.'-'.$to.' '.t('items_of').' '.$o['count'];


        $pagination = \H::pagination($this->request, ceil($o['count']/$o['perPage']), $o['max_links'],
            $o['arrows'], $o['text'], 'pagination pagination-sm');

        return '<div class="float-right">'.$perPage.' '.$count.'</div>'.$pagination;
    }

    private function open($o)
    {
        $attr = array_merge_recursive([
            'class' => ['table', 'table-sm', 'table-hover']
        ], $o);

        return \H::beginTag('table', $attr);
    }

    private function close()
    {
        return '</table>';
    }

    private function head(Model $item, array $keys)
    {
        $row = '<th style="width:25px" title="'.t('check_all').'">'.\H::checkbox('all', false, ['data-check-all' => true]).'</th>';
        $sort = [$this->request->input('by'), $this->request->input('order', 'desc')];

        $fields = $item->getFields($keys);
        if (empty($keys)) {
            $fields = array_slice($fields, 0, $this->options['columns_limit']);
        }

        foreach ($fields as $field) {
            if (!empty($label = $field->label())) {
                $row .= $this->th($label, $sort, $field->name());
            }
        }

        $settings = \H::dropdown([
                [
                    'label' => t('set_up'),
                    'url' => '#entity-table-settings',
                    'attr' => ['data-toggle' => 'modal']
                ],
                //['label' => t('export'), 'url' => '#table-exporter'], // TODO make export
            ],
            ['label' => '<i class="fa fa-cog"></i>', 'attr' => ['size' => 'sm']]
        );

        $row .= '<th class="text-right">'.$settings.'</th>';

        return '<thead><tr>'.$row.'</tr></thead>';
    }

    private function th($text, $sort, $name)
    {
        parse_str($this->request->getUri()->getQuery(), $query);

        $ord = 'desc';
        $class = [];
        if ($sort[0] == $name) {
            $ord = $sort[1] == 'asc' ? 'desc' : 'asc';
            $class[] = 'order-'.$ord;
        }
        $query = '?'.http_build_query(['by' => $name, 'order' => $ord] + $query);

        return '<th>'.\H::a($text, $query, ['class' => $class]).'</th>';
    }

    private function row(Model $item, array $keys, array $actions)
    {
        $row = '<td>'.\H::checkbox('mass[]', false, ['value' => $item->getKey()]).'</td>';

        $fields = $item->getFields($keys);
        if (empty($keys)) {
            $fields = array_slice($fields, 0, $this->options['columns_limit']);
        }

        foreach ($fields as $field) {
            if (!empty($field->label())) {
                $row .= '<td>'.$field->output('table').'</td>';
            }
        }

        $row .= '<td class="actions">'.$this->actions($item->getKey(), $actions).'</td>';

        return '<tr>'.$row.'</tr>';
    }

    private function actions($id, array $actions)
    {
        $url = $this->request->getUri()->getPath();
        $can = [
            'edit' => [
                'label' => t('edit'),
                'url' => $url.'/'.$id,
                'attr' => ['size' => 'sm'],
            ],
            'delete' => [
                'label' => t('delete'),
                'url' => $url.'/'.$id,
                'attr' => ['data-method' => 'delete']
            ],
            'clone' => [
                'label' => t('clone'),
                'url' => $url.'/'.$id.'/clone',
                'attr' => ['data-method' => 'post']
            ]
        ];

        $settings = [];
        foreach ($actions as $action) {
            if (is_array($action)) {
                $settings[] = $action;
            } else {
                $settings[] = $can[$action];
            }
        }

        return \H::dropdown($settings);
    }

    public function tableSettings($path)
    {
        $all = [];
        foreach ($this->entity->getFields() as $key => $field) {
            if (!empty($label = $field->label())) {
                $all[$key] = $label;
            }
        }

        $body = view('entity/table-settings', [
            'path' => '/admin/entity/table-settings/'.$path,
            'all' => $all,
            'selected' => $this->options['show'],
        ])->render();

        $footer = '<button type="button" class="btn btn-primary btn-sm" data-submit="form-column-sorter">'.
            t('save').'</button>';

        return [
            'id' => 'entity-table-settings',
            'title' => t('table_settings'),
            'body' => $body,
            'footer' => $footer,
        ];
    }
}
