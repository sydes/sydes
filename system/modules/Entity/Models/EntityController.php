<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Entity\Models;

use Module\Entity\Ui\Listing;
use Sydes\Http\Request;

abstract class EntityController
{
    /**
     * In constructor you can set here repository with linked entity
     *
     * @var Repository
     */
    protected $repo;

    /**
     * Root url of module
     *
     * @var string
     */
    protected $basePath;

    /**
     * @var array
     */
    protected $views = [
        'list' => 'entity/list',
        'form' => 'entity/form',
    ];

    /**
     * Page titles. Will be translated
     *
     * @var array
     */
    protected $titles = [
        'index' => 'listing',
        'create' => 'creation',
        'edit' => 'editing',
    ];
    protected $indexHeaderActions;

    public function index(Request $req, Listing $listing)
    {
        $query = $this->repo->filteredAndSorted($req);

        $page = $req->input('page', 1);
        $perPage = $req->input('per', $this->repo->getModel()->getPerPage());

        $count = $query->count();
        $total = ceil($count/$perPage);

        if ($page > $total && $total > 0) {
            return redirect($this->basePath);
        }

        $models = $query->forPage($page, $perPage)->get();

        $listing->init($this->repo->getModel(), $models->all(), [
            'show'       => settings('entity-tables')->get($this->repo->getModel()->getTable(), []),
            'pagination' => [
                'perPage' => $perPage,
                'count'   => $count,
            ],
        ]);

        $d = document([
            'title' => t($this->titles['index']),
            'header_actions' => $this->indexHeaderActions,
            'content' => view($this->views['list'], [
                'listing' => $listing
            ]),
        ]);

        $d->addModal($listing->tableSettings($this->repo->getModel()->getTable()));
        $d->addScript('column-sorter', '$(".column-sorter").sortable({connectWith: ".column-sorter"}).disableSelection();');

        return $d;
    }

    public function create()
    {
        $d = document([
            'title' => t($this->titles['create']),
            'header_actions' => \H::submitButton(t('save'), ['button' => 'primary', 'data-submit' => 'form-main']),
            'content' => view($this->views['form'], [
                'model' => $this->repo->getModel()->create(),
                'options' => [
                    'method' => 'post',
                    'url' => $this->basePath,
                    'form' => 'main',
                ],
            ]),
        ]);

        return $d;
    }

    public function store(Request $req)
    {
        $model = $this->repo->getModel()->create($req->all());
        $this->repo->save($model);
        notify(t('saved'));

        return redirect($this->basePath);
    }

    public function edit($id)
    {
        $model = $this->repo->findOrFail($id);

        $d = document([
            'title' => t($this->titles['edit']),
            'header_actions' => \H::submitButton(t('save'), ['button' => 'primary', 'data-submit' => 'form-main']),
            'content' => view($this->views['form'], [
                'model' => $model,
                'options' => [
                    'method' => 'put',
                    'url' => $this->basePath.'/'.$id,
                    'form' => 'main',
                ],
            ]),
        ]);

        return $d;
    }

    public function update($id, Request $req)
    {
        $model = $this->repo->findOrFail($id);
        $model->fill($req->all());
        $this->repo->save($model);
        notify(t('saved'));

        return redirect($this->basePath);
    }

    public function destroy($id)
    {
        $this->repo->destroy($id);
        notify(t('deleted'));

        return redirect($this->basePath);
    }
}
