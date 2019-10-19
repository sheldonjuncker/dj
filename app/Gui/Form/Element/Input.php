<?php


namespace App\Gui\Form\Element;


use App\Gui\Form\Sorcerer;

abstract class Input extends Element
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


}