<?php

class Parser extends \Phalcon\Mvc\User\Component
{
  public function parseEventMembers($data)
  {
    foreach ($data as $key => $item)
    {
      if ($item['name']=='event_member')
        $statuses = $item['fql_result_set'];
      if ($item['name']=='friends_info')
        $members = $item['fql_result_set'];
    }

    if (empty($statuses))
      return FALSE;

    foreach ($members as $key => $member)
    {
      foreach ($statuses as $key => $status)
      {
        if ($member['uid'] == $status['uid'])
        {
          $result[$status['rsvp_status']][] = $member;
          break;
        }
      }
    }

    return $result;
  }
}