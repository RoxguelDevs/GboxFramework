<?php
namespace Gbox\base;
use Gbox;
use Gbox\exceptions\OrmException;
abstract class Orm implements \ArrayAccess
{
	private $mysqli;
	private $config;
	private $as_array = false;
	private $action; // insert, select, update, delete
	private $array_tmp = [];
	private $query_params = [
		'fields' => ['*'],
		'join' => [],
		'where' => '',
		'having' => '',
		'limit' => '',
		'offset' => '',
		'order' => '',
	];

	public function __construct ()
	{

	}

	public static function className ()
	{
		return get_called_class();
	}

	public static function find()
	{
		return Gbox::createObject(static::className(), [get_called_class()]);
	}

	protected function connect ($config)
	{
		$this->config = $config;
		if (!$this->mysqli)
		{
			$this->mysqli = new \MySQLi($this->config['host'], $this->config['user'], $this->config['pass'], $this->config['base']);
			/*=============================
			=            DEBUG            =
			=============================*/
			if (\Gbox::$components->debug)
			\Gbox::$components->debug->saveReportOrm([
				'orm' => static::className(),
				'message' => 'Connected.',
				'subtype' => 'connected',
			]);
			/*=====  End of DEBUG  ======*/
		}
		if(!$this->mysqli->connect_errno)
		{
			$this->mysqli->set_charset('utf8');
			$this->mysqli->query('SET time_zone = "-03:00"');
		}
		else
		{
			/*=============================
			=            DEBUG            =
			=============================*/
			if (\Gbox::$components->debug)
			\Gbox::$components->debug->saveReportOrm([
				'orm' => static::className(),
				'message' => 'Not connected.',
				'subtype' => 'connected',
			]);
			/*=====  End of DEBUG  ======*/
			throw new OrmException('ORM misconfigured.');
		}
	}

	private function getConfig ()
	{
		return $this->config;
	}

	private function _select ()
	{
		$this->action = 'select';
		$query = 'SELECT ' . implode(', ', $this->query_params['fields']) . ' FROM ' . $this->tableName()
           . (count($this->query_params['join']) != 0 ? ' ' . implode(' ', $this->query_params['join']) : '')
           . (($this->query_params['where']) ? ' WHERE ' . $this->query_params['where'] : '')
           . (($this->query_params['having']) ? ' HAVING ' . $this->query_params['having'] : '')
           . (!empty($this->query_params['order']) ? ' ORDER BY ' . $this->query_params['order'] : '')
           . (!empty($this->query_params['limit']) ? ' LIMIT ' . $this->query_params['limit'] : '')
           . (!empty($this->query_params['offset']) && !empty($this->query_params['limit']) ? ' OFFSET ' . $this->query_params['offset'] : '');
        return $this->query($query);
	}

	private function _update ($id)
	{
		$this->action = 'update';
		$sets = [];
		foreach ($this->beforeSave() as $index => $value)
		{ 
			$sets[] = '`' . $index . '`' . ' = ' . $this->quoteValue($value);
		}
		if (is_array($id))
		{
			$keys = [];
			foreach ($id as $idKey => $idValue)
			{
				$keys[] = '`' . $idKey . '`' . ' = ' . $idValue;
			}
			$query = 'UPDATE ' . $this->tableName() . ' SET ' . implode(', ', $sets) . ' WHERE ' . implode(' AND ', $keys);
		}
		else
		{
			$query = 'UPDATE ' . $this->tableName() . ' SET ' . implode(', ', $sets) . ' WHERE `' . $this->key() . '` = ' . (is_null($id) ? $this->{$this->key()} : $id);
		}
        return $this->query($query);
	}

	private function _insert ()
	{
		$this->action = 'insert';
		$fields = [];
		$values = [];
		foreach ($this->beforeSave() as $index => $value)
		{ 
			$fields[] = '`' . $index . '`';
		}
		foreach ($this->beforeSave() as $index => $value)
		{ 
			$values[] = $this->quoteValue($value);
		}
		$query = 'INSERT INTO ' . $this->tableName() . '(' . implode(', ', $fields) . ') VALUES(' . implode(', ', $values) . ')';
        return $this->query($query);
	}

	private function _delete ()
	{
		$this->action = 'delete';
		$tmp_where = $this->where();
		if (!empty($tmp_where))
		{
			$query = 'DELETE FROM ' . $this->tableName() . ' WHERE ' . $this->where();
		}
		else
		{
			$query = 'DELETE FROM ' . $this->tableName() . ' WHERE `' . $this->key() . '` = ' . (is_null($id) ? $this->{$this->key()} : $id);
		}
        return $this->query($query);
	}

