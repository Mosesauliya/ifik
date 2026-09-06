<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| EMAIL CONFIGURATION (Gmail SMTP Protocol)
| -------------------------------------------------------------------
| Konfigurasi pengiriman email untuk Forgot Password & Notifikasi IFIK Labs Portal
*/

$config['protocol']     = 'smtp';
$config['smtp_host']    = 'smtp.gmail.com';
$config['smtp_port']    = 587;
$config['smtp_user']    = 'apgchannel11@gmail.com';
$config['smtp_pass']    = 'xukdgephatibgulq';
$config['smtp_crypto']  = 'tls';
$config['mailtype']     = 'html';
$config['charset']      = 'utf-8';
$config['newline']      = "\r\n";
$config['crlf']         = "\r\n";
$config['wordwrap']     = TRUE;
$config['smtp_timeout'] = 15;
