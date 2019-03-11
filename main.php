<?php

include 'src/Dolist.php';

$attachements = null;
$data = utf8_encode('<?xml version="1.0" encoding="utf-8"?>
                                                      <emtroot><CLIENT><NOM>DE GROOTE</NOM></CLIENT>                                        
                                                      </emtroot>');
$isTest = true;
$contentType = "EmailMultipart";
$recipient = "fdg@ylly.fr";

$doList = new Dolist();

$doList->connectDoList();

echo "1 - Email contact\n";
echo "2 - Email command\n";
echo "Enter the number corresponding : ";

$type = rtrim(fgets(STDIN));

//$doList->sendEmail($type, $attachements, $data, $isTest, $recipient, $contentType);

