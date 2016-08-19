<?php

require(dirname(__FILE__).'/config/config.inc.php');

Tools::redirect('index.php?controller=order-confirmation'.((count($_GET) || count($_POST)) ? '&'.http_build_query(array_merge($_GET, $_POST), '', '&') : ''), __PS_BASE_URI__, null, 'HTTP/1.1 301 Moved Permanently');
