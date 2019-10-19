<?php


namespace App\Gui\Form\Element;

/**
 * Class WithLabel
 *
 * Combines a label with a pre-existing element.
 *
 * @package App\Gui\Form\Element
 */
class WithLabel extends Element
{
	/** @var string $label */
	protected $label;

	/** @var Element $element */
	protected $element;

	public function __construct(string $label, Element $element)
	{
		$this->label = $label;
		$this->element = $element;
	}

	public function render(bool $return = false): string
	{
		$label = new Tag('label', $this->label);
		$labelWithElement = $label->render(true) . $this->element->render(true);

		if($return)
		{
			return $labelWithElement;
		}
		else
		{
			print $labelWithElement;
			return '';
		}
	}
}