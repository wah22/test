<?php
require_once __DIR__ . '/vendor/autoload.php';
session_start();
$_SESSION['result_html'];

$klein = new \Klein\Klein();

//$klein->respond(function () {
 // return 'Klein is Working';
//});

$klein->respond('GET', '/access-data', function($request, $response, $service) {
  $service->render('form.php', array('title' => 'Get Info'));
});

$klein->respond('GET', '/input-data', function($request, $response, $service) {
  $service->render('input_form.php', array('title' => 'Input Info'));
});

$klein->respond(function ($request, $response, $service, $app) use ($klein){


  $app->register('db', function () {
    $host = '127.0.0.1';
    $db = 'klein';
    $user = 'root';
    $pass = 'ztr6001ztr6001';
    $charset = 'utf8mb4';
    
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

    $opt = [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    return new PDO($dsn, $user, $pass, $opt);
  });
});

$klein->respond('GET', '/submit-input-form', function($request, $response, $service, $app) {
   $input_array = $request->paramsGet();
   $sql = 'INSERT INTO address (customerName, street, city, customerState, zip) VALUES (?, ?, ?, ?, ?)';
   $pdo = $app->db;
   $stmt = $pdo->prepare($sql);
   $stmt->execute([$input_array['customer-name'], $input_array['street'], $input_array['city'], $input_array['state'], $input_array['zip']]);
   return "Your entry has been successfully recorded!"; 
});

$klein->respond('GET', '/customer-form-results', function ($request, $response, $service, $app) {
  $input_array = $request->paramsGet();
  $name = $input_array['customer-name'] ?? ' ';
  $zip = $input_array['zip'] ?? '00000'; 
  $sql = 'SELECT * FROM address WHERE customerName = ? OR zip = ?';
  $stmt = $app->db->prepare($sql);
  $stmt->execute([$input_array['customer-name'], $input_array['zip']]);
  $html = '<table style="width:100%"><tr><th>Name</th><th>Street Address</th><th>City</th><th>State</th><th>Zip Code</th>     </tr>';
  $has_records = false;
  while ($users = $stmt->fetch()) {    
    $has_records = true;
    $html .= "<tr><th> {$users['customerName']} </th>
              <th> {$users['street']} </th>
              <th> {$users['city']} </th>
              <th> {$users['customerState']} </th>
              <th> {$users['zip']} </th></tr>";
  }
  $html .= "</table>";
  if (!$has_records) {
    $html = '<h1>Sorry, there are no records with the search criteria that you gave</h1>';
  }
  $service->startSession();
  $_SESSION['result_html'] = $html;
  $response->redirect('/results');
});
$klein->respond('GET', '/hello-world', function () {
    return 'Hello World!';
});
$klein->respond('GET', '/results', function ($request, $response, $service) {  
  $html = $_SESSION['result_html'];
  $service->render('result.php', array('result_table' =>  $html));
});
$klein->dispatch();
