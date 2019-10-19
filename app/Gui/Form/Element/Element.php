<?php


namespace App\Gui\Form\Element;

use App\Gui\Form\Sorcerer;

/**
 * Class Element
 *
 * These are the elements that the Sorcerer can work with.
 * Normally you'd have things like, earth, fire, air, and water,
 * but here things are slightly more mundane and there will be
 * inputs, textareas, dropdowns, and buttons.
 *
 * @package App\Gui\Form\Element
 */
abstract class Element
{
	/** @var  Sorcerer $form */
	protected $form;

	/**
	 * @param Sorcerer $form
	 */
	public function setForm(Sorcerer $form)
	{
		$this->form = $form;
	}

	/**
	 * Returns or renders the HTML for the element.
	 *
	 * @param bool $return If set to true, returns output as string instead of printing.
	 * @return string Either HTML if $return is true or ''.
	 */
	abstract public function render(bool $return = false): string;
}