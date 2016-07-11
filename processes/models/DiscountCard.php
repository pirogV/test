<?php

namespace models;

use config\Registry;
use common\Db;
use common\Html;

class DiscountCard
{

	public $status = ['Неактивный', 'Активный', 'С истекшим сроком'];
	public $expiration_date = [1 => '1 месяц', 6 => '6 месяцев', 12 => '1 год'];

	public $allCard;
	public $card;

	private $where = '';
	private $whereArr = [];

	private $start;
	private $limit = 25;
	private $page;
	public $urlPageCount;

	public function __construct()
	{
		Registry::get('get', 2) === null ? $this->urlPageCount = 1 : $this->urlPageCount = Registry::get('get', 2);
		$this->page = $this->urlPageCount - 1;
		$this->start = $this->page * $this->limit;
	}
	
	public function rulesCreate()
    {
        return [
            ['series', 'series', 3, 3, 'required'],
			['number', 'number', 1, 100, 'required'],
			['expiration_date', 'entry', '1|6|12', 'required']
        ];
    }
	
	public function rulesSearch()
    {
        return [
            ['series', 'series', 3, 3, ''],
            ['id', 'number', 1, 10000000000, ''],
			['number', 'number', 1, 100, ''],
			['issue_date', 'date', ''],
			['expiration_date', 'entry', '1|6|12', ''],
			['status', 'entry', '0|1|2', '']
        ];
    }
	
	public function where ()
	{
		$str = '';

		if(!empty(Registry::get('post', 'series'))) {
			$str .= ' AND series = :series';
			$this->whereArr['str:series'] = Registry::get('post', 'series');
		}

		if(!empty(Registry::get('post', 'id'))) {
			$str .= ' AND id = :id';
			$this->whereArr['int:id'] = Registry::get('post', 'id');
		}
		
		if(!empty(Registry::get('post', 'issue_date'))) {
			$str .= ' AND issue_date BETWEEN :issue_date1 AND :issue_date2';
			$this->whereArr['str:issue_date1'] = Registry::get('post', 'issue_date') . ' 00:00:00';
			$this->whereArr['str:issue_date2'] = Registry::get('post', 'issue_date') . ' 23:59:59';
		}

		if(!empty(Registry::get('post', 'expiration_date'))) {
			$str .= ' AND expiration_date IN (:expiration_date)';
			$this->whereArr['in:expiration_date'] = Registry::get('post', 'expiration_date');
		}

		if(!empty(Registry::get('post', 'status'))) {
			$str .= ' AND status IN (:status)';
			$this->whereArr['in:status'] = Registry::get('post', 'status');
		}

		if ($str != '') $str = ' WHERE ' . trim($str, ' AND');
		$this->where = $str;
	}

	public function getAllCard ()
	{
		$this->where();
		$arr = ['int:limitstart' => $this->start] + $this->whereArr;
		$this->allCard['cards'] = Db::mysql()
			->query('SELECT * FROM card' . $this->where . ' LIMIT :limitstart, ' . $this->limit . '')
			->arr($arr)
			->all();
	}

	public function getCard ($id)
	{
		$this->card = Db::mysql()
			->query('SELECT `card`.`id`, `series`, `issue_date`, `expiration_date`, `status`,
					SUM(`price` * `count`) AS sum_price,
					MAX(`date`) AS last_date
				FROM `card` LEFT JOIN `order`
				ON `card`.`id` = `order`.`id_card`
				WHERE `card`.`id` = :id
				GROUP BY `id_card`')
			->arr(['int:id' => $id])
			->one();
			
		if (!$this->card) return null;
		
		if (!$this->card['last_date']) {
			$this->card['sum_price'] = 0.00;
			$this->card['last_date'] = 'Не использовалась';
			$this->card['history'] = [];
		} else {
			$this->card['history'] = Db::mysql()
			->query('SELECT * FROM `order` WHERE `id_card` = :id')
			->arr(['int:id' => $id])
			->all();
		}
	}

	public function getStatusLink ($status, $id)
	{
		$class = ['yellow', 'green'];
		
		if ($status != 2) {
			return '<a href="/discount/status/' . $status . '/' . $id . '" box="status-' . $id . '" class="ajax ' .$class[$status]  . '">' . $this->status[$status] . '</a>';
		} else {
			return '<span class="red">' . $this->status[$status] . '</span>';
		}
	}

	public function updateStatus ($status, $id)
	{
		$status == 0 ? $newStatus = 1 : $newStatus = 0;
		
		$r = Db::mysql()
			->query('UPDATE card SET :set WHERE id = :id')
			->arr(['set:set' => ['status' => $newStatus], 'int:id' => $id])
			->cud();
			
		if ($r > 0) return $newStatus;
		return $status;
	}

	public function createCard ()
	{
		$i = 0;
		$str = '';

		while ($i < Registry::get('post', 'number')){
			$str .= '(:series, :expiration_date),';
			$i++;
		}

		return Db::mysql()
			->query('INSERT INTO `card` (`series`, `expiration_date`) VALUES ' . trim($str, ','))
			->arr(['str:series' => Registry::get('post', 'series'), 'int:expiration_date' => Registry::get('post', 'expiration_date') ])
			->cud('id');
	}

	public function deleteCard ($id)
	{
		Db::mysql()
			->query('DELETE FROM card WHERE  id = :id')
			->arr(['int:id' => $id])
			->cud();
	}
	
	public function pagination()
	{
		$count = Db::mysql()
			->query('SELECT COUNT(*) FROM card' . $this->where)
			->arr($this->whereArr)
			->scalar();
			
		$countPages = ceil($count / $this->limit);
		
		$left = $this->urlPageCount - 4;
		if ($left < 1) $left = 1;
		
		$right = $this->urlPageCount + 4;
		if($right > $countPages) $right = $countPages;
		
		$html = '';
		while ($left <= $right) {
			$left == $this->urlPageCount ? $class = 'yellow' : $class = '';
			$html .= '<a class="ajax ' . $class . ' pagination" box="box-card" form="card" href="/discount/index/0/0/' . $left . '">' . $left . '</a>';
			$left++;
		}
		
		$this->allCard['pagination'] = $html;
	}
}
