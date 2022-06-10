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
 * シートデータの更新
 */
$sheet_name = "想定問題集"; // シートを指定
$sheet_range = "F".$_POST['id'].":G".$_POST['id']; // 範囲を指定。開始から終了まで斜めで囲む感じです。
// ヘッダ（id, question, answer, category, reference, appearCount, correctCount)
$values = [
  [$_POST['total'], $_POST['correct']], // A5の列
];
$updateBody = new Google_Service_Sheets_ValueRange(['values' => $values]);
$response = $sheet->spreadsheets_values->update($sheet_id, $sheet_name.'!'.$sheet_range, $updateBody, ["valueInputOption"
=> 'USER_ENTERED']);
