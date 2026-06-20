<?php
if (preg_match("/social_config.php/i", $_SERVER['SCRIPT_NAME'])) {
	Header("Location: /");
	die();
}

/*
 * Social OAuth configuration.
 *
 * Priority order:
 * 1) Environment variables (PM_SOCIAL_<PROVIDER>_*)
 * 2) This file values (non-empty)
 * 3) DB table auth_oauth_providers
 *
 * Callback URLs to register in providers:
 * - Google:   {base_url}index.php?p=social-callback&provider=google
 * - Facebook: {base_url}index.php?p=social-callback&provider=facebook
 *
 * Apple requires additional keys and special callback handling.
 */
return array(
	'google' => array(
		'enabled' => false,
		'client_id' => '',
		'client_secret' => '',
		'scope' => 'openid email profile'
	),
	'facebook' => array(
		'enabled' => false,
		'client_id' => '',
		'client_secret' => '',
		'scope' => 'email public_profile'
	),
	'apple' => array(
		'enabled' => false,
		'client_id' => '',
		'team_id' => '',
		'key_id' => '',
		'private_key' => ''
	),
	'default_upline' => 1
);
