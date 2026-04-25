<?php

namespace App\Helpers;

use Spatie\Html\Elements\Div;
use Spatie\Html\Elements\Button;
use Spatie\Html\Html;

class HtmlExtended extends Html
{
	// The Spatie docs say that the button element defaults to $type = 'button'
	// but it's actually default null, so this is to resolve that issue.
	public function button($contents = null, $type = 'button', $name = null) {
		return Button::create()->text($contents)->type($type)->nameIf($name, $name)->class('btn'); // ->class('btn') is added for CSS simplicity and is not part of the original Spatie function
	}

	// This yesNoRadio function comes directly from the Spatie docs.
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