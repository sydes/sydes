<?php

/* 
 * Test module
 */

class TestController
{

    public function __construct()
    {

    }

    public function index()
    {
        $d = document();
        $d->data = [
            'content' => '<a href="page/123">page</a><br>
<a href="notfound">notfound</a><br>
<a href="forbidden">forbidden</a><br>
<a href="ajax">ajax</a><br>
<a href="string.txt">string</a><br>
<a href="export">export</a><br>
<a href="html">html</a><br>
<a href="nool">nool</a><br>
<a href="moved">moved</a><br>
<a href="update">update</a><br>
<a href="store">store</a><br>
<a href="ajaxupdate">ajaxupdate</a><br>
<a href="ajaxstore">ajaxstore</a><br>
<a href="refresh">refresh</a><br>
<a href="refresh2">refresh and notify</a><br>
<a href="random">random</a><br>
<a href="nope">nope</a><br>'.app('contentLang').' '.app('request')->url
        ];
        return $d;
    }

    public function page($id)
    {
        $d = document();
        $d->data = [
            'content' => '<p>some content</p>',
            'page_id' => $id,
            'meta_keywords' => 'key, another key',
            'title' => 'Page title',
            'meta_title' => 'Overridden title',
        ];
        $d->addScript('my', '$(document).ready(function(){console.log(\'worked!\')})');
        alert('You\'ve got a "message"', 'info');
        return $d;
    }

    public function notfound()
    {
        abort(404, 'Try Another Castle');
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

    public function string()
    {
        $content = 'this is <br> simple string';
        return response($content)->withMime('txt');
    }

    public function export()
    {
        $content = '"id";"title"'."\r\n".'"1";"Test page"';
        return response($content)->withMime('csv')->download('export.'.time().'.csv');
    }

    public function html()
    {
        return '<strong>this is just compiled string</strong>';
    }

    public function nool()
    {
        // do something
        // if some condition
        retur;
        // else do more things
    }

    public function moved()
    {
        return redirect('page/42');
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
        return refresh();
    }

    public function refreshAndNotify()
    {
        notify('Notify');
        return refresh();
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
                return refresh();
                break;
            case 3:
                return redirect('/');
                break;
            default:
                return null;
        }
    }

    public function adminMain()
    {
        $d = document();
        $d->data = [
            'content' => '<p>Dashboard</p>
<p><a href="admin/pages">Pages</a></p>',
        ];
        return $d;
    }

    public function adminPages()
    {
        $d = document();
        $d->data = [
            'content' => '<p>Pages list</p>
<p><a href="admin">Dashboard</a></p>',
        ];
        return $d;
    }

}
