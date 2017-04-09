<?php

function themeRenderAuthors($authors, $limit = 5)
{
    if (empty($authors)) {
        return '';
    }

    $other = count($authors) - $limit;
    $html = [];
    foreach ($authors as $author) {
        if (!empty($author['homepage'])) {
            $html[] = H::a($author['name'], $author['homepage'], ['target' => '_blank']);
        } else {
            $html[] = $author['name'];
        }

        if ($limit-- < 2) {
            break;
        }
    }

    return t('by').' '.implode(', ', $html).($other > 0 ? ' '.t('and_other', ['n' => $other]) : '');
}
