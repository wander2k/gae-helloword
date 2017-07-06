<?php

require __DIR__ . '/vendor/autoload.php';

use Google\Cloud\Datastore\DatastoreClient;

$datastore = new DatastoreClient([
    'projectId' => 'proud-lead-172005'
]);

// Create an entity
$bob = $datastore->entity('Person');
$bob['firstName'] = 'Bob';
$bob['email'] = 'bob@example.com';
$datastore->insert($bob);

$mike = $datastore->entity('Person');
$mike['firstName'] = 'Mike';
$mike['email'] = 'mike@example.com';
$datastore->insert($mike);

// Update the entity
//$bob['email'] = 'bobV2@example.com';
//$datastore->update($bob);

// If you know the ID of the entity, you can look it up
//$key = $datastore->key('Person', '12345328897844');
//$entity = $datastore->lookup($key);