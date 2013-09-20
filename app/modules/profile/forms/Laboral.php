<?php

class Profile_Form_Laboral extends Zend_Form{

	public function init(){
		$company=new Zend_Form_Element_Text('company');
		$company->removeDecorator('HtmlTag')->removeDecorator('Label');
		$company->setRequired(true)->addErrorMessage("Campo Obligatorio");
		$company->setAttrib("maxlength","30")->setAttrib("size","30");
		$company->setAttrib("tittle","Empresa");
		$company->setAttrib("class","form-control");

		$industry=new Zend_Form_Element_Text('industry');
		$industry->removeDecorator('HtmlTag')->removeDecorator('Label');
		$industry->setRequired(true)->addErrorMessage("Campo Obligatorio");
		$industry->setAttrib("maxlength","30")->setAttrib("size","30");
		$industry->setAttrib("tittle","Empresa");
		$industry->setAttrib("class","form-control");

		$salary=new Zend_Form_Element_Text('salary');
		$salary->removeDecorator('HtmlTag')->removeDecorator('Label');
		$salary->setRequired(true)->addErrorMessage("Campo Obligatorio");
		$salary->setAttrib("maxlength","7")->setAttrib("size","30");
		$salary->setAttrib("tittle","Salario");
		$salary->setAttrib("class","form-control");

		$condition=new Zend_Form_Element_Text('condition');
		$condition->removeDecorator('HtmlTag')->removeDecorator('Label');
		$condition->setRequired(true)->addErrorMessage("Campo Obligatorio");
		$condition->setAttrib("maxlength","7")->setAttrib("size","30");
		$condition->setAttrib("tittle","Condición");
		$condition->setAttrib("class","form-control");

		$submit=new Zend_Form_Element_Submit('submit');
		$submit->setAttrib("class","btn btn-info pull-right");
		$submit->setLabel("Guardar");
		$submit->removeDecorator('HtmlTag')->removeDecorator('Label');

		$this->addElements(array($company, $industry, $salary, $condition, $submit));

	}

}