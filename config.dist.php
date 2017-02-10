<?php

/**
 * This is Your Slack Team URL ( No trailing Slash )
 */
define('SLACK_URL', 'https://my-slack.slack.com');

/**
 * This is your Slack Token ( https://api.slack.com/docs/oauth-test-tokens )
 */
define('SLACK_TOKEN', 'XXXX-XXXXXXXXXXXX-XXXXXXXXXXXX-XXXXXXXXXXXX-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');

/**
 * Create a Unique Invite Code `abc123` for anyone you want to to allow to Invite Users
 * Once the new user registers, they will received a Direct Message letting them know
 * a new user signed up using their unique code.
 *
 * slack_id - This is the `members.id` returned from https://api.slack.com/methods/users.list
 * slack_name - This is the `members.name` returned from https://api.slack.com/methods/users.list
 * slack_ims_id - This is the `ims.id` returned the `ims.user` that matches `members.id` for from https://api.slack.com/methods/im.list
 */
define('INVITE_TOKENS', array(
    'abc123' => array(
        'name' => 'Jane Doe',
        'slack_id' => 'UXXXXXXXX',
        'slack_name' => '@jane-doe',
        'slack_ims_id' => 'DXXXXXXXX'
    ),
    '456def' => array(
        'name' => 'John Smith',
        'slack_id' => 'UXXXXXXXX',
        'slack_name' => '@john-smith',
        'slack_ims_id' => 'DXXXXXXXX'
    )
));