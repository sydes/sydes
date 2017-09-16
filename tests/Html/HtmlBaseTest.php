<?php

namespace Sydes\Tests\Html;

use PHPUnit\Framework\TestCase;
use Sydes\Html\Base;

final class HtmlBaseTest extends TestCase
{
    public function testAttr()
    {
        $result1 = Base::attr([]);
        $result2 = Base::attr(['required' => true]);
        $result3 = Base::attr(['id'       => 'main']);
        $result4 = Base::attr(['class'    => ['first', 'second']]);
        $result5 = Base::attr([
            'data' => [
                'one' => true,
                'two' => 'value',
                'three' => [
                    'foo' => 'bar',
                ],
            ]
        ]);
        $result6 = Base::attr([
            'ng' => [
                'one' => true,
                'two' => 'value',
            ]
        ]);

        $this->assertSame('', $result1);
        $this->assertSame(' required', $result2);
        $this->assertSame(' id="main"', $result3);
        $this->assertSame(' class="first second"', $result4);
        $this->assertSame(' data-one data-two="value" data-three="{"foo":"bar"}"', $result5);
        $this->assertSame(' ng-one ng-two="value"', $result6);
    }

    public function testTag()
    {
        $lorem = 'Lorem ipsum';
        $array = [
            Base::img('/image1.jpg'),
            Base::img('/image2.jpg'),
        ];

        $result1 = Base::tag('', $lorem);
        $result2 = Base::tag('p', $lorem);
        $result3 = Base::tag('p', $lorem, ['class' => 'text-center']);
        $result4 = Base::tag('div', '<p>'.$lorem.'</p>', ['class' => 'row']);
        $result5 = Base::tag('div', $array, ['class' => 'row']);

        $this->assertSame($lorem, $result1);
        $this->assertSame('<p>'.$lorem.'</p>', $result2);
        $this->assertSame('<p class="text-center">'.$lorem.'</p>', $result3);
        $this->assertSame('<div class="row"><p>'.$lorem.'</p></div>', $result4);
        $this->assertSame('<div class="row"><img src="/image1.jpg" alt="">'.
            '<img src="/image2.jpg" alt=""></div>', $result5);
    }

    public function testImg()
    {
        $url = '//example.com/dot.gif';
        $alt = 'Image';

        $this->assertSame('<img src="'.$url.'" alt="">', Base::img($url));
        $this->assertSame('<img alt="'.$alt.'" src="'.$url.'">', Base::img($url, ['alt' => $alt]));
        $this->assertSame(
            '<img srcset="/img-x2.jpg 2x,/img-x4.jpg 4x" src="'.$url.'" alt="">',
            Base::img($url, ['srcset' => ['2x' => '/img-x2.jpg', '4x' => '/img-x4.jpg']])
        );
    }

    public function testSubmitButton()
    {
        $this->assertSame('<button type="submit">Submit</button>', Base::submitButton());
        $this->assertSame('<button type="submit">Save</button>', Base::submitButton('Save'));
    }

