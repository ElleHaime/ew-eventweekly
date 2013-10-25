<?php

class SignupController extends Phalcon\Mvc\Controller
{

  public function indexAction()
	{
    echo "SignupController indexAction die()";

    echo "<pre>";
    var_dump($this);
    echo "</pre>";

    //$this->ﬂash->;
    die;
	}

  public function testAction()
  {
    echo "test";

    $request = new \Phalcon\Http\Request();

    if ($request->isGet() == true)
      echo "get";
      //print_r($request->getGet());
    else
      echo "post";

    /*
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
    */
  }

	public function registerAction()
	{

		//Request variables from html form
		$name = $this->request->getPost('name', 'string');
		$email = $this->request->getPost('email', 'email');

		$user = new Users();
		$user->name = $name;
		$user->email = $email;

    echo "almost done";

		//Store and check for errors
    /*
		if ($user->save() == true) {
			echo 'Thanks for register!';
		} else {
			echo 'Sorry, the next problems were generated: ';
			foreach ($user->getMessages() as $message){
				echo $message->getMessage(), '<br/>';
			}
		}
    */
	}

}
