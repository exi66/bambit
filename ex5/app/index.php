<?php
require_once(__DIR__ . '/../src/crest.php');

$phoneNumber = '+79181234567';

$result = CRest::call('crm.duplicate.findbycomm', [
	'entity_type' => 'LEAD',
	'type' => 'PHONE',
	'values' => [$phoneNumber]
]);

echo '<pre>';
print_r($result);
echo '</pre>';

if (empty($result['result']['LEAD']))
	exit(1);

$targetLead = $result['result']['LEAD'][0];

$newPhone = rand(111111111, 999999999);
$newSecondPhone = rand(111111111, 999999999);

$result = CRest::call('crm.lead.update', [
	'id' => $targetLead,
	'fields' => [
		'PHONE' => [
			[
				'VALUE' => $newPhone,
				'VALUE_TYPE' => 'HOME'
			]
		]
	]
]);

$result = CRest::call('crm.lead.get', ['id' => $targetLead]);

echo '<pre>';
print_r($result['result']['PHONE']);
echo '</pre>';

$leadData = CRest::call('crm.lead.get', ['id' => $targetLead]);

$phones = $leadData['result']['PHONE'];

foreach ($phones as &$phone) {
	if ($phone['VALUE'] == $newPhone)
		$phone['VALUE'] = $newSecondPhone;
	else if ($phone['VALUE'] != $phoneNumber)
		$phone['VALUE'] = '';
}

$result = CRest::call('crm.lead.update', [
	'id' => $targetLead,
	'fields' => [
		'PHONE' => $phones
	]
]);

$result = CRest::call('crm.lead.get', ['id' => $targetLead]);

echo '<pre>';
print_r($result['result']['PHONE']);
echo '</pre>';