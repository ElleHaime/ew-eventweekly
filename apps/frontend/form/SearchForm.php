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
        $this->addElement('text', 'searchTitle', 'By Title', ['placeholder' => 'Event or venue...',
        												  	  'value' => '']);
        
        $this->addElement('hidden', 'searchLocationFormattedAddress', 'By Formatted Address');

        // start date
        $this->addElement('hidden', 'searchStartDate', 'Start Date');
        
        // end date
        $this->addElement('hidden', 'searchEndDate', 'End Date');
        
        // search result: map or list
        $this->addElement('hidden', 'searchTypeResult', 'Show result');
        $this -> view -> setVar('searchTypes', ['Map', 'List']);
        
        // search grid: event, venue or both
        $this -> addElement('hidden', 'searchGrid', 'Search grid');
        $this -> view -> setVar('searchGrids', ['event' => 'Events', 'venue' => 'Venues']);

        $this->add(new Submit('search'));
    }

}
