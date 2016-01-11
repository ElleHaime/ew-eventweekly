<?php

namespace Core\Traits;

use Core\Utils as _U,
    Frontend\Models\Featured,
    Frontend\Models\Event,
    Frontend\Models\EventImage,
    Frontend\Models\EventRating,
	Core\Utils\DateTime as _UDT;


trait Sliders {
	
	public function composeSliders($locationId)
	{
		$paid = $featured = $trending = [];
		
		$queryData['searchLocationField'] = $locationId;
		//$queryData['searchStartDate'] = _UDT::getDefaultStartDate();
		
// 		$paidEventIds = (new Featured()) -> getFeatured($locationId, Featured::PRIORITY_HIGH);
// 		if ($paidEventIds) {
// 			$queryData['searchId'] = array_keys($paidEventIds);
		
// 			$eventGrid = new \Frontend\Models\Search\Grid\Event($queryData, $this->getDi(), null, ['adapter' => 'dbMaster']);
// 			$eventGrid -> setSort('start_date');
// 			$eventGrid -> setSortDirection('ASC');
// 			$resultsPaid = $eventGrid -> getData();
				
// 			if ($resultsPaid['all_count'] > 0) {
// 				foreach ($resultsPaid['data'] as $ev) {
// 					$ev -> cover = (new EventImage()) -> getCover($ev);
// 					$paid[] = $ev;
// 				}
// 				$this -> view -> setVar('paidEvents', $paid);
// 			}
// 		}
// 		unset($queryData['searchId']);
		 
		$featuredEventIds = (new Featured()) -> getFeatured($locationId);
		if ($featuredEventIds) {
			$queryData['searchId'] = array_keys($featuredEventIds);
		}
		$eventGrid = new \Frontend\Models\Search\Grid\Event($queryData, $this -> getDi(), null, ['adapter' => 'dbMaster']);
		$eventGrid -> setSort('start_date');
		$eventGrid -> setSortDirection('ASC');
		if (!$featuredEventIds) {
			$eventGrid -> setLimit(14);
		}
		$resultsFeatured = $eventGrid -> getData();
		
		if ($resultsFeatured['all_count'] > 0) {
			foreach ($resultsFeatured['data'] as $ev) {
				$ev -> cover = (new EventImage()) -> getCover($ev);
				$featured[] = $ev;
			}
			//$this -> view -> setVar('featuredEvents', $featured);
			$this -> view -> setVar('paidEvents', $featured);
		}
		unset($queryData['searchId']);
		 
		
		$trendingEventIds = (new EventRating()) -> getTrendingIds($locationId);
		if ($trendingEventIds && count($trendingEventIds) > 3) {
			$queryData['searchId'] = $trendingEventIds;
		}			 
		$eventGrid = new \Frontend\Models\Search\Grid\Event($queryData, $this->getDi(), null, ['adapter' => 'dbMaster']);
		$eventGrid -> setSort('start_date');
		$eventGrid -> setSortDirection('ASC');
		if (!$trendingEventIds || count($trendingEventIds) < 3) {
			$eventGrid -> setLimit(4);
		}
		$resultsTrending = $eventGrid -> getData();
	
		if ($resultsTrending['all_count'] > 0) {
			foreach ($resultsTrending['data'] as $ev) {
				$ev -> cover = (new EventImage()) -> getCover($ev);
				$trending[] = $ev;
			}
			$this -> view -> setVar('trendingEvents', $trending);
		}
		
		return;
	}
}