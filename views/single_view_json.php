<?php
$json_data = array(
	'success' => $data['success'],
	'html' => $view_factory->Load_view($data['html']['view'], $data['html']['data'], true)
);

echo json_encode($json_data);
