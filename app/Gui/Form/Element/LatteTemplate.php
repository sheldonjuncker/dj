<?php

namespace App\Gui\Form\Element;

use Latte\Engine;

class LatteTemplate extends Element
{
	protected $template;
	protected $parameters;

	public function __construct(string $template, array $parameters = [])
	{
		$this->template = $template;
		$this->parameters = $parameters;
	}

	public function render(bool $return = false): string
	{
		$latte = new Engine();
		if($return)
		{
			return $latte->renderToString($this->template, $this->parameters);
		}
		else
		{
			$latte->render($this->template, $this->parameters);
			return '';
		}
	}
}