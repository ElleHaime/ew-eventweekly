<?php 

namespace Objects;

use Core\Model,
	Core\Utils as _U;

class CampaignContact extends Model
{
	public $id;
	public $campaign_id;
	public $contact_type;
	public $contact_value;

	public $contactTypes = array(
							'email' => 'Email',
							'icq' => 'ICQ',
							'skype' => 'Skype',
							'phone' => 'Phone'
						);

	public function initialize()
	{
		parent::initialize();
		
		$this -> belongsTo('campaign_id', '\Object\Campaign', 'id', array('alias' => 'campaign'));
	}
}
