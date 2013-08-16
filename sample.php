<?php
include 'FormValidator.php';

$validator = new FormValidator();
$validator->addErrorMessage('required', '%s boş bırakılamaz');

$rules = array(
    'name' => array('maxlen(10)'),
    'surname' => array('required'),
    'age' => array('int', 'min(18)')
);

$status = $validator->isValid(array('name' => 'Savaş', 'surname' => '', 'age' => 'foo'), $rules);
if ($status === true)
    echo 'VALID!';
else
    echo "NOT VALID!\n" . implode("\n", $status);

echo "\n\n";

$status = $validator->isValid($data = array('name' => 'Savaş', 'surname' => 'KOÇ', 'age' => 19), $rules);
if ($status === true)
    echo 'VALID!';
else
    echo "NOT VALID!\n" . implode("\n", $status);
