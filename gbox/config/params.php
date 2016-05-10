<?php
return [
	'app_name' => 'Gbox Framework',
	'app_company' => 'Roxgüel Devs',
	'salt' => strtr(substr(base64_encode(openssl_random_pseudo_bytes('30')), 0, 22), array('+' => '.')),
];
?>