	public function query ($query)
	{
		$db = $this->getDb();
		$this->connect($db);
		
		/*=============================
		=            DEBUG            =
		=============================*/
		if (\Gbox::$components->debug)
		\Gbox::$components->debug->saveReportOrm([
			'orm' => static::className(),
			'message' => 'Before query.',
			'subtype' => 'query',
			'action' => $this->action,
			'query' => $query,
		]);
		/*=====  End of DEBUG  ======*/
		$result = $this->mysqli->multi_query($query);
		switch ($this->action)
		{
			case 'select':
				$data_as_array = [];
				if (!$data = $this->mysqli->use_result())
				{
					throw new OrmException("Error al recuperar los resultados");
				}
				while ($row = $data->fetch_assoc())
				{
					if ($this->as_array)
					{
						array_push($data_as_array, $row);
					}
					else
					{
						$tmp = new $this;
						$tmp->load_variables($row);
						array_push($data_as_array, $tmp);
					}
				}
				$this->array_tmp = $data_as_array;
				return $data_as_array;
				break;
			case 'update':
				return $result;
				break;
			case 'insert':
				return $this->mysqli->insert_id;
				break;
			case 'delete':
				return $result;
				break;
		}
	}

	public function all ()
	{
		return $this->_select();
	}

	public function update ($id = null)
	{
		return $this->_update($id);
	}

	public function insert ()
	{
		return $this->_insert();
	}

	public function delete ()
	{
		return $this->_delete();
	}

	public function count ()
	{
		return count($this->all());
	}

	public function one ($id = null)
	{
		if (!is_null($id))
		{
			$this->andWhere($this->key(), $id);
		}
		$this->limit(1);
		$result = $this->_select();
		if (count($result) == 0)
		{
			return NULL;
		}
		else
		{
			return $result[0];
		}
	}

	public function limit ($limit)
	{
		$this->query_params['limit'] = $limit;
		return $this;
	}

	public function asArray ($as_array = true)
	{
		$this->as_array = $as_array;
		return $this;
	}

	public function offset ($offset)
	{
		$this->query_params['offset'] = $offset;
		return $this;
	}

	public function orderBy ($field, $way = null)
	{
		$this->query_params['order'] = $field;
		if (in_array(strtolower($way), ['asc', 'desc']))
		{
			$this->query_params['order'] .= ' ' . strtoupper($way);
		}
		return $this;
	}

	public function select ($fields)
	{
		$tmp_fields = [];
		foreach ($fields as $field => $name)
		{
			if (!is_numeric($field))
			{
				$tmp_fields[] = $field . ' AS ' . $name;
			}
			else
			{
				$tmp_fields[] = $name;
			}
		}
		$this->query_params['fields'] = $tmp_fields;
		return $this;
	}

	public function joinWith ($actions)
	{
		foreach ($actions as $action)
		{
			call_user_func([$this, 'get' . ucfirst($action)]);
		}
		return $this;
	}

	public function hasOne ($class, $links)
	{
		$on = [];
		foreach ($links as $key => $value)
		{
			$tmp_class = new $class;
			$tmp_on = $this->tableName() . '.`' . $key . '` = ' . $tmp_class->tableName() . '.`' . $value . '`';
			array_push($on, $tmp_on);
			$tmp_inner = 'INNER JOIN ' . $tmp_class->tableName() . ' ON (' . implode(' AND ', $on) . ')';
			array_push($this->query_params['join'], $tmp_inner);
		}
		return $this;
	}

	public function hasMany ($class, $links)
	{
		$on = [];
		foreach ($links as $key => $value)
		{
			$tmp_class = new $class;
			$tmp_on = $this->tableName() . '.`' . $key . '` = ' . $tmp_class->tableName() . '.`' . $value . '`';
			array_push($on, $tmp_on);
			$tmp_inner = 'LEFT JOIN ' . $tmp_class->tableName() . ' ON (' . implode(' AND ', $on) . ')';
			array_push($this->query_params['join'], $tmp_inner);
		}
		return $this;
	}

	public function where ($where = null, $p_value = null, $p_operation = '=')
	{
		if ($where)
		{
			if (!is_null($p_value) || $p_operation != '=')
			{
				$this->andWhere($where, $p_value, $p_operation);
			}
			else
			{
				$this->query_params['where'] = $where;
			}
			return $this;
		}
		else
		{
			return $this->query_params['where'];
		}
	}

	public function andWhere ($field, $value = null, $operation = '=')
	{
		if (is_null($value) && $operation == '=')
		{
			$where = $field;
		}
		else
		{
			$where = self::constructOperation($field, $value, $operation);
		}
		if ($this->where() == '')
		{
			$where = $where;
		}
		else
		{
			$where = '(' . $this->where() . ' AND ' . $where . ')';
		}
		$this->where($where);
		return $this;
	}

