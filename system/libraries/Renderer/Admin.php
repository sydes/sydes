<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace App\Renderer;

use App\Document;

class Admin extends Renderer
{

    /**
     * @param Document $document
     * @return string
     */
    public function render(Document $doc)
    {
        //$siteName = app('config')['site']['name'];
        //$doc->title .= ' '.$siteName.' @ SyDES'; TODO uncomment
        $this->prepare($doc);

        $this->fillHead();

        $this->fillFooter();

        $dummy = [
            'language' => app('contentLang'),
            'head'     => implode("\n    ", $this->head),
            'footer'   => implode("\n    ", $this->footer),
            //'context_menu' => $this->site ? $this->getContextMenu() : '',
            'meta_title' => '',
            //'token' => $this->user->token,
            'menu_pos' => isset(app('request')->cookie['menu_pos']) ? 'left' : 'top',
            //'site_name' => $siteName,
            //'page_types' => $this->site ? $this->getPagesList() : '',
            //'modules' => $this->site ? $this->getModuleList() : '',
            //'menu_sections' => $this->site ? $this->getMenuSections() : array(),
            'base' => app('base'),
            'breadcrumbs' => '',
            'form_url' => '',
            'sidebar_left' => '',
            'content' => '',
            'sidebar_right' => '',
            'footer_left' => '',
            'footer_center' => '',
            'skin' => ifsetor(app('request')->cookie['skin'], 'black'),
            'col_sm' => 12,
            'col_lg' => 12,
        ];
        if (!empty($this->document->data['sidebar_left'])){
            $dummy['col_sm'] = $dummy['col_sm']-3;
            $dummy['col_lg'] = $dummy['col_lg']-2;
        }
        if (!empty($this->document->data['sidebar_right'])){
            $dummy['col_sm'] = $dummy['col_sm']-3;
            $dummy['col_lg'] = $dummy['col_lg']-2;
        }

        return render(DIR_SYSTEM.'/views/main.php', array_merge($dummy, $doc->data));
    }

}
