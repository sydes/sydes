<?php
namespace Module\SampleName;

use App\AdminMenu;
use App\Route;

class Controller
{
    public static function routes(Route $r)
    {
        $r->get('/', 'SampleName@index');
        $r->get('/admin/sample', 'SampleName@index');
        $r->get('/admin/sample/add', 'SampleName@create');
        $r->get('/admin/sample/another', 'SampleName@myMethod');
        $r->get('/item/{id:[0-9]+}', 'SampleName@item');
        $r->get('/string.txt', 'SampleName@textString');
        $r->get('/html', 'SampleName@asHtml');
        $r->get('/view', 'SampleName@view');
        $r->get('/api.json', 'SampleName@forAjax');
        $r->get('/null', 'SampleName@returnNull');
        $r->get('/export', 'SampleName@export');
        $r->get('/download', 'SampleName@download');
        $r->get('/not-found', 'SampleName@notFound');
        $r->get('/forbidden', 'SampleName@forbidden');
        $r->get('/error', 'SampleName@error');
        $r->get('/moved', 'SampleName@moved');
        $r->get('/back', 'SampleName@back');
        $r->get('/ajax-notify', 'SampleName@ajaxNotify');
        $r->get('/ajax-alert', 'SampleName@ajaxAlert');
        $r->get('/update', 'SampleName@notifyAfterRedirect');
        $r->get('/ajax-update', 'SampleName@notifyAfterRedirect');
        $r->get('/save', 'SampleName@alertAfterRedirect');
        $r->get('/ajax-save', 'SampleName@alertAfterRedirect');
        $r->get('/ajax-random', 'SampleName@random');
        $r->get('/sub-module', 'SampleName/SubModule@method');
        $r->get('/ajax-modal', 'SampleName@modal');
        $r->post('/csrf', 'SampleName@csrf');
    }

    public function __construct()
    {
        // Define properties and services for every method
    }

    public function install(AdminMenu $menu)
    {
        // Create tables, if needed

        $menu->addGroup('sample', 'menu_sample', 'star', 120)
            ->addItem('sample/page', [
                'title' => 'sample_page',
                'url' => '/admin/sample',
                'quick_add' => true,
            ], 10)
            ->addItem('sample/other', [
                'title' => 'another_page',
                'url' => '/admin/sample/another'
            ], 20);
    }

    public function uninstall(AdminMenu $menu)
    {
        $menu->removeGroup('sample');

        // Remove tables and config, if used
    }

    public function index()
    {
        $links = [
            '/'             => 'Front main page for module',
            '/admin/sample' => 'Admin main page for module',
            '/item/42'      => 'Page with id',
            '/string.txt'   => 'Text response',
            '/html'         => 'Html response',
            '/view'         => 'Response with rendered module view',
            '/api.json'     => 'JSON response',
            '/null'         => 'Null returned',
            '/export'       => 'Export any content',
            '/download'     => 'Force downloading',
            '/not-found'    => 'Error 404',
            '/forbidden'    => 'Error 403',
            '/error'        => 'Page with error in code',
            '/moved'        => 'Redirect to true answer',
            '/back'         => 'Redirects back',
            '/ajax-notify'  => 'Ajax notification',
            '/ajax-alert'   => 'Ajax alert',
            '/update'       => 'Notify after redirect',
            '/ajax-update'  => 'Notify after redirect for ajax',
            '/save'         => 'Alert after redirect',
            '/ajax-save'    => 'Alert after redirect for ajax',
            '/ajax-random'  => 'Random response',
            '/ajax-nowhere' => 'Ajax 404 response',
            '/sub-module'   => 'Sub-module works too',
            '/ajax-modal'   => 'Modal example',
        ];

        $d = document([
            'title' => 'Index page of module',
            'content' => '{links} {view_sample}',
            'links' => \H::flatList($links, false, ['id' => 'sample']),
        ]);

        $d->data['view_sample'] = view('sample-name/main', [
            'key' => 'for index',
        ]);

        $d->addScript('my', "$('#sample a').click(function(){
    if ($(this).attr('href').indexOf('/ajax') == 0){
         $.get($(this).attr('href'));
         return false;
    }
})");

        $d->addContextMenu('left', 'edit', [
            'weight' => 10,
            'title' => 'edit_item',
            'url' => '/admin/sample',
        ]);
        $d->addContextMenuItem('left', 'edit', [
            'weight' => 10,
            'title' => 'add_item',
            'url' => '/admin/sample/add',
        ]);

        return $d;
    }

    public function create()
    {
        $d = document([
            'title' => 'Add item',
            'content' => 'Here will be form',
        ]);

        return $d;
    }

    public function myMethod()
    {
        $d = document([
            'title' => 'Another page',
            'content' => sampleHello().' Content for <strong>/admin/sample/another</strong><br>
                <a href="/admin/sample">Back</a>',
        ]);

        return $d;
    }

    public function item($id)
    {
        $d = document([
            'content' => '<p>some html content for {item_id}nd item</p>
<p>This is iblocks: </p>{iblock:sample} and {iblock:other}',
            'item_id' => $id,
            'meta_keywords' => 'key, another key',
            'title' => 'Page title',
            'meta_title' => 'Overridden title',
        ]);

        $d->addScript('my', "console.log('Answer always {$id}')");

        alert('You\'ve got a "message"', 'info');
        return $d;
    }

    public function textString()
    {
        return "This is <strong>string</strong><br>\nTrust me";
    }

    public function asHtml()
    {
        return html("This is <strong>string</strong><br>\nTrust me");
    }

    public function view()
    {
        return view('sample-name/main', [
            'key' => 'value',
        ]);
    }

    public function forAjax()
    {
        return [
            'status' => 'ok',
        ];
    }

    public function returnNull()
    {
        return;
    }

    public function export()
    {
        $content = '"id";"title"'."\r\n".'"1";"Test page"';
        return  downloadContent($content, 'export.'.time().'.csv');
    }

    public function download()
    {
        return  download(DIR_THEME.'/default/assets/images/logo.png', 'image.png');
    }

    public function notFound()
    {
        abort(404, 'Error: This thing can\'t be found');
    }

    public function forbidden()
    {
        abort(403);
    }

    public function error()
    {
        trigger_error("This can be real error in code", E_USER_ERROR);

        return 'place that can\'t reach';
    }

    public function moved()
    {
        return redirect('/item/42');
    }

    public function back()
    {
        return back();
    }

    public function ajaxNotify()
    {
        return notify('Sample notification', 'warning');
    }

    public function ajaxAlert()
    {
        return alert('Sample alert', 'info');
    }

    public function notifyAfterRedirect()
    {
        notify('Updated');
        return back();
    }

    public function alertAfterRedirect()
    {
        alert('Not saved', 'danger');
        return redirect('/item/42');
    }

    public function random()
    {
        $rand = rand(0, 3);
        switch ($rand) {
            case 0:
                return notify('Notify');
                break;
            case 1:
                return alert('Alert');
                break;
            case 2:
                return back();
                break;
            case 3:
                return redirect('/item/42');
                break;
            default:
                return null;
        }
    }

    public function modal()
    {
        return [
            'modal' => [
                'title' => 'Hello Modal!',
                'body' => '<p>This is body text</p>',
                'footer' => '<button type="button" class="btn btn-primary" data-dismiss="modal">Okay</button>',
                'size' => 'modal-sm',
            ],
        ];
    }

    public function csrf()
    {
        return [
            'console' => 'Hello, console!'
        ];
    }
}
