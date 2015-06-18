<?php 

namespace Objects;

use Core\Model,
	Core\Utils as _U,
	Objects\Member,
	Phalcon\Mvc\Model\Validator\Uniqueness;

class MemberNetwork extends Model
{
	const FACEBOOK		= 1;

	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 */
	public $id;
	
	/**
	 * @Column(type="integer")
	 */
	public $member_id;
	
	/**
	 * @Column(type="integer")
	 */
	public $network_id;

	/**
	 * @Column(type="varchar", nullable=false, length=30)
	 */
	public $account_uid;
	
	/**
	 * @Column(type="varchar", nullable=false, length=50)
	 */
	public $account_id;
	
	/**
	 * @Column(type="integer")
	 */
	public $permission_base;
	
	/**
	 * @Column(type="integer")
	 */
	public $permission_publish;
	
	/**
	 * @Column(type="integer")
	 */
	public $permission_manage;
	
	
	public function initialize()
	{
		parent::initialize();
				
		$this -> useDynamicUpdate(true);
		
		$this -> hasOne('member_id', '\Objects\Member', 'id', ['alias' => 'member']);
		//$this -> hasOne('network_id', '\Objects\Network', 'id', ['alias' => 'network']);
	}
}