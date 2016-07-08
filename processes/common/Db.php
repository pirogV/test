<?php
namespace common;

use common\Html;

class Db
{
	
    /**
     * Параметры конекта 
     */
	private 		$host 		= '127.0.0.1';
	private 		$dbname 	= 'discount';
	private 		$user 		= 'root';
	private 		$password 	= '';

	/**
     * $instance - экземпляр этого класса
	 * $query - Запрос в базу
	 * $arr - масив с плейсхолдерами
	 * $result - результат запроса
	 * $mysql - ресурс конекта
     */
	private static 	$instance 	= null;
	private 		$query 		= '';
	private 		$arr		= [];
	private 		$result 	= null;
	private 		$mysql 		= null;

    /**
     * public static function mysql()
	 * Возвращает экземпляр себя
	 * Создает подключения
	 * очищает свойства от предыдущего запроса
     *
     * @return self::$instance instance
     */
    public static function mysql()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
			self::$instance->connect();
        }

		self::$instance->reset();

        return self::$instance;
    }

    /**
     * private function connect()
	 * Создает подключения
     *
     * @return void
     */
    private function connect()
    {
		$this->mysql = new \mysqli($this->host, $this->user, $this->password, $this->dbname);
		
		if ($this->mysql->connect_errno) {
			throw new \Exception ("Не удалось подключиться к MySQL: (" . $this->mysql->connect_errno . ") " . $this->mysql->connect_error);
		}
    }

    /**
     * private function reset()
	 * Сбрасывае значения от пред. запроса до дефолтных
     *
     * @return void
     */
    private function reset()
    {
		$this->result 	= null;
		$this->arr		= [];
		$this->query 	= '';
    }

    /**
     * public function query($str)
	 * устанавливает необработанный запрос
     *
	 * @param str $str 
     * @return obj $this
     */
	public function query($str)
    {
        $this->query = $str;
		return $this;
    }

    /**
     * public function arr($arr)
	 * устанавливает масив с плейсхолдерами
     *
	 * @param array $arr 
     * @return obj $this
     */
	public function arr($arr)
    {
        $this->arr = $arr;
		return $this;
    }

    /**
     * public function all()
	 * запрашивает парсинг строки запроса
	 * запрашивает данные с базы
	 * Возвращает многомерный масив результата
     *
     * @return array $this->getArr($result);
     */
	public function all()
    {
		$this->parseQery();
		$result = $this->sendRequest();
		
		if (!$result) {
			return null;
		}
		
		return $this->getArr($result);
    }

    /**
     * private function getArr($res)
	 * while ($r = mysql_fetch_assoc($res))
	 * Возвращает многомерный масив результата
     *
	 * @param resurs $res
     * @return array $ar
     */
	private function getArr($res)
    {
		$ar = null;
		while ($r = $res->fetch_assoc()) {
			$ar[] = $r;
		}
		return $ar;
    }

    /**
     * public function one()
	 * запрашивает парсинг строки запроса
	 * запрашивает данные с базы
	 * Возвращает одномерный масив результата
     *
     * @return array $result->fetch_assoc();
     */
	public function one()
    {
		$this->parseQery();
		$result = $this->sendRequest();

		if (!$result) {
			return null;
		}

		return $result->fetch_assoc();
    }

    /**
     * public function scalar()
	 * запрашивает парсинг строки запроса
	 * запрашивает данные с базы
	 * Возвращает склярный результат (первое поле первой строки)
     *
     * @return scalar $ar[0][0];
     */
	public function scalar()
    {
		$this->parseQery();
		$result = $this->sendRequest();
		
		if (!$result) {
			return null;
		}
		
		$ar = $result->fetch_array();
		if (is_array($ar[0])) return $ar[0][0];
		return $ar[0];
    }

    /**
     * public function cud($f = '')
	 * create, update, delete
	 * 
     * @param str $f id||affected

     * @return mixed $result
     */
	public function cud($f = '')
    {
		$this->parseQery();
		$result = $this->sendRequest();

		if ($f == 'id') {
			return $this->mysql->insert_id;
		} else if ($f == 'affected') {
			return $this->mysql->affected_rows;
		}

		return $result;
    }

    /**
     * public function sendRequest()
	 * Запрашивает данные в базе
     *
     * @return resurse $r
     */
	public function sendRequest()
    {
		//Html::p($this->query);
		$r = $this->mysql->query($this->query);

		if ($this->mysql->errno) {
			throw new \Exception('Ошибка выполнения запроса -=== (' . $this->mysql->errno . ') ===- ' . $this->mysql->error . ' -===- ' . $this->query . ' -===- ');
		}
		
		return $r;
    }

    /**
     * private function parseQery()
	 * распознает типы плейсхолдеров и запрвшивает эскейпинг вставляемых данный
     *
     * @return void
     */
	private function parseQery()
    {
		foreach ($this->arr as $k => $v) {
			$a = explode(':', $k);

			if (strpos($this->query, ':' . $a[1]) === false) {
				throw new \Exception('Ошибка создания запроса: имя плейсхолдера ( :' . $a[1] . ' ) не найдено в запросе<hr>' . $this->query);
			}

			switch ($a[0]) {
				case 'int':
					$this->escapeInt($a[1], $v);
					break;
				case 'str':
					$this->escapeStr($a[1], $v);
					break;
				case 'in':
					$this->createIn($a[1], $this->arr[$k]);
					break;
				case 'set':
					$this->createSet($a[1], $this->arr[$k]);
					break;
				case 'name':
					$this->escapeName($a[1], $v);
					break;
				default :
					throw new \Exception('Ошибка создания запроса: недопустимый тип плейсхолдера. Допустимые типы int|str|in|set|name<hr>' . $this->query);
					break;
			}
		}
    }

	/**
     * public function createSet($rep, $arr)
	 * Создает конструкцию SET (SQL)
	 * 
	 * @param str $rep метка в запросе
     * @param array $arr масив для SET
     * @return void
     */
	public function createSet($rep, $arr)
    {
		$str = '';
		
		if (!is_array($arr) or ($arr == [])) {
			throw new \Exception('Для конструкции SET( set:' . $rep . ' ) необходимо передать не пустой масив.<hr>' . $this->query);
		}
		
		foreach ($arr as $k => $v) {
			$str .= $k . " = '" . $this->mysql->real_escape_string($v) . "', ";
		}

		$this->query = str_replace(':' . $rep, rtrim($str, ', '), $this->query);

    }

    /**
     * private function escapeStr($rep, $val)
	 * эскейпит строку
     *
     * @return void
     */
	private function escapeStr($rep, $val)
    {
		$this->query = str_replace(':' . $rep, "'" . $this->mysql->real_escape_string($val) . "'", $this->query);
    }
	
    /**
     * private function escapeName($rep, $val)
	 * эскейпит идентификаторы (имена таблиц, полей...)
     *
     * @return void
     */
	private function escapeName($rep, $val)
    {
		$this->query = str_replace(':' . $rep, '`' . $val . '`', $this->query);
    }

    /**
     * private function escapeInt($rep, $val)
	 * эскейпит число
     *
     * @return void
     */
	private function escapeInt($rep, $val)
    {	
		$this->query = str_replace(':' . $rep, (int) $val, $this->query);
    }

	/**
     * public function createIN($rep, $arr)
	 * Создает конструкцию IN (SQL)
	 * 
	 * @param str $rep метка в запросе
     * @param array $arr масив для IN
     * @return $this->query
     */
	private function createIn($rep, $arr)
    {
		if (!is_array($arr) or $arr === []) {
			throw new \Exception('Для конструкции IN ( in:' . $rep . ' ) необходимо передать не пустой масив.<hr>' . $this->query);
		}
		
		$str = '';
		foreach ($arr as $v) {
			$str .= "'" . $this->mysql->real_escape_string($v) . "', ";
		}

		$this->query = str_replace(':' . $rep, rtrim($str, ', '), $this->query);
    }

    /**
     * public function viewDebug()
	 * для разработки. показывает необработаный и обработанный запрос и масив плейсхолдеров
	 * die()
     *
     * @return void
     */
	public function viewDebug()
	{
		echo '<pre><div style="border: 1px solid #CCCCCC; padding: 12px;"><div style="border: 1px solid #CCCCCC; padding: 12px;">' . $this->query . '</div>';

		$this->parseQery();

		echo '<div style="border: 1px solid #CCCCCC; padding: 12px;">' . $this->query . '</div>';
		echo '<div style="border: 1px solid #CCCCCC; padding: 12px;">';

		print_r ($this->arr);

		echo '</div>';
		echo '</div></pre>';
		die();
	}

    /**
     * Конструктор закрыт
     */
    private function __construct()
    {
    }

    /**
     * Клонирование запрещено
     */
    private function __clone()
    {
    }

}