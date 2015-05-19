<?php

namespace Frontend\Form;

use Core\Form,
    Frontend\Models\Category,
    Phalcon\Forms\Element\Submit;

class SearchForm extends Form
{

    public function init()
    {
        // search by title
        $this->addElement('text', 'searchTitle', 'By Title');

        // search by location
        $this->addElement('hidden', 'searchLocationLatMin', 'By Location Latitude');
        $this->addElement('hidden', 'searchLocationLngMin', 'By Location Longitude');

        $this->addElement('hidden', 'searchLocationLatMax', 'By Location Latitude');
        $this->addElement('hidden', 'searchLocationLngMax', 'By Location Longitude');

        // start date
        $this->addElement('hidden', 'searchStartDate', 'Start Date');
        
        // end date
        $this->addElement('hidden', 'searchEndDate', 'End Date');
        
        // search result: map or list
        $this->addElement('hidden', 'searchTypeResult', 'Show result');
        $this -> view -> setVar('searchTypes', ['Map', 'List']);

/*
 		// start date
        $this->addElement('text', 'searchStartDate', 'Start Date'); 
      
  		// end date
        $this->addElement('text', 'searchEndDate', 'End Date');

        // search by category
        $categories = Category::find();
        $categories = $categories->toArray();

        foreach ($categories as $index => $node) {
            $this->addElement('check', 'searchCategory['.$index.']', 'By Category', array(
                    'value' => $node['id']
                ));
        }
*/
        $this->add(new Submit('search'));
    }

}