    public function testSelect()
    {
        // empty list
        $select = Base::select('empty-list', null, []);
        $this->assertSame($select, '<select name="empty-list"><option value="" selected>--</option></select>');

        // empty list with placeholder
        $select = Base::select('name', null, [], ['placeholder' => 'Select one']);
        $this->assertSame($select, '<select name="name"><option value="" selected>Select one</option></select>');

        // select attributes
        $select = Base::select('name', null, [], ['id' => 'select-id', 'class' => ['class-name']]);
        $this->assertSame($select, '<select id="select-id" class="class-name" name="name">'.
            '<option value="" selected>--</option></select>');

        // option attributes
        $select = Base::select(
            'size', null, ['L' => 'Large', 'S' => 'Small'], [], ['L' => ['data-foo' => 'bar', 'disabled' => true]]
        );
        $this->assertSame($select,
            '<select name="size"><option value="L" data-foo="bar" disabled>Large</option>'.
            '<option value="S">Small</option></select>');

        // filled list
        $select = Base::select('size', false, ['L' => 'Large', 'S' => 'Small']);
        $this->assertSame($select,
            '<select name="size"><option value="L">Large</option><option value="S">Small</option></select>');

        // filled list with value
        $select = Base::select('size', 'L', ['L' => 'Large', 'S' => 'Small']);
        $this->assertSame($select,
            '<select name="size"><option value="L" selected>Large</option><option value="S">Small</option></select>');

        // multiple selection
        $select = Base::select(
            'size', ['M'],
            ['0' => 'All Sizes', 'L' => 'Large', 'M' => 'Medium', 'S' => 'Small'],
            ['multiple' => true]
        );
        $this->assertSame($select,
            '<select multiple name="size[]"><option value="0">All Sizes</option><option value="L">Large</option>'.
            '<option value="M" selected>Medium</option><option value="S">Small</option></select>');

        // option groups
        $select = Base::select(
            'size', null, [
                'Large sizes' => [
                    'L' => 'Large',
                    'XL' => 'Extra Large',
                ],
                'S' => 'Small',
            ], ['class' => 'class-name', 'id' => 'select-id']
        );
        $this->assertSame($select,
            '<select class="class-name" id="select-id" name="size"><optgroup label="Large sizes">'.
            '<option value="L">Large</option><option value="XL">Extra Large</option></optgroup>'.
            '<option value="S">Small</option></select>'
        );

        // encoded html
        $select = Base::select(
            'encoded_html', null, ['no_break_space' => '&nbsp;', 'ampersand' => '&amp;', 'lower_than' => '&lt;']
        );
        $this->assertSame($select,
            '<select name="encoded_html"><option value="no_break_space">&nbsp;</option>'.
            '<option value="ampersand">&amp;</option><option value="lower_than">&lt;</option></select>'
        );
    }

    public function testBaseInput()
    {
        $form1 = Base::input('text', 'foo');
        $form2 = Base::input('text', 'foo', 'foobar');
        $form3 = Base::input('date', 'foobar', null, ['class' => 'span2']);
        $form4 = Base::input('hidden', 'foo', true);
        $form6 = Base::input('checkbox', 'foo-check', true);

        $this->assertSame('<input type="text" name="foo" value="">', $form1);
        $this->assertSame('<input type="text" name="foo" value="foobar">', $form2);
        $this->assertSame('<input class="span2" type="date" name="foobar" value="">', $form3);
        $this->assertSame('<input type="hidden" name="foo" value="1">', $form4);
        $this->assertSame('<input type="checkbox" name="foo-check" value="1">', $form6);
    }

    public function testInput()
    {
        $inputs2 = ['password', 'file'];
        $inputs3 = ['text', 'hidden', 'color', 'date', 'email', 'number', 'range', 'search', 'tel', 'time', 'url'];

        foreach ($inputs2 as $input) {
            $method = $input.'Input';
            $this->assertSame('<input type="'.$input.'" name="foo" value="">', Base::$method('foo'));
            $this->assertSame('<input class="span2" type="'.$input.'" name="foo" value="">',
                Base::$method('foo', ['class' => 'span2']));
        }

        foreach ($inputs3 as $input) {
            $method = $input.'Input';
            $this->assertSame('<input type="'.$input.'" name="foo" value="">', Base::$method('foo', null));
            $this->assertSame('<input type="'.$input.'" name="foo" value="1">', Base::$method('foo', 1));
            $this->assertSame('<input class="span2" type="'.$input.'" name="foo" value="">',
                Base::$method('foo', null, ['class' => 'span2']));
        }

        $this->assertSame('<input multiple type="file" name="foo[]" value="">',
            Base::fileInput('foo', ['multiple' => true]));
    }

    public function testBooleanInput()
    {
        foreach (['radio', 'checkbox'] as $type) {
            $this->assertSame('<input type="'.$type.'" name="foo" value="1">', Base::$type('foo'));

            $this->assertSame('<input checked type="'.$type.'" name="foo" value="1">', Base::$type('foo', 1));

            $this->assertSame('<input value="foo" type="'.$type.'" name="foo">',
                Base::$type('foo', 0, ['value' => 'foo']));

            $this->assertSame('<label><input checked type="'.$type.'" name="foo" value="1"> Foo</label>',
                Base::$type('foo', 1, ['label' => 'Foo']));
        }
    }

