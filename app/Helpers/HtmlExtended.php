<?php

namespace App\Helpers;

use Spatie\Html\Elements\Div;
use Spatie\Html\Html;

class HtmlExtended extends Html
{
    public function yesNoRadio($name = null, $model = null)
    {
        return Div::create()->addChildren(
            Div::create()->class('form-check form-check-inline')
                ->addChildren($this->radio($name)->class('form-check-input')->id($name.'_yes')->value(1)->checked(old($name) === '1' || $model[$name] === 1))
                ->addChildren($this->label('Yes')->for($name.'_yes')->class('form-check-label'))
        )->addChildren(
            Div::create()->class('form-check form-check-inline')
                ->addChildren($this->radio($name)->class('form-check-input')->id($name.'_no')->value(0)->checked(old($name) === '0' || $model[$name] === 0))
                ->addChildren($this->label('No')->for($name.'_no')->class('form-check-label'))
        );
    }

    public function textareaBBC($name, $default, $data) {
        return view('components.bbcode_textarea', ['name' => $name, 'default' => $default, 'data' => $data]);
    }
}