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
        $this->addElement('text', 'title', 'By Title');

        // search by location
        $this->addElement('text', 'locationSearch', 'By Location');

        // start date
        $this->addElement('text', 'start_dateSearch', 'Start Date');

        // end date
        $this->addElement('text', 'end_dateSearch', 'End Date');

        // search by category
        $categories = Category::find();
        $categories = $categories->toArray();

        foreach ($categories as $index => $node) {
            $this->addElement('check', 'category['.$index.']', 'By Category', array(
                    'value' => $node['id']
                ));
        }

        $this->add(new Submit('search'));
    }

}
