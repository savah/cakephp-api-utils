<?php
/**
 * Authorize
 *
 * This view is displayed to user when a client/app is being authorized for the first
 * time, and the user is required to grant permission.
 * 
 * PHP 5
 *
 * Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @author      Anthony Putignano <anthony@wizehive.com>
 * @since       0.1
 * @package     OAuth2
 * @subpackage  OAuth2.View.OAuth2
 * @copyright   Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>

<script type="text/javascript">
	if (top != self) {
		window.document.write("<div style='background:black; opacity:0.5; filter: alpha (opacity = 50); position: absolute; top:0px; left: 0px;"
		+ "width: 9999px; height: 9999px; zindex: 1000001' onClick='top.location.href=window.location.href'></div>");
	}
</script>


<?php echo $this->Form->create('Authorize'); ?>

<?php echo $client['app_name']; ?> is requesting permission to access your account.

<?php
echo $this->Form->submit('Allow access', array('name' => 'allow'));
echo $this->Form->submit('No thanks', array('name' => 'deny'));
echo $this->Form->end();
?>