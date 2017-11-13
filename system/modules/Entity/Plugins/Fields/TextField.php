<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Entity\Plugins\Fields;

use Sydes\Database\Entity\Field;
use Sydes\Database\Entity\Model;

class TextField extends Field
{
    protected $settings = [
        'rows' => 1,
        'translatable' => false,
    ];

    /**
     * Current locale
     *
     * @var string
     */
    private $locale;

    /**
     * Site locales
     *
     * @var array
     */
    private $locales = [];

    public function __construct($name, $value, array $settings = [])
    {
        $this->locales = Model::getLocales();
        parent::__construct($name, $value, $settings);
    }

    public function defaultInput()
    {
        if ($this->settings['translatable']) {
            $input = [];
            foreach ($this->locales as $locale) {
                $input[$locale] = [
                    'title' => $locale,
                    'content' => $this->getInput('['.$locale.']', ifsetor($this->value[$locale], '')),
                ];
            }

            $input = \H::tabs($input, reset($this->locales), ['class' => ['translation-tabs']]);
        } else {
            $input = $this->getInput('', $this->value);
        }

        return $input;
    }

    private function getInput($append, $value)
    {
        if ($this->settings('rows') == 1) {
            $attrs = [
                'required' => $this->settings['required'],
            ];

            if (isset($this->settings['attr'])) {
                $attrs += $this->settings['attr'];
            }

            return \H::textInput($this->name.$append, $value, $attrs);
        } else {
            return \H::textarea($this->name.$append, $value, [
                'required' => $this->settings['required'],
                'rows' => $this->settings['rows'],
            ]);
        }
    }

    protected function defaultOutput()
    {
        if (!$this->settings['translatable']) {
            return $this->value;
        }

        if ($this->locale && isset($this->value[$this->locale])) {
            return $this->value[$this->locale];
        }

        return '';
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }
}
