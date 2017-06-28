# Includes the autoloader for libraries installed with composer
<?php

require __DIR__ . '/vendor/autoload.php';

# Imports the Google Cloud client library
use Google\Cloud\Vision\VisionClient;

# Your Google Cloud Platform project ID
$projectId = 'proud-lead-172005';

# Instantiates a client
$vision = new VisionClient([
    'projectId' => $projectId
]);

# The name of the image file to annotate
$fileName = __DIR__ . '/resources/car.jpg';

# Prepare the image to be annotated
$image = $vision->image(fopen($fileName, 'r'), [
    'LABEL_DETECTION'
]);

# Performs label detection on the image file
$labels = $vision->annotate($image)->labels();

echo "Labels:\n";
foreach ($labels as $label) {
    echo $label->description() . "\n";
}