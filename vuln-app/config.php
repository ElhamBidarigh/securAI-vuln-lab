<?php
session_start();
$DB_HOST = 'db';
$DB_USER = 'root';
$DB_PASS = 'rootpass';
$DB_NAME = 'securAI_lab';
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) { die("DB connection failed: " . $conn->connect_error); }
ini_set('display_errors', 1);
error_reporting(E_ALL);
