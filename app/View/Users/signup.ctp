<?php echo $this->Session->flash(); ?>

<h2>Create an Account</h2>

<?php

echo $this->Form->create('User');

echo $this->Form->input('username');

echo $this->Form->input('password');

echo $this->Form->input('display_name');

echo $this->Form->input('email');

echo $this->Form->end('Sign Up');

?>