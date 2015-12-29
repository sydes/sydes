<?php

/* 
 * Test module
 */

class TestController {

    public function __construct() {
        
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
        $content = 'this is simple string';
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
        document()->notify('Updated', 'info');
        return back();
    }

    public function store() {
        document()->alert('This is stored');
        return redirect('');
    }

    public function ajaxupdate() {
        return document()->notify('Updated', 'info');
    }

    public function ajaxstore() {
        return document()->alert('This is stored', 'info');
    }
}
