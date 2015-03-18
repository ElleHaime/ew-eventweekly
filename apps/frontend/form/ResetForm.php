<?php

namespace Frontend\Form;

use Core\Form,
    Phalcon\Forms\Element\Submit;

class ResetForm extends Form
{
    public function init()
    {
        $conf_passwordValidators = array(
            'PresenceOf' => array('message' => 'Password is required'),
            'StringLength' => array('min' => 6),
            'Confirmation' => array(
                'message' => 'Password doesn\'t match confirmation',
                'with' => 'conf_password'
            )
        );

        $passwordValidators = array(
            'PresenceOf' => array('message' => 'Password is required'),
            'StringLength' => array('min' => 6)
        );

        $this -> addElement('password', 'password', 'Password', array('validators' => $conf_passwordValidators,
        																'class' => 'input-registration-control'));

        $this -> addElement('password', 'conf_password', 'Confirm Password', array('validators' => $passwordValidators,
        																			'class' => 'input-registration-control'));

        $this -> add(new Submit('Reset password'));
    }
}