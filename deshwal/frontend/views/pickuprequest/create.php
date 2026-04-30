<?php
use yii\helpers\Html;
use yii\grid\GridView;
$this->title = 'New Pickup Request';
?>

<h4 class="mt-2"><?= Html::encode($this->title) ?></h4>

<?= $this->render('_form', [
    'model' => $model,
    'pickupItems' => $pickupItems??[],
    'vendorLocations'=>$vendorLocations ??[],
    'locationType'=>$locationType??[],
    'additionalInfo'=>$additionalInfo??[],
    'documentReceivedOptions'=>$documentReceivedOptions??[],
    'pickupDocumentType'=>$pickupDocumentType??[],

    'workingTimingsOptions' => $workingTimingsOptions??[],
    'provisionToExtendTiming' => $provisionToExtendTiming??[],
    'extensionProvisionOptions' => $extensionProvisionOptions??[],
    'entryFormalitiesPersonOptions' => $entryFormalitiesPersonOptions??[],
    'materialLocationFloorOptiond' => $materialLocationFloorOptiond??[],
    'serviceLiftOptions' => $serviceLiftOptions??[],
    'stairsSpaceOptions' => $stairsSpaceOptions??[],
    'segregationOptions' => $segregationOptions??[],
    'spaceForSegregationOptions' => $spaceForSegregationOptions??[],
    'movementFromPremisesOptions' => $movementFromPremisesOptions??[],
    'spaceForVehicleOptions' => $spaceForVehicleOptions??[],
    'smallVehicleOptions' => $smallVehicleOptions??[],
    'vehicleAsPerHeightOptions' => $vehicleAsPerHeightOptions??[],
    'vehicleEntryFormalitiesOptions' => $vehicleEntryFormalitiesOptions??[],
    'vehicleInsidePremisesOptions' => $vehicleInsidePremisesOptions??[],
    'products_list' => $products_list??[]
]) ?>
