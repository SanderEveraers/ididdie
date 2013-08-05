<?php
/**
 * Static content controller.
 *
 * This file will render views from views/documentation/
 *
 * PHP 5
 *
 */
App::uses('AppController', 'Controller');

/**
 * Static content controller with automatic MarkDown conversion
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 */
class DocumentationController extends AppController {

	public $name = 'Documentation';
	public $helpers = array('Markdown.Markdown');
	public $components = array('Markdown.Markdown');

	public function display() {
		$path = func_get_args();

		$location = APP.'View'.DS.'Documentation'.DS;
		$this->set('textInMarkdownFormat', 
			$this->Markdown->getFile($location.implode('/', $path).'.md'));
	}
}
