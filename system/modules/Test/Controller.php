<?php
/*
 * Test module
 */
namespace Module\Test;

use H;

class Controller
{

    public function __construct()
    {

    }

    public function index()
    {
        $d = document();
        $d->data = [
            'content' => H::listLinks([
                'page/123'   => 'Страница 123',
                'notfound'   => 'notfound',
                'forbidden'  => 'forbidden',
                'ajax'       => 'ajax',
                'string.txt' => 'string',
                'export'     => 'export',
                'html'       => 'html',
                'nool'       => 'nool',
                'moved'      => 'Редирект',
                'update'     => 'update',
                'store'      => 'store',
                'ajaxupdate' => 'ajaxupdate',
                'ajaxstore'  => 'ajaxstore',
                'refresh'    => 'refresh',
                'refresh2'   => 'refresh and notify',
                'random'     => 'random',
            ]),
        ];

        return $d;
    }

    public function page($id)
    {
        $d = document([
            'content' => '<p>some content</p>',
            'page_id' => $id,
            'meta_keywords' => 'key, another key',
            'title' => 'Page title',
            'meta_title' => 'Overridden title',
        ]);
        $d->addJs('my', '$(document).ready(function(){console.log(\'worked!\')})');
        alert('You\'ve got a "message"', 'info');
        return $d;
    }

    public function notFound()
    {
        abort(404, t('error_page_not_found'));
    }

    public function forbidden()
    {
        abort(403);
    }

    public function ajax()
    {
        return [
            'status' => 'ok',
        ];
    }

    public function textString()
    {
        $content = "this is <br>\nsimple string";
        return $content;
    }

    public function export()
    {
        //$content = '"id";"title"'."\r\n".'"1";"Test page"';
        //return response($content)->withMime('csv')->download('export.'.time().'.csv');
        // TODO раскомментировать
    }

    public function html()
    {
        return html('this is just <strong>compiled string</strong>');
    }

    public function nool()
    {

        $var = strpos();

        return $var;
    }

    public function moved()
    {
        return redirect('/page/42', 301);
    }

    public function notifyAfterRedirect()
    {
        notify('Updated');
        return back();
    }

    public function alertAfterRedirect()
    {
        alert('This is not stored', 'danger');
        return redirect('/');
    }

    public function ajaxNotify()
    {
        return notify('Not Updated', 'warning');
    }

    public function ajaxAlert()
    {
        return alert('This is stored', 'info');
    }

    public function ajaxRefresh()
    {
        return back();
    }

    public function refreshAndNotify()
    {
        notify('Notify');
        return back();
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
                return redirect();
                break;
            default:
                return null;
        }
    }

    public function adminMain()
    {
        $d = document([
            'content' => '<p>Dashboard</p>
<p style="height: 500px;"><a href="admin/pages">Pages</a></p>',
        ]);
        $d->title = 'Dashboard';
        return $d;
    }

    public function adminPages()
    {
        $d = document([
            'content' => '<p>Pages list</p>
<p><a href="admin">Dashboard</a></p>',
        ]);
        $d->title = 'Pages list';
        return $d;
    }

    public function login()
    {
        return view('test/login-form', ['signUp' => 0, 'autoLogin' => 1, 'button' => 'go']);
    }

    public function doLogin()
    {
        $r = app('request');
        if (app('user')->login($r->get('username'), $r->get('password'), $r->has('remember'))) {
            $entry = $_SESSION['entry'];
            unset($_SESSION['entry']);
            return redirect($entry);
        }
        return back();
    }

}
