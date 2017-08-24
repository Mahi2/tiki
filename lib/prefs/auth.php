<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_auth_list()
{
	return array(
		'auth_method' => array(
			'name' => tra('Authentication method'),
			'description' => tra('Tiki supports several authentication methods. The default method is to use the internal user database.'),
			'type' => 'list',
			'help' => 'External+Authentication',
			'perspective' => false,
			'options' => array(
				'tiki' => tra('Tiki'),
				'openid' => tra('Tiki and OpenID'),
				'pam' => tra('Tiki and PAM'),
				'ldap' => tra('Tiki and LDAP'),
				'cas' => tra('CAS (Central Authentication Service)'),
				'saml' => tra('SAML'),
				'shib' => tra('Shibboleth'),
				'ws' => tra('Web Server'),
				'phpbb' => tra('phpBB'),
			),
			'default' => 'tiki',
		),
		'auth_token_access' => array(
			'name' => tra('Token access'),
			'description' => tra('With the presentation of a token, allow access to the content with elevated rights. The primary use of this authentication method is to grant temporary access to content to an external service.'),
			'help' => 'Token+Access',
			'perspective' => false,
			'type' => 'flag',
			'default' => 'n',
			'view' => 'tiki-admin_tokens.php',
		),
		'auth_token_access_maxtimeout' => array(
			'name' => tra('Token access default timeout'),
			'description' => tra('The default duration for which the generated tokens will be valid.'),
			'type' => 'text',
			'size' => 5,
			'perspective' => false,
			'filter' => 'digits',
			'units' => tra('seconds'),
			'default' => 3600*24*7,
		),
		'auth_token_access_maxhits' => array(
			'name' => tra('Token access default maximum hits'),
			'description' => tra('The default maximum number of times a token can be used before it expires.'),
			'type' => 'text',
			'size' => 5,
			'perspective' => false,
			'filter' => 'digits',
			'units' => tra('hits'),
			'default' => 10,
		),
		'auth_token_tellafriend' => array(
			'name' => tra('Share access rights with friends when using Tell a friend'),
			'description' => tra('Allow users to share their access rights for the current page with a friend when sending the link by email. The lifespan of the link is defined by the site.'),
			'type' => 'flag',
			'perspective' => false,
			'dependencies' => array(
				'auth_token_access',
				'feature_tell_a_friend',
			),
			'default' => 'n',
		),
		'auth_token_preserve_tempusers' => array(
			'name' => tra('Do not delete temporary users when token is deleted/expired'),
			'description' => tra('Normally temporary users created (see tiki-adminusers.php) are deleted when their access token is deleted/expired. If turned on, this will keep those users around (and can be manually deleted later) but they will have no groups and therefore no perms'),
			'type' => 'flag',
			'dependencies' => array(
				'auth_token_access',
			),
			'default' => 'n',
		),
		'auth_token_share' => array(
			'name' => tra('Share access rights with friends when using Share'),
			'description' => tra('Allow users to share their access rights for the current page with a friend when sending the link by email, Twitter, or Facebook. The lifespan of the link is defined by the site.'),
			'type' => 'flag',
			'perspective' => false,
			'dependencies' => array(
				'auth_token_access',
				'feature_share',
			),
			'default' => 'n',
		),
		'auth_phpbb_create_tiki' => array(
			'name' => tra('Create user if not registered in Tiki'),
			'description' => tra('Automatically create a new Tiki user for the PHPbb login'),
			'type' => 'flag',
			'perspective' => false,
			'default' => 'n',
		),
		'auth_phpbb_skip_admin' => array(
			'name' => tra('Use Tiki authentication for Admin login'),
			'type' => 'flag',
			'description' => tra('The user “admin” will be authenticated by <b>only</b> using Tiki’s user database. This option has no effect on users other than “admin”.'),
			'perspective' => false,
			'hint' => 'Recommended',
			'default' => 'y',
		),
		'auth_phpbb_disable_tikionly' => array(
			'name' => tra("Disable Tiki users with no phpBB login"),
			'type' => 'flag',
			'description' => tr('Disable Tiki users who don’t have a phpBB login as they could have been deleted.'),
			'perspective' => false,
			'hint' => 'Recommended',
			'default' => 'n',
		),
		'auth_phpbb_version' => array(
			'name' => tra('phpBB Version'),
			'description' => tra(''),
			'type' => 'list',
			'perspective' => false,
			'options' => array(
				'3' => tra('3'),
			),
			'default' => 3,
		),
		'auth_phpbb_dbhost' => array(
			'name' => tra('phpBB Database Hostname'),
			'description' => tra(''),
			'type' => 'text',
			'size' => 40,
			'perspective' => false,
			'default' => '',
		),
		'auth_phpbb_dbuser' => array(
			'name' => tra('phpBB Database Username'),
			'description' => tra(''),
			'type' => 'text',
			'size' => 40,
			'perspective' => false,
			'default' => '',
		),
		'auth_phpbb_dbpasswd' => array(
			'name' => tra('phpBB Database Password'),
			'description' => tra(''),
			'type' => 'password',
			'size' => 40,
			'perspective' => false,
			'default' => '',
		),
		'auth_phpbb_dbname' => array(
			'name' => tra('phpBB Database Name'),
			'description' => tra(''),
			'type' => 'text',
			'size' => 40,
			'perspective' => false,
			'default' => '',
		),
		'auth_phpbb_table_prefix' => array(
			'name' => tra('phpBB Table Prefix'),
			'description' => tra(''),
			'type' => 'text',
			'size' => 40,
			'perspective' => false,
			'default' => 'phpbb_',
		),
		'auth_ldap_permit_tiki_users' => array(
			'name' => tra('Use Tiki authentication for users created in Tiki'),
			'description' => tra('If this option is set, users that are created using Tiki are not authenticated via LDAP. This can be useful to let external users (ex.: partners or consultants) access Tiki, without being in your main user list in LDAP.'),
			'type' => 'flag',
			'perspective' => false,
			'default' => 'n',
		),
		'auth_ldap_host' => array(
			'name' => tra('Host'),
			'description' => tra('The hostnames, ip addresses or URIs of your LDAP servers. Separate multiple entries with Whitespace or ‘,’. If you use URIs, then the settings for Port number and SSL are ignored. Example: “localhost ldaps://master.ldap.example.org:63636” will try to connect to localhost unencrypted and if if fails it will try the master LDAP server at a special port with SSL.'),
			'type' => 'text',
			'size' => 20,
			'perspective' => false,
			'default' => '',
			'extensions' => ['ldap'],
		),
		'auth_ldap_port' => array(
			'name' => tra('Port'),
			'description' => tra('The port number your LDAP server uses (389 is the default, 636 if you check SSL).'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
			'perspective' => false,
			'default' => '',
			'extensions' => ['ldap'],
		),
		'auth_ldap_debug' => array(
			'name' => tra('Write LDAP debug Information in Tiki Logs'),
			'description' => tra('Write debug information to Tiki logs (Admin -> Tiki Logs, Tiki Logs have to be enable).'),
			'type' => 'flag',
			'perspective' => false,
			'warning' => tra('Do not enable this option for production sites.'),
			'default' => 'n',
			'view' => 'tiki-syslog.php',
		),
		'auth_ldap_ssl' => array(
			'name' => tra('Use SSL (ldaps)'),
			'description' => tra(''),
			'type' => 'flag',
			'perspective' => false,
			'default' => 'n',
		),
		'auth_ldap_starttls' => array(
			'name' => tra('Use TLS'),
			'description' => tra(''),
			'type' => 'flag',
			'perspective' => false,
			'default' => 'n',
		),
		'auth_ldap_type' => array(
			'name' => tra('LDAP Bind Type'),
			'description' => tra('<ul><li><b>Active Directory bind</b> will build a RDN like username@example.com where your basedn is (dc=example, dc=com) and username is your username</li><li><b>Plain bind</b> will build a RDN username</li><li><b>Full bind</b> will build a RDN like userattr=username, userdn, basedn where userattr is replaced with the value you put in ‘User attribute’, userdn with the value you put in ‘User DN’, basedn with the value with the value you put in ‘base DN’</li><li><b>OpenLDAP bind</b> will build a RDN like cn=username, basedn</li><li><b>Anonymous bind</b> will build an empty RDN</li></ul>'),
			'type' => 'list',
			'perspective' => false,
			'help' => 'LDAP-authentication#How_to_know_which_LDAP_Bind_Type_you_need_to_use',
			'options' => array(
				'default' => tra('Default: Anonymous Bind'),
				'full' => tra('Full: userattr=username,UserDN,BaseDN'),
				'ol' => tra('OpenLDAP: cn=username,BaseDN'),
				'ad' => tra('Active Directory (username@domain)'),
				'plain' => tra('Plain Username'),
			),
			'default' => 'default',
		),
		'auth_ldap_scope' => array(
			'name' => tra('Search scope'),
			'description' => tra('Used after authentication for getting user and group information.'),
			'type' => 'list',
			'perspective' => false,
			'options' => array(
				'sub' => tra('Subtree'),
				'one' => tra('One level'),
				'base' => tra('Base object'),
			),
			'default' => "sub",
		),
		'auth_ldap_version' => array(
			'name' => tra('LDAP version'),
			'description' => tra(''),
			'type' => 'text',
			'size' => 5,
			'perspective' => false,
			'default' => 3,
		),
		'auth_ldap_basedn' => array(
			'name' => tra('Base DN'),
			'description' => tra(''),
			'type' => 'text',
			'size' => 15,
			'perspective' => false,
			'default' => '',
		),
		'auth_ldap_userdn' => array(
			'name' => tra('User DN'),
			'description' => tra(''),
			'type' => 'text',
			'size' => 20,
			'perspective' => false,
			'default' => '',
		),
		'auth_ldap_userattr' => array(
			'name' => tra('User attribute'),
			'description' => tra(''),
			'type' => 'text',
			'size' => 20,
			'perspective' => false,
			'default' => 'uid',
		),
		'auth_ldap_useroc' => array(
			'name' => tra('User OC'),
			'description' => tra(''),
			'type' => 'text',
			'size' => 20,
			'perspective' => false,
			'default' => 'inetOrgPerson',
		),
		'auth_ldap_nameattr' => array(
			'name' => tra('Realname attribute'),
			'description' => tra(' Synchronize Tiki user attributes with the LDAP values.'),
			'type' => 'text',
			'size' => 20,
			'help' => 'LDAP-attributes-synchronization',
			'perspective' => false,
			'default' => 'displayName',
		),
		'auth_ldap_countryattr' => array(
			'name' => tra('Country attribute'),
			'description' => tra(' Synchronize Tiki user attributes with the LDAP values.'),
			'type' => 'text',
			'size' => 20,
			'help' => 'LDAP-attributes-synchronization',
			'perspective' => false,
			'default' => '',
		),
		'auth_ldap_emailattr' => array(
			'name' => tra('Email attribute'),
			'description' => tra(' Synchronize Tiki user attributes with the LDAP values.'),
			'type' => 'text',
			'size' => 20,
			'help' => 'LDAP-attributes-synchronization',
			'perspective' => false,
			'default' => '',
		),
		'auth_ldap_groupdn' => array(
			'name' => tra('Group DN'),
			'description' => tra(''),
			'type' => 'text',
			'size' => 20,
			'perspective' => false,
			'default' => '',
		),
		'auth_ldap_groupattr' => array(
			'name' => tra('Group name attribute'),
			'description' => tra(''),
			'type' => 'text',
			'size' => 20,
			'perspective' => false,
			'default' => 'cn',
		),
		'auth_ldap_groupdescattr' => array(
			'name' => tra('Group description attribute'),
			'description' => tra(''),
			'type' => 'text',
			'size' => 20,
			'perspective' => false,
			'default' => '',
		),
		'auth_ldap_groupoc' => array(
			'name' => tra('Group OC'),
			'description' => tra(''),
			'type' => 'text',
			'size' => 20,
			'perspective' => false,
			'default' => 'groupOfUniqueNames',
		),
		'auth_ldap_memberattr' => array(
			'name' => tra('Member attribute'),
			'description' => tra(''),
			'type' => 'text',
			'size' => 20,
			'perspective' => false,
			'default' => 'uniqueMember',
		),
		'auth_ldap_memberisdn' => array(
			'name' => tra('Member is DN'),
			'description' => tra(''),
			'type' => 'flag',
			'perspective' => false,
			'default' => 'y',
		),
		'auth_ldap_usergroupattr' => array(
			'name' => tra('Group attribute'),
			'description' => tra(''),
			'type' => 'text',
			'size' => 20,
			'perspective' => false,
			'default' => '',
		),
		'auth_ldap_groupgroupattr' => array(
			'name' => tra('Group attribute in group entry'),
			'description' => tra(''),
			'type' => 'text',
			'size' => 20,
			'hint' => tra('(Leave this empty if the group name is already given in the user attribute)'),
			'perspective' => false,
			'default' => '',
		),
		'auth_ldap_adminuser' => array(
			'name' => tra('Admin user'),
			'description' => tra(''),
			'type' => 'text',
			'size' => 15,
			'autocomplete' => 'off',
			'perspective' => false,
			'default' => '',
		),
		'auth_ldap_adminpass' => array(
			'name' => tra('Admin password'),
			'description' => tra(''),
			'type' => 'password',
			'size' => 15,
			'autocomplete' => 'off',
			'perspective' => false,
			'default' => '',
		),
		'auth_ldap_group_external' => array(
			'name' => tra('Use an external LDAP server for groups'),
			'description' => tra(''),
			'type' => 'flag',
			'perspective' => false,
			'default' => 'n',
		),
		'auth_ldap_group_host' => array(
			'name' => tra('Host'),
			'description' => tra(''),
			'type' => 'text',
			'size' => 20,
			'perspective' => false,
			'default' => 'localhost',
		),
		'auth_ldap_group_port' => array(
			'name' => tra('Port'),
			'description' => tra(''),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
			'perspective' => false,
			'default' => '389',
		),
		'auth_ldap_group_debug' => array(
			'name' => tra('Write LDAP debug Information in Tiki Logs'),
			'description' => tra('Write debug information to Tiki logs (Admin -> Tiki Logs, Tiki Logs have to be enable).'),
			'type' => 'flag',
			'perspective' => false,
			'warning' => tra('Do not enable this option for production sites.'),
			'default' => 'n',
		),
		'auth_ldap_group_ssl' => array(
			'name' => tra('Use SSL (ldaps)'),
			'description' => tra(''),
			'type' => 'flag',
			'perspective' => false,
			'default' => 'n',
		),
		'auth_ldap_group_starttls' => array(
			'name' => tra('Use TLS'),
			'description' => tra(''),
			'type' => 'flag',
			'perspective' => false,
			'default' => 'n',
		),
		'auth_ldap_group_type' => array(
			'name' => tra('LDAP Bind Type'),
			'description' => tra('<ul><li><b>Active Directory bind</b> will build a RDN like username@example.com where your basedn is (dc=example, dc=com) and username is your username</li><li><b>Plain bind</b> will build a RDN username</li><li><b>Full bind</b> will build a RDN like userattr=username, userdn, basedn where userattr is replaced with the value you put in ‘User attribute’, userdn with the value you put in ‘User DN’, basedn with the value with the value you put in ‘base DN’</li><li><b>OpenLDAP bind</b> will build a RDN like cn=username, basedn</li><li><b>Anonymous bind</b> will build an empty RDN</li></ul>'),
			'type' => 'list',
			'perspective' => false,
			'help' => 'LDAP-authentication#How_to_know_which_LDAP_Bind_Type_you_need_to_use',
			'options' => array(
				'default' => tra('Default: Anonymous Bind'),
				'full' => tra('Full: userattr=username,UserDN,BaseDN'),
				'ol' => tra('OpenLDAP: cn=username,BaseDN'),
				'ad' => tra('Active Directory (username@domain)'),
				'plain' => tra('Plain Username'),
			),
			'default' => 'default',
		),
		'auth_ldap_group_scope' => array(
			'name' => tra('Search scope'),
			'description' => tra(''),
			'type' => 'list',
			'perspective' => false,
			'options' => array(
				'sub' => tra('Subtree'),
				'one' => tra('One level'),
				'base' => tra('Base object'),
			),
			'default' => 'sub',
		),
		'auth_ldap_group_version' => array(
			'name' => tra('LDAP version'),
			'description' => tra(''),
			'type' => 'text',
			'size' => 5,
			'perspective' => false,
			'default' => '3',
		),
		'auth_ldap_group_basedn' => array(
			'name' => tra('Base DN'),
			'description' => tra(''),
			'type' => 'text',
			'size' => 15,
			'perspective' => false,
			'default' => '',
		),
		'auth_ldap_group_userdn' => array(
			'name' => tra('User DN'),
			'description' => tra(''),
			'type' => 'text',
			'size' => 20,
			'perspective' => false,
			'default' => '',
		),
		'auth_ldap_group_userattr' => array(
			'name' => tra('User attribute'),
			'description' => tra(''),
			'type' => 'text',
			'size' => 20,
			'perspective' => false,
			'default' => 'uid',
		),
		'auth_ldap_group_corr_userattr' => array(
			'name' => tra('Corresponding user attribute in 1st directory'),
			'description' => tra(''),
			'type' => 'text',
			'size' => 20,
			'perspective' => false,
			'default' => 'uid',
		),
		'auth_ldap_group_useroc' => array(
			'name' => tra('User OC'),
			'description' => tra(''),
			'type' => 'text',
			'size' => 20,
			'perspective' => false,
			'default' => 'inetOrgPerson',
		),
		'auth_ldap_group_adminuser' => array(
			'name' => tra('Admin user'),
			'description' => tra(''),
			'type' => 'text',
			'size' => 15,
			'perspective' => false,
			'default' => '',
		),
		'auth_ldap_group_adminpass' => array(
			'name' => tra('Admin password'),
			'description' => tra(''),
			'type' => 'password',
			'size' => 15,
			'perspective' => false,
			'default' => '',
		),
		'auth_ws_create_tiki' => array(
			'name' => tra('Create user if not registered in Tiki'),
			'type' => 'flag',
			'description' =>tr('If a user was externally authenticated, but not found in the Tiki user database, Tiki will create an entry in its user database.'),
			'perspective' => false,
			'default' => 'n',
		),
	);
}
