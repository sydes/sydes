<?php

/* 
 * Test module
 */

class TestController {

    public function __construct() {
        
    }

    public function page($id) {
        $d = document();
        $d->data = ['content' => 'some content'];
        $d->addScript('my', '$(document).ready(function(){console.log(\'worked!\')})');
        return $d;
    }

    public function notfound() {
        abort(404);
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
        return 'this is simple string';
    }

    public function nool() {
        // do something
        // if some condition
        return;
        // else do more things
    }

    public function moved() {
        return redirect('/page');
    }

    public function update() {
        return redirect()->back();
    }

    public function store() {
        return redirect('/')->with('alert', 'This is stored', 'info');
    }
}
