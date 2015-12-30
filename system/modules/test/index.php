<?php

/* 
 * Test module
 */

class TestController {

    public function __construct() {
        
    }

    public function index() {
        return '<a href="page/123">page</a><br>
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
<a href="nope">nope</a><br>';
    }

    public function page($id) {
        $d = document();
        $d->data = [
            'content' => 'some content',
            'page_id' => $id,
        ];
        $d->addScript('my', '$(document).ready(function(){console.log(\'worked!\')})');
        $d->alert('You are got message');
        return $d;
    }

    public function notfound() {
        abort(404, 'Try Another Castle');
    }

    public function forbidden() {
        abort(403);
    }

    public function ajax() {
        return [
            'status' => 'ok'
        ];
    }

    public function string() {
        $content = 'this is <br> simple string';
        return response($content)->withMime('txt');
    }

    public function export() {
        $content = '"id";"title"'."\r\n".'"1";"Test page"';
        return response($content)->withMime('csv')->download('export.'.time().'.csv');
    }

    public function html() {
        return '<strong>this is just compiled string</strong>';
    }

    public function nool() {
        // do something
        // if some condition
        return;
        // else do more things
    }

    public function moved() {
        return redirect('page/42');
    }

    public function update() {
        return back()->withContent(document(true)->notify('Updated', 'info'));
    }

    public function store() {
        return redirect('/')->withContent(document(true)->alert('This is stored'));
    }

    public function ajaxupdate() {
        return document(true)->notify('Updated', 'info');
    }

    public function ajaxstore() {
        return document(true)->alert('This is stored', 'info');
    }

}
