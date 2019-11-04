<?php


namespace App\Storm\Form;


use App\Storm\DataDefinition\DataFieldDefinition;
use App\Storm\Model\BaseModel;
use App\Storm\Model\Model;
use Nette\Http\Request;

abstract class FormModel extends BaseModel implements Model
{
	/** @var Request|null */
	protected $request;

	public function __construct(Request $request = NULL)
	{
		$this->request = $request;
		if($request)
		{
			$this->setFieldsFromRequest();
		}
	}

	public function setFieldsFromRequest()
	{
		$method = $this->request->getMethod();
		$dataArray = $method == 'GET' ? $this->request->getQuery() : $this->request->getPost();

		//Everything is prefixed with the model class name
		$dataArray = $dataArray[$this->getBaseName()] ?? [];

		$dataFields = $this->getDataDefinition();

		//Set get/post data
		foreach($dataArray as $key => $value)
		{
			if($field = $dataFields->getField($key))
			{
				$field->setValue($value, DataFieldDefinition::FORMAT_TYPE_FROM_UI);
			}
		}

		//Set files
		foreach(($this->request->getFiles()[$this->getBaseName()] ?? []) as $key => $file)
		{
			if($field = $dataFields->getField($key))
			{
				$field->setValue($file);
			}
		}
	}
}