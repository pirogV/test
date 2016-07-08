<?php
/**
* CatalogModel extends Catalog
* модель каталога
* @author Victior Pirog <pirog.v@gmail.com>
* @copyright 2015-2016 monchul.com
*/
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
	
	public function rules()
    {
        return [
            ['series', 'series'],
            ['number', 'number'],
			['issue_date', 'date'],
			['expiration_date', 'entry', '1|6|12'],
			['status', 'entry', '0|1|2'],
        ];
    }
	
	public function where ()
	{
		$str = '';
		
		if(!empty($_POST['series'])) {
			$str .= ' AND series = :series';
			$this->whereArr['str:series'] = $_POST['series'];
		}
		
		if(!empty($_POST['number'])) {
			$str .= ' AND number = :number';
			$this->whereArr['int:number'] = $_POST['number'];
		}
		
		if(!empty($_POST['issue_date'])) {
			$str .= ' AND issue_date BETWEEN :issue_date1 AND :issue_date2';
			$this->whereArr['str:issue_date1'] = $_POST['issue_date'] . ' 00:00:00';
			$this->whereArr['str:issue_date2'] = $_POST['issue_date'] . ' 23:59:59';
		}

		if(!empty($_POST['expiration_date'])) {
			$str .= ' AND expiration_date IN (:expiration_date)';
			$this->whereArr['in:expiration_date'] = $_POST['expiration_date'];
		}

		if(!empty($_POST['status'])) {
			$str .= ' AND status IN (:status)';
			$this->whereArr['in:status'] = $_POST['status'];
		}

		if ($str != '') $str = ' WHERE ' . trim($str, ' AND');
		$this->where = $str;
	}

	/**
	* setInfo ($uid)
	* устанавливаем инфу для текущего каталога
	* @param string $uid alias
	* @return array $info
	*/
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
			->query('SELECT * FROM card WHERE id = :id')
			->arr(['int:id' => $id])
			->one();
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
