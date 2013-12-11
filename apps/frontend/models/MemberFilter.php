<?php 

namespace Frontend\Models;

use Objects\MemberFilter as MemberFilterObject;

class MemberFilter extends MemberFilterObject
{

    protected function beforeSave ()
    {
        if (is_array($this->value)) {
            $this->value = json_encode($this->value);
        }
    }

    public static function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public function getById($id)
    {
        $return = array();
        if (!empty($id)) {
            $result = $this->find('member_id = '.$id)->toArray();
            foreach ($result as $node) {
                if (self::isJson($node['value'])) {
                    $return[$node['key']]['value'] = json_decode($node['value'], true);
                }
                $return[$node['key']]['id'] = $node['id'];
            }
        }
        return $return;
    }

} 