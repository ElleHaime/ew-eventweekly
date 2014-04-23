<?php 

namespace Objects;

use Core\Model;

class MemberFilter extends Model
{
	public $id;

	public $member_id;

    public $key;

    public $value;

    public function initialize()
    {
		parent::initialize();
		    	
        $this->belongsTo('member_id', '\Objects\Member', 'id', array('alias' => 'member_filter'));
    }
}