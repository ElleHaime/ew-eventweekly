<?php

namespace Frontend\Component;

use \Core\Utils as _U,
	\Phalcon\Mvc\User\Component;

class FetchObject extends Component
{
	const FETCH_OBJECT = 1;
    const FETCH_ARRAY = 2;
    const ORDER_ASC = 3;
    const ORDER_DESC = 4;
    const CONDITION_SIMPLE = 5;
    const CONDITION_COMPLEX = 6;
    const DEF_FIELD = 'id';

    private $from = false;
    private $selector = ' AND';
    private $order;
    private $start;
    private $offset;
    private $fetchTytpe;
	private $conditions = [];
    private $defaultConditions = [
        [
            'type' => self::CONDITION_COMPLEX,
            'condition' => '\Frontend\Models\Event.deleted = 0'
        ]
    ];

	public function fetchRows()
	{
		try {
			$this -> getFrom();
		} catch (\Phalcon\Exception $e) {
			echo 'Fetch error: $from property is not defined';
		}

 		$builder = $this -> getModelsManager() -> createBuilder();
 		$builder -> from($this -> fromTable);
	}

    public function setFrom()
    {

    }

	public function setLimit($start = 0, $offset = false)
	{
		$this -> start = $start;
		if ($offset) {
			$this -> offset = $offset;	
		}
		return $this;
	}

	public function setCondition($condition, $type = self::CONDITION_COMPLEX)
	{
	    if (!empty($condition)) {
            $cond = [
                'type' => $type,
                'condition' => (string)$condition
            ];
	    	$this -> conditions[] = $cond;
	    }
	    return $this;
	}

	public function setOrder($order)
	{
        if (!empty($order)) {
            $this -> order = $order;
        }
        return $this;
	}

	protected function setFrom(\Frontend\Models $obj)
	{

	}

	public getFrom()
	{
		return $this -> from
	}

	public function setLeftJoin()
	{

	}
}
