<?php
/**view files are bcoming clumsy to manage action buttons. This file is an attempt to move that clutter to this location and 
 * may be eventually to database
 */
namespace app\models;
use Yii;

class CheckAllowActions
{
	private function getAccountColumn($moduleName)
    {
        return \app\models\ModuleAccountColumnMap::find()
            ->select('column_name')
            ->where(['module_name' => $moduleName, 'status' => 1])
            ->scalar();
    }
	function checkAllowedActions($TabId, $ModuleName, $Recordid, $Record, $ExportPermission = null, $ImportPermission = null)
	{
		$user_role = Yii::$app->user->identity->role ?? null;
		$user_id = Yii::$app->user->id;
		$actions = [];
		$actions["AllowEditGeneral"] = false;
		 // -------------------------------
        // 1. MASTER MODULE ALWAYS EDITABLE
        // -------------------------------
		//check if master type then edit all allowed
		$sql_master ="select count(*) as cnt from  `tab` WHERE tabid = :TabId and`parent` LIKE '10' ";
            $rec_master = Yii::$app->db->createCommand($sql_master)->bindValue(':TabId', $TabId)->queryOne();
            if ($rec_master && $rec_master['cnt'] >0 ) {
               $actions["AllowEditGeneral"] = true;
            }
		

		 // -------------------------------
        // 2. DYNAMIC ACCOUNT COLUMN CHECK
        // -------------------------------
        // Fetch account column for this module from DB
        $accountColumn = $this->getAccountColumn($ModuleName);

        if ($accountColumn) {
			

            // Extract the account value from the record
            $recordAccountValue = $Record[$accountColumn] ?? null;

            if ($recordAccountValue) {

                // Check if user belongs to this vendor account
                $sql_v = "SELECT COUNT(*) AS cnt 
                          FROM vendor_account_orgaisation_section
                          WHERE vendoraccid = :VendorAccId
                          AND userid = :UserId order by va_org_id desc limit 1";

                $records_list = Yii::$app->db->createCommand($sql_v)
                    ->bindValue(':VendorAccId', $recordAccountValue)
                    ->bindValue(':UserId', $user_id)
                    ->queryOne();
					
				if ($records_list && $records_list['cnt'] > 0) {
                    $actions['AllowEditGeneral'] = true;
                }
            }
        }

            
		
		if ($TabId == 6) {
			//data wiping case
			$actions["Edit"] = false;
			$actions["DataWipingCompletd"] = false;
			$wiping_status = $Record->wiping_status ?? null;
			$fe_user = $Record->fe_name ?? null;
			$hdd_count = $Record->hdd_count ?? null;
			$hdd_completed = $Record->hdd_completed ?? null;
			if (($wiping_status == 3 || $wiping_status == 4) && $user_role == 'H56' && $fe_user == $user_id) {
				$actions["Edit"] = true;
			}
			if (empty($wiping_status) || $wiping_status == 2) {
				$actions["Edit"] = true;
			}
			if ($wiping_status == 5 || $wiping_status == 6 || $wiping_status == 7 || $wiping_status == 8) {
				$actions["Edit"] = false;
			}
			if ($wiping_status == 4 && ($hdd_completed == $hdd_count) && $user_role == 'H56' && $fe_user == $user_id) {
				$actions["DataWipingCompletd"] = true;
				$actions["DataWipingClientSignature"] = true;
			}

		}
		if ($TabId == 3) {
			//drilling case
			$actions["Edit"] = false;
			$actions["DrillingCompletd"] = false;
			$drilling_status = $Record->drilling_status ?? null;
			$fe_user = $Record->fe_name ?? null;
			$hdd_count = $Record->hdd_count ?? null;
			$hdd_completed = $Record->hdd_completed ?? null;
			if (($drilling_status == 3 || $drilling_status == 4) && $user_role == 'H56' && $fe_user == $user_id) {
				$actions["Edit"] = true;
			}
			if (empty($drilling_status) || $drilling_status == 2) {
				$actions["Edit"] = true;
			}
			if ($drilling_status == 5 || $drilling_status == 6 || $drilling_status == 7 || $drilling_status == 8) {
				$actions["Edit"] = false;
			}
			if ($drilling_status == 4 && ($hdd_completed == $hdd_count) && $user_role == 'H56' && $fe_user == $user_id) {
				$actions["DrillingCompletd"] = true;
				$actions["DrillingClientSignature"] = true;
			}

		}
		if ($TabId == 5) {
			//shredding case
			$actions["Edit"] = false;
			$actions["ShreddingCompletd"] = false;
			$shredding_status = $Record->shredding_status ?? null;
			$fe_user = $Record->fe_name ?? null;
			$hdd_count = $Record->hdd_count ?? null;
			$hdd_completed = $Record->hdd_completed ?? null;
			if (($shredding_status == 3 || $shredding_status == 4) && $user_role == 'H56' && $fe_user == $user_id) {
				$actions["Edit"] = true;
			}
			if (empty($shredding_status) || $shredding_status == 2) {
				$actions["Edit"] = true;
			}
			if ($shredding_status == 5 || $shredding_status == 6 || $shredding_status == 7 || $shredding_status == 8) {
				$actions["Edit"] = false;
			}
			if ($shredding_status == 4 && ($hdd_completed == $hdd_count) && $user_role == 'H56' && $fe_user == $user_id) {
				$actions["ShreddingCompletd"] = true;
				$actions["ShreddingClientSignature"] = true;
			}

		}
		if ($TabId == 4) {
			//degaussing case
			$actions["Edit"] = false;
			$actions["DegaussingCompletd"] = false;
			$degaussing_status = $Record->degaussing_status ?? null;
			$fe_user = $Record->fe_name ?? null;
			$hdd_count = $Record->hdd_count ?? null;
			$hdd_completed = $Record->hdd_completed ?? null;
			if (($degaussing_status == 3 || $degaussing_status == 4) && $user_role == 'H56' && $fe_user == $user_id) {
				$actions["Edit"] = true;
			}
			if (empty($degaussing_status) || $degaussing_status == 2) {
				$actions["Edit"] = true;
			}
			if ($degaussing_status == 5 || $degaussing_status == 6 || $degaussing_status == 7 || $degaussing_status == 8) {
				$actions["Edit"] = false;
			}
			if ($degaussing_status == 4 && ($hdd_completed == $hdd_count) && $user_role == 'H56' && $fe_user == $user_id) {
				$actions["DegaussingCompletd"] = true;
				$actions["DegaussingClientSignature"] = true;
			}

		}
		if ($TabId == 24) {
			$actions['AllowFormSix'] = false;
			$actions['AllowFormTen'] = false;
			$actions['AllowGreenCertificate'] = false;
			$actions['SchedulePickup'] = false;
			$actions['AllowBulkImportLaptop'] = false;
			$actions['AllowBulkImportDesktop'] = false;
			$actions['AllowBulkImportTft'] = false;
			$actions['AllowBulkImportRandom'] = false;
			$pickup_status = $Record->pickup_status ?? null;
			$sourcing_deal = $Record->opportuity_name ?? null;
			$waste_catgory_sql = "SELECT products.waste_catagory,
					prod_waste_catagory.waste_catagory_value
					FROM pickup_asset_detail 
					left join products on products.products_id = pickup_asset_detail.porduct_name 
					left join prod_waste_catagory on prod_waste_catagory.waste_catagory_id = products.waste_catagory
					WHERE pickup_asset_detail.pickup_id = :record_id and pickup_asset_detail.deleted=0";
			if ($pickup_status == 5 && $user_role == "H56") {
				$newmod = new ShippedDetails();
				$ShippedDetailsData = $newmod->find()->where(["pickup_id" => $Recordid])->all();
				if (count($ShippedDetailsData) == 0) {
					$actions['AllowCertificateGeneration'] = false;
				} else {
					$actions['AllowCertificateGeneration'] = true;
					foreach ($ShippedDetailsData as $sd => $sd_val) {
						$transporter_name = $sd_val->transporter_name ?? "";
						$vehicle_size = $sd_val->vehicle_size ?? "";
						$shippment_mode = $sd_val->shippment_mode ?? "";
						$docket_number = $sd_val->docket_number ?? "";
						$seal_number = $sd_val->seal_number ?? "";
						$vehicle_number = $sd_val->vehicle_number ?? "";
						$shipped_date = $sd_val->shipped_date ?? "";
						$estimate_delivery_date = $sd_val->estimate_delivery_date ?? "";
						$delivery_date = $sd_val->delivery_date ?? "";
						$status = $sd_val->status ?? "";
						//$status !=3 || this condition is removed
						if (empty($transporter_name) || empty($vehicle_size) || empty($shippment_mode) || empty($docket_number) || empty($seal_number) || empty($vehicle_number) || empty($estimate_delivery_date)) {
							$actions['AllowCertificateGeneration'] = false;
							break;
						}
					}
				}
			}
			if ($pickup_status == 5 && $user_role == "H56") {

				$newmod = new ShippedDetails();
				$ShippedDetailsData = $newmod->find()->where(["pickup_id" => $Recordid])->all();
				if (count($ShippedDetailsData) == 0) {
					$actions['AllowFormSix'] = false;
				} else {
					$actions['AllowFormSix'] = true;
					foreach ($ShippedDetailsData as $sd => $sd_val) {
						$transporter_name = $sd_val->transporter_name ?? "";
						$vehicle_size = $sd_val->vehicle_size ?? "";
						$shippment_mode = $sd_val->shippment_mode ?? "";
						$docket_number = $sd_val->docket_number ?? "";
						$seal_number = $sd_val->seal_number ?? "";
						$vehicle_number = $sd_val->vehicle_number ?? "";
						$shipped_date = $sd_val->shipped_date ?? "";
						$estimate_delivery_date = $sd_val->estimate_delivery_date ?? "";
						$delivery_date = $sd_val->delivery_date ?? "";
						$status = $sd_val->status ?? "";
						//$status !=3 || this condition is removed
						if (empty($transporter_name) || empty($vehicle_size) || empty($shippment_mode) || empty($docket_number) || empty($seal_number) || empty($vehicle_number) || empty($estimate_delivery_date)) {
							$actions['AllowFormSix'] = false;
							break;
						}
					}
				}
			}

			if ($pickup_status == 5 && $user_role == "H56") {
				$newmod = new ShippedDetails();
				$ShippedDetailsData = $newmod->find()->where(["pickup_id" => $Recordid])->all();
				if (count($ShippedDetailsData) == 0) {
					$actions['AllowFormTen'] = false;
				} else {
					$actions['AllowFormTen'] = true;
					foreach ($ShippedDetailsData as $sd => $sd_val) {
						$transporter_name = $sd_val->transporter_name ?? "";
						$vehicle_size = $sd_val->vehicle_size ?? "";
						$shippment_mode = $sd_val->shippment_mode ?? "";
						$docket_number = $sd_val->docket_number ?? "";
						$seal_number = $sd_val->seal_number ?? "";
						$vehicle_number = $sd_val->vehicle_number ?? "";
						$shipped_date = $sd_val->shipped_date ?? "";
						$estimate_delivery_date = $sd_val->estimate_delivery_date ?? "";
						$delivery_date = $sd_val->delivery_date ?? "";
						$status = $sd_val->status ?? "";
						//$status !=3 || this condition is removed
						if (empty($transporter_name) || empty($vehicle_size) || empty($shippment_mode) || empty($docket_number) || empty($seal_number) || empty($vehicle_number) || empty($estimate_delivery_date)) {
							$actions['AllowFormTen'] = false;
							break;
						}
					}
				}
			}
			if ($pickup_status == 6) {
				$actions['AllowFormSix'] = true;
				$actions['AllowFormTen'] = true;
			}
			if ($pickup_status == 12 && $user_role == "H53") {
				$actions['SchedulePickup'] = true;
			}
			$pickup_assets = [];
			if ($actions['AllowFormSix'] || $actions['AllowFormTen']) {
				$connection = Yii::$app->db;
				$assets_command = $connection->createCommand($waste_catgory_sql)->bindValues([":record_id" => $Recordid]);
				$pickup_assets = $assets_command->queryAll();
				if (empty($pickup_assets)) {
					$actions['AllowFormSix'] = false;
					$actions['AllowFormTen'] = false;
				}
			}
			if ($actions['AllowFormSix']) {
				foreach ($pickup_assets as $i => $pa) {
					$waste_category = $pa["waste_catagory_value"];
					if ($waste_category == "G-Waste" || $waste_category == "E-Waste") {
						$actions['AllowFormSix'] = true;
						break;
					} else {
						$actions['AllowFormSix'] = false;
					}
				}
			}
			if ($actions['AllowFormTen']) {
				foreach ($pickup_assets as $i => $pa) {
					$waste_category = $pa["waste_catagory_value"];
					if ($waste_category == "H-Waste") {
						$actions['AllowFormTen'] = true;
						break;
					} else {
						$actions['AllowFormTen'] = false;
					}
				}
			}
			$newmod = new Grn();
			$GrnData = $newmod->find()->where(["pickup_id" => $Recordid, "deleted" => 0])->all();
			if (count($GrnData) == 0) {
				$actions['AllowGreenCertificate'] = false;
			} else {
				$actions['AllowGreenCertificate'] = true;
			}
			//echo "user_role = $user_role,sd =  $sourcing_deal, stt =  $pickup_status";exit;
			if ($ImportPermission && $user_role == "H56" && $sourcing_deal && $pickup_status != 6) {
				$LaptopData = PickupFullProductDetailLaptop::find()->where(['pickup_id' => $Recordid])->all();
				if (!empty($LaptopData)) {
					$actions['AllowBulkImportLaptop'] = true;
					$actions['AllowBulkImportDesktop'] = false;
					$actions['AllowBulkImportTft'] = false;
					$actions['AllowBulkImportRandom'] = false;
				}
				$DesktopData = PickupFullProductDetailDesktop::find()->where(['pickup_id' => $Recordid])->all();
				if (!empty($DesktopData)) {
					$actions['AllowBulkImportDesktop'] = true;
				}
				$TftData = PickupFullProductDetailTft::find()->where(['pickup_id' => $Recordid])->all();
				if (!empty($TftData)) {
					$actions['AllowBulkImportTft'] = true;
				}
				$RandomData = PickupRandomProductDetail::find()->where(['pickup_id' => $Recordid])->all();
				if (!empty($RandomData)) {
					$actions['AllowBulkImportRandom'] = true;
				}
			}
		}
		if ($TabId == 2) {
			$sourcing_deal = $Record->sourcing_deal ?? null;
			if ($sourcing_deal) {
				$product_costing_id = (new \yii\db\Query())
					->select('product_costing_id')
					->from('product_costing')
					->where(['related_to' => 51, 'related_to_id' => $sourcing_deal])
					->scalar();
				if ($product_costing_id)
					$url = Yii::$app->request->baseUrl . "/productdetail/detail?Record=$product_costing_id&sourcemodule=51&sourceid=$sourcing_deal";
				else
					$url = '';
			}
			if ($url)
				$actions['ShowProductDetail'] = $url;
			else
				$actions['ShowProductDetail'] = '';

			//check if stage = 3 logistics pending or inspection in process = 4 added on 22 sept 2025
			$stage = $Record->stages ?? null;
			//commented on 22 sept 2025 
			//check if fe then show buttons to add products
			// $fe_user = $Record->ownerid ?? null;
			// if ($user_role == 'H56' && $fe_user == $user_id) {
			// 	$actions["AddLaptopDetail"] = true;
			// 	$actions["AddDesktopDetail"] = true;
			// 	$actions["AddTFTDetail"] = true;
			// 	$actions["AddGeneralDetail"] = true;
			// }
			if ($stage == 3 or $stage == 4) {
				$actions["AddLaptopDetail"] = true;
				$actions["AddDesktopDetail"] = true;
				$actions["AddTFTDetail"] = true;
				$actions["AddGeneralDetail"] = true;
			}
			//check if stage == Pav completed
			$stage = $Record->stages ?? null;
			if ($stage == '8') {
				$actions["ExportLaptopDetail"] = true;
				$actions["ExportDesktopDetail"] = true;
				$actions["ExportTFTDetail"] = true;
				$actions["ExportGeneralDetail"] = true;
			}
		}
		if ($TabId == 8) {

			//check if solution team profile then show buttons to add products

			//check team responsible
			if ($Record['opportunity_stage'] == '2') {
				$team_responsible = explode(",", $Record['team_responsible']);
				if (!empty($team_responsible)) {
					foreach ($team_responsible as $tm) {
						if ($tm == '1')//solution team
						{
							if (!empty($Record['sa_assigned']) && !empty($Record['sf_assigned']))
								$actions["ApproveOpportuntiy"] = true;
							else {
								$actions["ApproveOpportuntiy"] = false;
								break;
							}

						}


						if ($tm == '2')//procurement team
						{
							if (!empty($Record['procurement_team_member']))
								$actions["ApproveOpportuntiy"] = true;
							else {
								$actions["ApproveOpportuntiy"] = false;
								break;
							}


						}


					}
				}
				$actions["RejectOpportunity"] = true;
			}


			if ($Record['opportunity_stage'] == '4') {
				//check team responsible
				$team_responsible = explode(",", $Record['team_responsible']);
				if (!empty($team_responsible)) {
					foreach ($team_responsible as $tm) {
						if ($tm == '1' && ($Record['sa_assigned'] == $user_id))//solution team
						{
							$ex_pd = "Select count(*) as cnt from  `opportunity_pricing_done` where userid=:userid and opportunity_id=:opportunity_id";
							$exs = Yii::$app->db->createCommand($ex_pd)->bindValue(":userid", $user_id)->bindValue(":opportunity_id", $Recordid)->queryOne();
							$cnt_s = $exs['cnt'];

							if ($cnt_s == 0) {
								$actions["EditOpportunity"] = true;
								break;
							}
						}
						if ($tm == '1' && $Record['sf_assigned'] == $user_id)//solution team
						{


							$ex_pd = "Select count(*) as cnt from  `opportunity_pricing_done` where userid=:userid and opportunity_id=:opportunity_id";
							$exs = Yii::$app->db->createCommand($ex_pd)->bindValue(":userid", $user_id)->bindValue(":opportunity_id", $Recordid)->queryOne();
							$cnt_s = $exs['cnt'];

							if ($cnt_s == 0) {
								$actions["EditOpportunity"] = true;
								break;
							}
						}

						if ($tm == '2' && $Record['procurement_team_member'] == $user_id)//procurement team
						{
							$ex_pd = "Select count(*) as cnt from  `opportunity_pricing_done` where userid=:userid and opportunity_id=:opportunity_id";
							$exs = Yii::$app->db->createCommand($ex_pd)->bindValue(":userid", $user_id)->bindValue(":opportunity_id", $Recordid)->queryOne();
							$cnt_s = $exs['cnt'];

							if ($cnt_s == 0) {
								$actions["EditOpportunity"] = true;
								break;
							}
						}


					}
				}
				//print_r($actions);die;
			}
		}
		//code added for contract approve and reject by ptpatel on date 17-06-25
		if ($TabId == 12) { //12 -contract

			// if($Record['ownerid'] == $user_id)
			// {
			// 	$actions["Edit"] = true;
			// }
			if ($Record['contract_status'] == '2' && $Record['ownerid'] == $user_id) {
				$actions["RejectContract"] = true;
				$actions["ApproveContract"] = true;
			} else {
				$actions["RejectContract"] = false;
				$actions["ApproveContract"] = false;
			}

		}
		//code added for deliverychallan approve and reject by ptpatel on date 04-07-25
		if ($TabId == 88) { //88 -deliverychallandit
			if ($Record['status'] == '2' && $Record['ownerid'] == $user_id) {
				$actions["RejectDeliverychallandit"] = true;
				$actions["ApproveDeliverychallandit"] = true;
			} else {
				$actions["RejectDeliverychallandit"] = false;
				$actions["ApproveDeliverychallandit"] = false;
			}

		}
		//code added for FOC approve and reject by ptpatel on date 010-07-25
		if ($TabId == 91) { //91 -FOC
			if ($Record['stage'] == '2' && $Record['ownerid'] == $user_id) {
				$actions["RejectFocdit"] = true;
				$actions["ApproveFocdit"] = true;
			} else {
				$actions["RejectFocdit"] = false;
				$actions["ApproveFocdit"] = false;
			}

		}

		if ($TabId == 79) {//grndit
			if ($ImportPermission) {
				$GrnditBarcodes = GrnditBarcodes::find()
					->where(['grndit_id' => $Recordid])
					->andWhere([
						'or',
						['bar_code' => ''],
						['bar_code' => null]
					])
					->all();

				//also check status
				if ($Record['status'] != '1' && $Record['status'] != '2') {
					$actions['AllowBulkImportGrnditBarcodes'] = true;

				}
				if (!empty($GrnditBarcodes)) {
					$actions['AllowBulkImportGrnditBarcodes'] = true;
				}
				// $actions['AllowBulkImportGrnditBarcodes'] = true;

				// echo $actions['AllowBulkImportGrnditBarcodes'];die;
			}
		}
		//code added for invoicedit approve and reject by deepika on date 15-07-25
		if ($TabId == 87) { //87 -invoicedit
			if ($Record['invoice_status'] == '2' && $Record['ownerid'] == $user_id) {
				$actions["RejectInvoicedit"] = true;
				$actions["ApproveInvoicedit"] = true;
			} else {
				$actions["RejectInvoicedit"] = false;
				$actions["ApproveInvoicedit"] = false;
			}
			if ($Record['invoice_status'] > 3) {
				$actions["AllowgeneratepdfInvoicedit"] = true;
			}

		}
		//code added for contact and user to reset password by ptpatel on date 04-09-25
		if ($TabId == 19) { //19 -contact module
			if ($Record['password'] != '' && $Record['password'] != null) {
				$actions["AllowResetPassword"] = true;
			} else {
				$actions["AllowResetPassword"] = false;
			}

		}
		if ($TabId == 41) { //41 -user module
			if ($Record['password_hash'] != '' && $Record['password_hash'] != null) {
				$actions["AllowUserResetPassword"] = true;
			} else {
				$actions["AllowUserResetPassword"] = false;
			}

		}
		if ($TabId == 97) { //97 -raiserequestclient
			if ($Record['status'] == '2' && $Record['ownerid'] == $user_id) {
				$actions["RejectRaiseRequestByClient"] = true;
				$actions["ApproveRaiseRequestByClient"] = true;
			} else {
				$actions["RejectRaiseRequestByClient"] = false;
				$actions["ApproveRaiseRequestByClient"] = false;
			}

		}
		if ($TabId == 96) { //96 -raiserequestvendor
			if ($Record['status'] == '2' && $Record['ownerid'] == $user_id) {
				$actions["RejectRaiseRequestByVendor"] = true;
				$actions["ApproveRaiseRequestByVendor"] = true;
			} else {
				$actions["RejectRaiseRequestByVendor"] = false;
				$actions["ApproveRaiseRequestByVendor"] = false;
			}

		}
		//code added for contact and user to reset password by ptpatel on date 04-09-25
		//added code to allow edit to management section in account module on 5 sept 2025
		// if ($TabId == 18) {
		if ($TabId == 18 && ($Record['acc_status'] != '5' && $Record['acc_status'] != '6' && $Record['acc_status'] != '7' && $Record['acc_status'] != '8')) {

			//management section can also edit added on 9 set 2025 
			$sql_v = "SELECT count(*) as cnt FROM `vendor_account_orgaisation_section` where vendoraccid=:RecordId and userid=:uid limit 1";
			$records_list = Yii::$app->db->createCommand($sql_v)
				->bindValue(':RecordId', $Recordid)
				->bindValue(':uid', $user_id)
				->queryOne();
			if ($records_list && $records_list['cnt'] > 0) {
				$org_ids = $records_list['cnt'];
				if ($org_ids > 0) {
					$actions['AllowEditGeneral'] = true;
				}
			}
			

		}
		// if ($TabId == 29 || $TabId == 19 || $TabId == 12) {//vendorlocations || contacts || contracts

		// 	//management section can also edit added on 9 set 2025 
		// 	if ($TabId == 29) {
		// 		$sql_v = "SELECT count(*) as cnt FROM `vendor_account_orgaisation_section` where vendoraccid=(select vendor_account from vendor_locations where vendorloc_id = :RecordId limit 1) and userid=:uid";
		// 		$records_list = Yii::$app->db->createCommand($sql_v)
		// 			->bindValue(':RecordId', $Recordid)
		// 			->bindValue(':uid', $user_id)
		// 			->queryOne();
		// 	} else if ($TabId == 19) {
		// 		$sql_v = "SELECT count(*) as cnt FROM `vendor_account_orgaisation_section` where vendoraccid=(select vendor_account_name from contacts where contacts_id= :RecordId limit 1) and userid=:uid";
		// 		$records_list = Yii::$app->db->createCommand($sql_v)
		// 			->bindValue(':RecordId', $Recordid)
		// 			->bindValue(':uid', $user_id)
		// 			->queryOne();
		// 	} else if ($TabId == 12) {
		// 		$sql_v = "SELECT count(*) as cnt FROM `vendor_account_orgaisation_section` where vendoraccid=(select account_name from contracts where contract_id= :RecordId limit 1) and userid=:uid";
		// 		$records_list = Yii::$app->db->createCommand($sql_v)
		// 			->bindValue(':RecordId', $Recordid)
		// 			->bindValue(':uid', $user_id)
		// 			->queryOne(); 
		// 	}
		// 	if ($records_list && $records_list['cnt'] > 0) {
		// 		$org_ids = $records_list['cnt'];
		// 		if ($org_ids > 0) {
		// 			$actions['AllowEditGeneral'] = true;
		// 		}
		// 	}

		// }
		if ($TabId == 14) { //14 -salesorder vishwas
			if ($Record['stage'] == '2' && $Record['ownerid'] == $user_id) {
				$actions["RejectSalesOrder"] = true;
				$actions["ApproveSalesOrder"] = true;
			} else {
				$actions["RejectSalesOrder"] = false;
				$actions["ApproveSalesOrder"] = false;
			}
		}
		
		return $actions;
	}
}