	public function orWhere ($field, $value = null, $operation = '=')
	{
		if (is_null($value) && $operation == '=')
		{
			$where = $field;
		}
		else
		{
			$where = self::constructOperation($field, $value, $operation);
		}
		if ($this->where() == '')
		{
			$where = $where;
		}
		else
		{
			$where = '(' . $this->where() . ' OR ' . $where . ')';
		}
		$this->where($where);
		return $this;
	}

	public function having ($where = null, $p_value = null, $p_operation = '=')
	{
		if ($where)
		{
			if (!is_null($p_value) || $p_operation != '=')
			{
				$this->andHaving($where, $p_value, $p_operation);
			}
			else
			{
				$this->query_params['having'] = $where;
			}
			return $this;
		}
		else
		{
			return $this->query_params['having'];
		}
	}

	public function andHaving ($field, $value = null, $operation = '=')
	{
		if (is_null($value) && $operation == '=')
		{
			$where = $field;
		}
		else
		{
			$where = self::constructOperation($field, $value, $operation);
		}
		if ($this->having() == '')
		{
			$where = $where;
		}
		else
		{
			$where = '(' . $this->having() . ' AND ' . $where . ')';
		}
		$this->having($where);
		return $this;
	}

	public function orHaving ($field, $value = null, $operation = '=')
	{
		if (is_null($value) && $operation == '=')
		{
			$where = $field;
		}
		else
		{
			$where = self::constructOperation($field, $value, $operation);
		}
		if ($this->having() == '')
		{
			$where = $where;
		}
		else
		{
			$where = '(' . $this->having() . ' OR ' . $where . ')';
		}
		$this->having($where);
		return $this;
	}

	private function constructOperation ($field, $value, $operation)
	{
		if (is_array($field))
		{
			$field = $field[0] . '.`' . $field[1] . '`';
			// $field = $field[0] . ' AS ' . $field[1];
		}
		else
		{
			$field = '`' . $field . '`';
		}
		$where = '';
		switch (strtolower($operation))
		{
			case '>':
			case '>=':
			case '<':
			case '<=':
			case '=':
			case '<>':
				if (is_string($value))
				{
					$where = $field . ' ' . $operation . ' ' . '"' . $value . '"';
				}
				else if (is_numeric($value))
				{
					$where = $field . ' ' . $operation . ' ' . $value;
				}
				break;
			case 'like':
				$where = $field . ' LIKE ' . '"%' . $value . '%"';
				break;
			case 'is null':
			case 'is not null':
				$where = $field . $operation;
				break;
		}
		return $where;
	}

	private function quoteValue ($value)
    {
        if ($value === null)
        {
            $value = 'NULL';
        }
        else if (!is_numeric($value))
        {
            //$value = "'" . mysqli_real_escape_string($this->mysqli, $value) . "'";
            $value = "'" . $value . "'";
        }
        return $value;
    }

	protected function load_variables ($data)
	{
		if (!is_array($data))
			return false;
		foreach ($data as $key => $value)
		{
			$this->{$key} = $value;
		}
	}

	public function beforeSave ()
	{
		$columns = [];
		$data = [];
		$columns_tmp = $this->getColumns();
		foreach ($columns_tmp as $column_tmp)
		{
			if (property_exists($this, $column_tmp))
				array_push($columns, $column_tmp);
		}
		foreach ($columns as $column)
		{
			$data[$column] = $this->{$column};
		}
		return $data;
	}

    public function getColumns ()
    {
    	$db = $this->getDb();
		$this->connect($db);
		
    	$sql = 'SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`="' . $this->getConfig()['base'] . '" AND `TABLE_NAME`="' . $this->tableName() . '"';
    	$result = $this->mysqli->multi_query($sql);
		$pre_columns = $this->mysqli->use_result();
    	$columns = [];
    	while ($column = $pre_columns->fetch_assoc())
    	{	
    		array_push($columns, $column['COLUMN_NAME']);
    	}
    	return $columns;
    }

    public function tableName ()
    {
        return strtolower(get_class($this));
    }

    public function key ()
    {
        return 'id';
    }

	protected function disconect ()
	{
		if ($this->mysqli/* && $this->mysqli->ping()*/)
		{
			$this->mysqli->close();
		}
	}

	public function __destruct ()
	{
		$this->disconect();
	}

	public function offsetExists ($offset)
	{
		return isset($this->array_tmp[$offset]);
	}
	public function offsetGet ($offset)
	{
		return isset($this->array_tmp[$offset]) ? $this->array_tmp[$offset] : null;
	}
	public function offsetSet ($offset, $value)
	{
		if (is_null($offset))
		{
			$this->array_tmp[] = $value;
		}
		else
		{
			$this->array_tmp[$offset] = $value;
		}
	}
	public function offsetUnset ($offset)
	{
		unset($this->array_tmp[$offset]);
	}

}