<?php

namespace Frontend\Form;

use Core\Form,
    Phalcon\Forms\Element\Submit;

class ChangePassForm extends Form
{
    public function init()
    {
        $OldPasswordValidators = array(
            'PresenceOf' => array('message' => 'Password is required')
        );

        $conf_passwordValidators = array(
            'PresenceOf' => array('message' => 'Password is required'),
            'StringLength' => array('min' => 2),
            'Confirmation' => array(
                'message' => 'Password doesn\'t match confirmation',
                'with' => 'conf_password'
            )
        );

        $passwordValidators = array(
            'PresenceOf' => array('message' => 'Password is required'),
            'StringLength' => array('min' => 2)
        );

        $this->addElement('password', 'old_password', 'Old Password', array('validators' => $OldPasswordValidators));

        $this->addElement('password', 'password', 'New Password', array('validators' => $conf_passwordValidators));

        $this->addElement('password', 'conf_password', 'Confirm Password', array('validators' => $passwordValidators));

        $this->add(new Submit('Change password'));
    }
}