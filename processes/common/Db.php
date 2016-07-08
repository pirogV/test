<?php
namespace common;
/**
* ПОЯСНЕНИЯ
*______________________________________________________________________________________________________
*
* Этот класс был написан для проэкта infocompany.biz
* 
* ВАЖНО!!!!!!!
*
* В КЛАСЕ РЕАЛИЗОВАН ТОЛЬЕО МИНИНИМАЛЬНО НАОБХОДИМЫЙ ФУНКЦИОНАЛ ДЛЯ ПРОЭКТА
* не надо искать здесь, например, pg_escape_bytea() поскольку проэкт как то обошелся без этого типа данных.
* по этой же причине не реализована удобная работа с типом array, не реализованы все возмиожности SQL и др.
*
* ЗАДАЧА
*
* Стояла задача написать легкий клас для безопасной работы с данными в postgres при этом он должен быть удобным в использовании
*
* КАК РАБОТАТЬ С КЛАСОМ
*
* Работа с класом похожа на рабрту с PDO, но в отличии от PDO этот клас использует типизированные, именованные плейсхолдеры,
* не использует подготовленные запросы, неименованные плейсхолдеры и т.д.
* Ниже некоторые запросы для понимания
*
* Оптимизицию запросов здесь не рассматриваем, только работу класса.
*
* 	$count = Db::mysql()->query('SELECT COUNT(*) FROM catalog')->scslsr();
*	
*	(в postgres такие запросы не пишут, знаю, это для примера)
*
* 	$result = Db::mysql()
*		->query('SELECT * FROM catalog WHERE id = :id AND view = \'Y\'') // Запос содержит метку :id куда клас подставит плейсхолдер
*		->arr(['int:id' => 1]) // int:id приведет значение к числовому типу(можно спорить, приводить к типу или показывать ошибку), проэскейпит и подставит значение на место :id в запросе
*		->one(); //вернет первую строку результата
*
* 	$result = Db::mysql()
*		->query('SELECT * FROM catalog WHERE id IN (:id) AND view = :view')
*		->arr(['str:view' => 'Y', 'in:id' => [1,2,3]])
*		->all();
*		отправит серверу как то так запрос: SELECT * FROM ru.catalog WHERE id IN (1, 2, 3) AND view = 'Y'
*		и ->all() вернет многомерный масив со строками
*
*	$result = Db::mysql()
*		->query('UPDATE catalog SET :sethere WHERE id = 1')
*		->arr(['set:sethere' => ['parent' => 0, 'sort' => 0, 'head' => 'pupuru', 'title' => 'Helo pupuru', 'view' => 'Y' ]])
*		->cud('id');
*		set:sethere сформирует конструкцию SET (SQL) тоесть parent=0, sort = 0, head = 'pupuru', title = 'Helo pupuru', view = 'Y'
*		->cud('id') вернет id затронутой строки, можно insertId при INSERT, все затронутые строки и т. д. см. RETURNING Postgres
*
* Все возможности описывать не буду, думаю понятно...
*
* ЧТО ИЗМЕНЕНО ПО СРАВНЕНИЮ С РАБОЧИМ (РЕАЛЬНЫМ) КЛАСОМ
*
* Параметры подключения перенес в класс, было в конфиге.
* Вывод ошибок тоже был завязан на другой класс, не мудрствуя лукаво переделал на die()
* Класс сделал полностью автономным, он покрывает, наверное, 99% потребностей среднего проэкта.
* Расширяем, если надо вернуть результат какой нить другой структурой - пишем метод не затрагивая паралельно работающие методы.
* Чтоб добавть новый тип плейсхолдера - добавляем в свич вызов метода и пишем метод где потставляем обработанные данные в сроку запроса.
*/
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
	 * $pg - ресурс конекта
     */
	private static 	$instance 	= null;
	private 		$query 		= '';
	private 		$arr		= [];
	private 		$result 	= null;
	private 		$mysql 		= null;

    /**
     * public static function pg()
	 * Возвращает экземпляр себя
	 * Создает подключения
	 * очищает свойства от предыдущего запроса
     *
     * @return self::$instance->instance
     */
    public static function mysql()// mysql///////////////////////////////////////////////////////////////////////////////////////////////////////
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
	 * while ($r = pg_fetch_assoc($res))
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
     * @return array $this->getRow($result);
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
     * @param str $f стобец или поля через запятею для возврата затронутых строк, insertId,  и т. д. (RETURNING)

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
     * @return $this->query
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
		$this->query = str_replace(':' . $rep, $val, $this->query);
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