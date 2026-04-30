<?php

namespace app\models;
use yii\web\BadRequestHttpException;

use Yii;

/**
 * This is the model class for table "inspection".
 *
 * @property int $inspection_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string $inspection_no
 * @property int|null $sourcing_deal
 * @property string|null $account_name
 * @property int|null $spoc_name
 * @property string|null $spoc_number
 * @property string|null $spoc_email
 * @property string|null $inspection_preferred_time
 * @property int|null $phone_allowed
 * @property int|null $safety_equipment
 * @property string|null $inspection_preferred_date
 * @property string|null $laptop_allowed
 * @property string|null $photo_imges_allowed
 * @property int|null $stages
 * @property string|null $pav_hold_by_client_reason
 * @property string|null $pav_hold_by_dwmpl_reason
 * @property string|null $pav_cancelled_reason
 * @property string|null $resume_date
 * @property int|null $submit_for_logistics
 * @property int|null $schedule_inspection
 * @property int|null $inspection_started
 * @property int|null $inspection_completed
 * @property string|null $inspection_location
 * @property string|null $location_address
 * @property string|null $location_state
 * @property string|null $location_city
 * @property string|null $location_pincode
 * @property int|null $inspection_done_by
 * @property int|null $vendor_name
 * @property int|null $insection_type
 * @property string|null $inspection_start_date
 * @property string|null $inpection_completed_date
 * @property string|null $vendor_spoc_number
 * @property int|null $logistics_fe_number
 * @property string|null $inspection_schedule_date
 * @property int|null $material_type
 * @property int|null $vendor_spoc_name_done_by_vendor
 * @property int|null $logistics_fe_name_done_by_dwmpl
 * @property int|null $ins_entry personnel
 * @property string|null $entry_personnel
 * @property string|null $working_timings
 * @property string|null $slot_get_inspect_item
 * @property string|null $single_location_multi_location
 * @property string|null $how_many_locations_floor
 * @property string|null $security_protocoal_parameter
 * @property string|null $allowed_at_the_faciltiy
 * @property string|null $items_which_need_inspect
 * @property string|null $laptop_entry_at_the_premises
 * @property string|null $physical_verification_of_asset
 * @property string|null $perform_at_which_floor_area
 * @property string|null $designated_inspection_area
 * @property string|null $sufficient_power_supply
 * @property string|null $supply_to_laptop_desktop
 * @property string|null $power_on_the_machines
 * @property int $deleted
 *
 * @property InspectionFullProductDetailDesktop[] $inspectionFullProductDetailDesktops
 * @property InspectionFullProductDetailLaptop[] $inspectionFullProductDetailLaptops
 * @property InspectionFullProductDetailTft[] $inspectionFullProductDetailTfts
 * @property InspectionRandomProductDetail[] $inspectionRandomProductDetails
 */
