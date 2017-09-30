<?php
    $mail_account = $_REQUEST['email'];
	$mailling_list = "mailling-list.txt";
    $text_data = file_get_contents($mailling_list) . "\r\n" . $mail_account;
    file_put_contents($mailling_list, $text_data);
	$show_data = file_get_contents($mailling_list);
	$show_data = str_replace("\r\n", "<br>", $show_data);
?>
<pre><?php echo $show_data; ?></pre>