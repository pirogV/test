<?php
namespace controllers;
/** 
* CatalogController extends Controller
* контроллер для каталога
* @author Victior Pirog <pirog.v@gmail.com>
* @copyright 2015-2016 monchul.com
*/
use common\Html;
use common\Controller;
use models\DiscountCard;
use config\Registry;

class DiscountController extends Controller
{

	/**
	* indexAction()
	* Действие контролера каталога по умолчанию
	* кешируется
	* устанавливаем дефолтный uid для стартовой страницы
	* извлекаем инфу по текущему расделу
	* если непопадание - 404
	* звлекаем каталог из кеша или базы
	* вызывается без параметров
	* @return void
	*/
	public function indexAction ()
	{
		$model = new DiscountCard ();

		$model->getAllCard ();
		
		if (!$model->allCard['cards']) {
			$this->render ('emptyCard');
		} else {
			if (!Registry::get('ajax')) $this->render ('form', $model);
			$model->pagination ();
			$this->render ('allCard', $model);
		}

		Registry::set ('title', 'Дисконтные карточки');
		Registry::set ('description', 'Дисконтные карточки');
		Registry::set ('head', 'Дисконтные карточки');

	}
	
	public function statusAction ()
	{
		$model = new DiscountCard ();

		$newStatus = $model->updateStatus (Registry::get('get', 0), Registry::get('get', 1));
		echo $model->getStatusLink ($newStatus, Registry::get('get', 1));
	}
	
	public function deleteAction ()
	{
		$model = new DiscountCard ();

		$model->deleteCard (Registry::get('get', 1));

		$model->getAllCard ();
		$model->pagination ();

		if (!$model->allCard) {
			$this->render ('emptyCard');
		} else {
			$this->render ('allCard', $model);
		}

		Registry::set ('title', 'Дисконтные карточки');
		Registry::set ('description', 'Дисконтные карточки');
		Registry::set ('head', 'Дисконтные карточки');
	}

	public function viewAction ()
	{
		$model = new DiscountCard ();

		$model->getCard (Registry::get('get', 0));
		
		if (!$model->card) {
			$this->render ('emptyCard');
		} else {
			$this->toVar = $model->card;
			$this->render ('card', $model);
		}

		Registry::set ('title', 'Дисконтные карточки');
		Registry::set ('description', 'Дисконтные карточки');
		Registry::set ('head', 'Дисконтные карточки');
	}
}
