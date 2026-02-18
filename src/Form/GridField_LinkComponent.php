<?php

declare(strict_types=1);

namespace SilverShop\Discounts\Form;

use SilverStripe\Forms\GridField\GridField_HTMLProvider;

class GridField_LinkComponent implements GridField_HTMLProvider
{
    protected $title;

    protected $url;

    protected $extraclasses;

    public function __construct($title, $url)
    {
        $this->title = $title;
        $this->url = $url;
    }

    public function getHTMLFragments($gridField)
    {
        return [
            'before' => sprintf('<a href="%s" class="ss-ui-button %s">%s</a>', $this->url, $this->extraclasses, $this->title)
        ];
    }

    public function addExtraClass($classes): void
    {
        $this->extraclasses = $classes;
    }
}