    public function testTable()
    {
        $this->assertSame('<table><tbody></tbody></table>', Base::table([]));

        $this->assertSame('<table><tbody><tr><td>1</td><td>2</td></tr>'.
            '<tr><td>3</td><td>4</td></tr></tbody></table>', Base::table([[1,2], [3,4],]));

        $this->assertSame('<table><thead><tr><th>foo</th><th>bar</th></tr></thead>'.
            '<tbody><tr><td>1</td><td>2</td></tr><tr><td>3</td><td>4</td></tr></tbody></table>',
            Base::table([[1,2], [3,4]], ['foo', 'bar']));

        $this->assertSame('<table class="table"><tbody><tr><td>1</td><td>2</td></tr>'.
            '<tr><td>3</td><td>4</td></tr></tbody></table>', Base::table([[1,2], [3,4],], [], ['class' => ['table']]));
    }

    public function testList()
    {
        $this->assertSame('<ul></ul>', Base::ul([]));

        $this->assertSame('<ul><li>foo</li><li>bar</li></ul>', Base::ul(['foo', 'bar']));

        $this->assertSame('<ol><li>foo</li><li>bar</li></ol>', Base::ol(['foo', 'bar']));

        $this->assertSame('<ul id="menu"><li>foo</li></ul>', Base::ul(['foo'], ['id' => 'menu']));

        $this->assertSame('<ul><li class="item">foo</li><li class="item">bar</li></ul>',
            Base::ul(['foo', 'bar'], ['itemAttr' => ['class' => ['item']]]));

        $this->assertSame('<ul><li class="item-0">foo</li><li class="item-1">bar</li></ul>',
            Base::ul(['foo', 'bar'], ['item' => function ($item, $i) {
                return '<li class="item-'.$i.'">'.$item.'</li>';
            }]));
    }

    public function testTreeList()
    {
        $cases = [
            'flat' => [
                [
                    ['name' => 'foo', 'level' => '1',],
                    ['name' => 'bar', 'level' => '1',],
                ],
                '<ul>'.
                    '<li><a>foo</a></li>'.
                    '<li><a>bar</a></li>'.
                '</ul>'
            ],

            'hump' => [
                [
                    ['name' => 'foo', 'level' => '1',],
                    ['name' => 'bar', 'level' => '2',],
                    ['name' => 'baz', 'level' => '1',],
                ],
                '<ul>'.
                    '<li>'.
                        '<a>foo</a>'.
                        '<ul>'.
                            '<li><a>bar</a></li>'.
                        '</ul>'.
                    '</li>'.
                    '<li><a>baz</a></li>'.
                '</ul>'
            ],

            'stairs' => [
                [
                    ['name' => 'foo', 'level' => '1',],
                    ['name' => 'bar', 'level' => '2',],
                    ['name' => 'baz', 'level' => '3',],
                ],
                '<ul>'.
                    '<li>'.
                        '<a>foo</a>'.
                        '<ul>'.
                            '<li>'.
                                '<a>bar</a>'.
                                '<ul>'.
                                    '<li><a>baz</a></li>'.
                                '</ul>'.
                            '</li>'.
                        '</ul>'.
                    '</li>'.
                '</ul>'
            ],

            'misc' => [
                [
                    ['name' => 'foo', 'level' => '1', 'skip' => true,],
                    ['name' => 'bar', 'level' => '1', 'attr' => ['class' => 'item']],
                    ['name' => 'baz', 'level' => '1',],
                ],
                '<ul>'.
                    '<li class="item"><a>bar</a></li>'.
                    '<li><a>baz</a></li>'.
                '</ul>'
            ]
        ];

        foreach ($cases as $case) {
            $this->assertSame($case[1], Base::treeList($case[0], function ($item) {
                return '<a>'.$item['name'].'</a>';
            }));
        }
    }

    public function testFlatNav()
    {
        $array = [
            '/' => 'foo',
            '/bar' => 'baz',
        ];

        $html = '<ul>'.
            '<li class="active"><a href="/">foo</a></li>'.
            '<li><a href="/bar">baz</a></li>'.
            '</ul>';

        $this->assertSame($html, Base::flatNav($array, '/'));
    }
}
