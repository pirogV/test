<?php

namespace controllers;

use common\Html;
use common\Controller;
use models\DiscountCard;
use config\Registry;
use common\Validate;

class DiscountController extends Controller
{

	public function indexAction ()
	{
		$model = new DiscountCard ();

		if (Validate::isValid($model->rulesSearch(), $_POST)) {
			$model->getAllCard ();
			if (!$model->allCard['cards']) {
				$this->render ('form', $model);
				$this->render ('emptyCard');
			} else {
				$model->pagination ();
				$this->render ('form', $model);
				$this->render ('allCard', $model);
			}
		} else {
			$this->render ('form', $model);
			$this->render ('emptyCard');
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
			$this->render ('form', $model);
			$this->render ('emptyCard');
		} else {
			$this->render ('form', $model);
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
		
		Registry::set ('title', 'Дисконтные карточки');
		Registry::set ('description', 'Дисконтные карточки');

		if (!$model->card) {
			$this->render ('emptyCard');
			Registry::set ('head', 'Дисконтная карточка не найдена');
		} else {
			$this->toVar = $model->card;
			$this->render ('card', $model);

			Registry::set ('head', 'Дисконтная карточка #' . $this->toVar['id']);
		}
	}

	public function createAction ()
	{
		$model = new DiscountCard ();

		Registry::set ('title', 'Дисконтные карточки');
		Registry::set ('description', 'Дисконтные карточки');

		if (Validate::isValid($model->rulesCreate(), $_POST)) {
			$id = $model->createCard ();

			$model->getCard ($id);

			if (!$model->card) {
				$this->render ('emptyCard');
				Registry::set ('head', 'Дисконтная карточка не найдена');
			} else {
				$this->toVar = $model->card;
				$this->render ('card', $model);

				Registry::set ('head', 'Дисконтные карточки созданы (показана последняя)');
			}

		} else {
			Registry::set ('head', 'Создать дисконтную карточку');
			$this->render ('createForm', $model);
		}
	}
	
	public function deactivationAction ()
	{
		$model = new DiscountCard ();
		$res = $model->deactivationCard ();
		echo 'Деактивировано ' . $res . ' карт';
		
	}
}
