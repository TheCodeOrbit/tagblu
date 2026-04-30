<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "vendor_locations".
 *
 * @property int $vendorloc_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string $vendor_loc_no
 * @property int $vendor_account
 * @property string $vendor_loc_name
 * @property string $type_of_location
 * @property string|null $legal_entity_name
 * @property int|null $currency
 * @property float|null $exchange_rate
 * @property string|null $quote
 * @property float|null $area_sf
 * @property string|null $spoc_name
 * @property string|null $spoc_number
 * @property string $spoc_email
 * @property string|null $escalation_name
 * @property string|null $escalation_number
 * @property string|null $escalation_email
 * @property string|null $lease_expiry_date
 * @property string|null $material_rec_name
 * @property int|null $pli
 * @property string|null $plot_number
 * @property string|null $building_name
 * @property string|null $floor
 * @property string|null $area_sector_name
 * @property string|null $landmark
 * @property string|null $city_short_name
 * @property int|null $local_union
 * @property int|null $no_entry_zone
 * @property int|null $no_entry_timings
 * @property string|null $loc_fin_invoice_name
 * @property string|null $loc_fin_invoice_number
 * @property string|null $loc_fin_invoice_email
 * @property string|null $loc_escalation_first_level_name
 * @property string|null $loc_escalation_first_level_number
 * @property string|null $loc_escalation_first_level_email
 * @property string|null $primary_location
 * @property string|null $cust_location_owner
 * @property string|null $address
 * @property string|null $country
 * @property string|null $city_state
 * @property int|null $city
 * @property string|null $pincode
 * @property int|null $location_type
 * @property string|null $state
 * @property string|null $state_code
 * @property string|null $place_of_supply
 * @property string|null $CE_regn_no
 * @property int|null $bill_currency
 * @property int|null $gstin_status
 * @property string|null $gstin_no_uin
 * @property string|null $pan_no
 * @property string|null $tin_no
 * @property string|null $cin_no
 * @property int $deleted
 */
class VendorLocations extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vendor_locations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'vendor_loc_no', 'vendor_account', 'vendor_loc_name'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'vendor_account', 'currency', 'pli', 'local_union', 'no_entry_zone', 'no_entry_timings', 'city', 'location_type', 'bill_currency', 'gstin_status', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime', 'lease_expiry_date'], 'safe'],
            [['exchange_rate', 'area_sf'], 'number'],
            [['vendor_loc_no', 'type_of_location', 'quote', 'spoc_email', 'escalation_name', 'escalation_email', 'plot_number', 'building_name', 'landmark', 'loc_fin_invoice_name', 'loc_fin_invoice_number', 'loc_fin_invoice_email', 'loc_escalation_first_level_name', 'loc_escalation_first_level_number', 'loc_escalation_first_level_email', 'country', 'city_state', 'state'], 'string', 'max' => 200],
            [['vendor_loc_name', 'legal_entity_name', 'spoc_name', 'material_rec_name', 'primary_location', 'cust_location_owner', 'place_of_supply'], 'string', 'max' => 255],
            [[ 'address'], 'string', 'max' => 3000],
            [['spoc_number', 'escalation_number'], 'string', 'max' => 15],
            [['floor', 'area_sector_name', 'city_short_name', 'state_code', 'CE_regn_no'], 'string', 'max' => 100],
            [['pincode', 'pan_no'], 'string', 'max' => 10],
            [['gstin_no_uin', 'tin_no', 'cin_no'], 'string', 'max' => 50],
            // added for handling blank values saving in by ptpatel on date 24-01-2026
            [['vendor_account'], 'trim'],
            [['vendor_account'], 'required', 'message' => 'Vendor Account Name cannot be blank.'],
            [['vendor_account'], 'integer', 'message' => 'Vendor Account Name must be a number.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'vendorloc_id' => 'Vendorloc ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'vendor_loc_no' => 'Vendor Loc No',
            'vendor_account' => 'Vendor Account',
            'vendor_loc_name' => 'Vendor Loc Name',
            'type_of_location' => 'Type Of Location',
            'legal_entity_name' => 'Legal Entity Name',
            'currency' => 'Currency',
            'exchange_rate' => 'Exchange Rate',
            'quote' => 'Quote',
            'area_sf' => 'Area Sf',
            'spoc_name' => 'Spoc Name',
            'spoc_number' => 'Spoc Number',
            'spoc_email' => 'Spoc Email',
            'escalation_name' => 'Escalation Name',
            'escalation_number' => 'Escalation Number',
            'escalation_email' => 'Escalation Email',
            'lease_expiry_date' => 'Lease Expiry Date',
            'material_rec_name' => 'Material Rec Name',
            'pli' => 'Pli',
            'plot_number' => 'Plot Number',
            'building_name' => 'Building Name',
            'floor' => 'Floor',
            'area_sector_name' => 'Area Sector Name',
            'landmark' => 'Landmark',
            'city_short_name' => 'City Short Name',
            'local_union' => 'Local Union',
            'no_entry_zone' => 'No Entry Zone',
            'no_entry_timings' => 'No Entry Timings',
            'loc_fin_invoice_name' => 'Loc Fin Invoice Name',
            'loc_fin_invoice_number' => 'Loc Fin Invoice Number',
            'loc_fin_invoice_email' => 'Loc Fin Invoice Email',
            'loc_escalation_first_level_name' => 'Loc Escalation First Level Name',
            'loc_escalation_first_level_number' => 'Loc Escalation First Level Number',
            'loc_escalation_first_level_email' => 'Loc Escalation First Level Email',
            'primary_location' => 'Primary Location',
            'cust_location_owner' => 'Cust Location Owner',
            'address' => 'Address',
            'country' => 'Country',
            'city_state' => 'City State',
            'city' => 'City',
            'pincode' => 'Pincode',
            'location_type' => 'Location Type',
            'state' => 'State',
            'state_code' => 'State Code',
            'place_of_supply' => 'Place Of Supply',
            'CE_regn_no' => 'Ce Regn No',
            'bill_currency' => 'Bill Currency',
            'gstin_status' => 'Gstin Status',
            'gstin_no_uin' => 'Gstin No Uin',
            'pan_no' => 'Pan No',
            'tin_no' => 'Tin No',
            'cin_no' => 'Cin No',
            'deleted' => 'Deleted',
        ];
    }
}
