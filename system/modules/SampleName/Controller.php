<?php
namespace Module\SampleName;

use App\Cmf;

class Controller
{
    private $moduleName = 'sample-name';

    public function __construct()
    {
        // Define properties and services for every method
    }

	public function install()
    {
        // Create tables, if needed

        Cmf::installModule($this->moduleName, [
            'handlers' => ['Module\SampleName\Handlers::init'],
            'files' => ['functions.php'],
         ]);

        Cmf::addRoutes($this->moduleName, [
            ['GET', '/', 'SampleName@index'],
            ['GET', '/admin/sample', 'SampleName@index'],
            ['GET', '/admin/sample/add', 'SampleName@create'],
            ['GET', '/admin/sample/another', 'SampleName@myMethod'],
            ['GET', '/item/{id:[0-9]+}', 'SampleName@item'],
            ['GET', '/string.txt', 'SampleName@textString'],
            ['GET', '/html', 'SampleName@asHtml'],
            ['GET', '/view', 'SampleName@view'],
            ['GET', '/api.json', 'SampleName@forAjax'],
            ['GET', '/null', 'SampleName@returnNull'],
            ['GET', '/export', 'SampleName@export'],
            ['GET', '/download', 'SampleName@download'],
            ['GET', '/not-found', 'SampleName@notFound'],
            ['GET', '/forbidden', 'SampleName@forbidden'],
            ['GET', '/error', 'SampleName@error'],
            ['GET', '/moved', 'SampleName@moved'],
            ['GET', '/back', 'SampleName@back'],
            ['GET', '/ajax-notify', 'SampleName@ajaxNotify'],
            ['GET', '/ajax-alert', 'SampleName@ajaxAlert'],
            ['GET', '/update', 'SampleName@notifyAfterRedirect'],
            ['GET', '/ajax-update', 'SampleName@notifyAfterRedirect'],
            ['GET', '/save', 'SampleName@alertAfterRedirect'],
            ['GET', '/ajax-save', 'SampleName@alertAfterRedirect'],
            ['GET', '/ajax-random', 'SampleName@random'],
            ['GET', '/sub-module', 'SampleName/SubModule@method'],
        ]);

        Cmf::addMenuGroup($this->moduleName, 'menu_sample', 'star', 120);
        Cmf::addMenuItem($this->moduleName, [
            'title' => 'sample_page',
            'url' => '/admin/sample',
            'quick_add' => true,
        ], 10);
        Cmf::addMenuItem($this->moduleName, [
            'title' => 'another_page',
            'url' => '/admin/sample/another'
        ], 20);
    }

	public function uninstall()
    {
        Cmf::removeRoutes($this->moduleName);
        Cmf::removeMenuGroup($this->moduleName);
        Cmf::uninstallModule($this->moduleName);

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
        ];

        $d = document([
            'content' => '{links} {view_sample}',
            'links' => \H::listLinks($links, false, ['id' => 'sample']),
        ]);

        $d->data['view_sample'] = view('sample-name/main', [
            'key' => 'for index',
        ]);

        $d->title = 'Index page of module';

        $d->addJs('my', "$('#sample a').click(function(){
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
            'content' => 'Here will be form',
        ]);
        $d->title = 'Add item';
        return $d;
    }

    public function myMethod()
    {
        $d = document([
            'content' => sampleHello().' Content for <strong>/admin/sample/another</strong><br>
                <a href="/admin/sample">Back</a>',
        ]);
        $d->title = 'Another page';
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

        $d->addJs('my', "console.log('Answer always {$id}')");

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
        return  download(DIR_THEME.'/default/img/logo.png', 'image.png');
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
}
