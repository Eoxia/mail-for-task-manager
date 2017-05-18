<?php
$task_mail = get_option( 'task_mail', true );
$taskpw = get_option( 'task_pass', true );
$hostname = "{imap.gmail.com:993/imap/ssl/novalidate-cert}";

$imap = imap_open( $hostname, $task_mail, $taskpw );