class Inspection extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inspection';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'inspection_no'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'sourcing_deal', 'spoc_name', 'phone_allowed', 'safety_equipment', 'stages', 'submit_for_logistics', 'schedule_inspection', 'inspection_started', 'inspection_completed', 'inspection_done_by', 'vendor_name', 'insection_type', 'logistics_fe_number', 'material_type', 'vendor_spoc_name_done_by_vendor', 'logistics_fe_name_done_by_dwmpl', 'ins_entry personnel', 'deleted','tools_allowed_inside_premises','vehicle_allowed_parking','formailites_vehicle_entry','logistics_spoc'], 'integer'],
            [['createdtime', 'modifiedtime', 'inspection_preferred_date', 'resume_date', 'inspection_start_date', 'inpection_completed_date', 'inspection_schedule_date'], 'safe'],
            [['inspection_no', 'spoc_email', 'entry_personnel', 'working_timings', 'slot_get_inspect_item', 'single_location_multi_location', 'how_many_locations_floor', 'allowed_at_the_faciltiy', 'items_which_need_inspect', 'laptop_entry_at_the_premises', 'physical_verification_of_asset', 'perform_at_which_floor_area', 'designated_inspection_area', 'sufficient_power_supply', 'supply_to_laptop_desktop', 'power_on_the_machines'], 'string', 'max' => 100],
            [['account_name', 'laptop_allowed', 'photo_imges_allowed', 'pav_hold_by_client_reason', 'pav_hold_by_dwmpl_reason', 'pav_cancelled_reason', 'inspection_location','location_state', 'location_city', 'security_protocoal_parameter'], 'string', 'max' => 200],
            [['spoc_number', 'vendor_spoc_number'], 'string', 'max' => 15],
            [['inspection_preferred_time', 'location_pincode'], 'string', 'max' => 10],
            [[ 'location_address'], 'string', 'max' => 3000],
            // added for handling blank values saving in by ptpatel on date 24-01-2026
            [['account_name'], 'trim'],
            [['account_name'], 'required', 'message' => 'Account Name cannot be blank.'],
            [['account_name'], 'integer', 'message' => 'Account Name must be a number.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'inspection_id' => 'Inspection ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'inspection_no' => 'Inspection No',
            'sourcing_deal' => 'Sourcing Deal',
            'account_name' => 'Account Name',
            'spoc_name' => 'Spoc Name',
            'spoc_number' => 'Spoc Number',
            'spoc_email' => 'Spoc Email',
            'inspection_preferred_time' => 'Inspection Preferred Time',
            'phone_allowed' => 'Phone Allowed',
            'safety_equipment' => 'Safety Equipment',
            'inspection_preferred_date' => 'Inspection Preferred Date',
            'laptop_allowed' => 'Laptop Allowed',
            'photo_imges_allowed' => 'Photo Imges Allowed',
            'stages' => 'Stages',
            'pav_hold_by_client_reason' => 'Pav Hold By Client Reason',
            'pav_hold_by_dwmpl_reason' => 'Pav Hold By Dwmpl Reason',
            'pav_cancelled_reason' => 'Pav Cancelled Reason',
            'resume_date' => 'Resume Date',
            'submit_for_logistics' => 'Submit For Logistics',
            'schedule_inspection' => 'Schedule Inspection',
            'inspection_started' => 'Inspection Started',
            'inspection_completed' => 'Inspection Completed',
            'inspection_location' => 'Inspection Location',
            'location_address' => 'Location Address',
            'location_state' => 'Location State',
            'location_city' => 'Location City',
            'location_pincode' => 'Location Pincode',
            'inspection_done_by' => 'Inspection Done By',
            'vendor_name' => 'Vendor Name',
            'insection_type' => 'Insection Type',
            'inspection_start_date' => 'Inspection Start Date',
            'inpection_completed_date' => 'Inpection Completed Date',
            'vendor_spoc_number' => 'Vendor Spoc Number',
            'logistics_fe_number' => 'Logistics Fe Number',
            'inspection_schedule_date' => 'Inspection Schedule Date',
            'material_type' => 'Material Type',
            'vendor_spoc_name_done_by_vendor' => 'Vendor Spoc Name Done By Vendor',
            'logistics_fe_name_done_by_dwmpl' => 'Logistics Fe Name Done By Dwmpl',
            'ins_entry personnel' => 'Ins Entry Personnel',
            'entry_personnel' => 'Entry Personnel',
            'working_timings' => 'Working Timings',
            'slot_get_inspect_item' => 'Slot Get Inspect Item',
            'single_location_multi_location' => 'Single Location Multi Location',
            'how_many_locations_floor' => 'How Many Locations Floor',
            'security_protocoal_parameter' => 'Security Protocoal Parameter',
            'allowed_at_the_faciltiy' => 'Allowed At The Faciltiy',
            'items_which_need_inspect' => 'Items Which Need Inspect',
            'laptop_entry_at_the_premises' => 'Laptop Entry At The Premises',
            'physical_verification_of_asset' => 'Physical Verification Of Asset',
            'perform_at_which_floor_area' => 'Perform At Which Floor Area',
            'designated_inspection_area' => 'Designated Inspection Area',
            'sufficient_power_supply' => 'Sufficient Power Supply',
            'supply_to_laptop_desktop' => 'Supply To Laptop Desktop',
            'power_on_the_machines' => 'Power On The Machines',
            'logistics_spoc'=>'Logistics SPOC',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * Gets query for [[InspectionFullProductDetailDesktops]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInspectionFullProductDetailDesktops()
    {
        return $this->hasMany(InspectionFullProductDetailDesktop::class, ['inspection_id' => 'inspection_id']);
    }

    /**
     * Gets query for [[InspectionFullProductDetailLaptops]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInspectionFullProductDetailLaptops()
    {
        return $this->hasMany(InspectionFullProductDetailLaptop::class, ['inspection_id' => 'inspection_id']);
    }

    /**
     * Gets query for [[InspectionFullProductDetailTfts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInspectionFullProductDetailTfts()
    {
        return $this->hasMany(InspectionFullProductDetailTft::class, ['inspection_id' => 'inspection_id']);
    }

    /**
     * Gets query for [[InspectionRandomProductDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInspectionRandomProductDetails()
    {
        return $this->hasMany(InspectionRandomProductDetail::class, ['inspection_id' => 'inspection_id']);
    }
    public function checkProducts($inspection_id)
    {
        try {
            $cond = false; // Set initial condition as false

            // Check if any record exists in InspectionRandomProductDetail for the given inspection_id
            $exists = InspectionRandomProductDetail::find()
                ->where(['inspection_id' => $inspection_id])
                ->exists();  // Returns true if any record exists, false otherwise

            if ($exists) {
                $cond = true;
            }

            // Check if any record exists in InspectionFullProductDetailLaptop for the given inspection_id
            $exists = InspectionFullProductDetailLaptop::find()
                ->where(['inspection_id' => $inspection_id])
                ->exists();

            if ($exists) {
                $cond = true;
            }

            // Check if any record exists in InspectionFullProductDetailDesktop for the given inspection_id
            $exists = InspectionFullProductDetailDesktop::find()
                ->where(['inspection_id' => $inspection_id])
                ->exists();

            if ($exists) {
                $cond = true;
            }

            // Check if any record exists in InspectionFullProductDetailTft for the given inspection_id
            $exists = InspectionFullProductDetailTft::find()
                ->where(['inspection_id' => $inspection_id])
                ->exists();

            if ($exists) {
                $cond = true;
            }
            return $cond;

          

            // Normal logic continues here if records exist

        } catch (BadRequestHttpException $e) {
            // Log the error message for debugging purposes
            Yii::error($e->getMessage(), __METHOD__);

            // Re-throw the BadRequestHttpException to be handled by Yii's exception handler
            throw $e;
        }
    }

}
