<?php
//ini_set('display_errors', "On");
require __DIR__ . '/vendor/autoload.php';

$key = __DIR__ . '/memorandum-card-f442aaae46d3.json';
$sheet_id = "1KvHnlPiUXtSjTYupezeNZ8-XMpoW04rG4RIucDncnBM";

$client = new \Google_Client();
$client->setAuthConfig($key);
$client->addScope(\Google_Service_Sheets::SPREADSHEETS);
$client->setApplicationName("想定問題集"); // 適当な名前でOK
$sheet = new \Google_Service_Sheets($client);

/*
 * シートデータの取得
 */
$sheet_name = "想定問題集"; // シートを指定
$response = $sheet->spreadsheets_values->get($sheet_id, $sheet_name);
$arrayData = $response->getValues();
$arrayHash = array();

foreach ($arrayData as $indexRows => $row) {
  if ($indexRows == 0) continue;
  foreach ($row as $indexCols => $item) {
    $arrayHash[$indexRows - 1][$arrayData[0][$indexCols]] = $item;
  }
}

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

echo json_encode($arrayHash, JSON_UNESCAPED_UNICODE);
