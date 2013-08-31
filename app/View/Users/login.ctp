<?php

	echo $this->Session->flash();

	echo $this->Form->create('User');

	echo $this->Form->input('username');
	echo $this->Form->input('password');

	echo $this->Form->end('Sign In');

?>

<p><a href="/users/signup">Sign up for an account</a></p>