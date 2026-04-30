<?php

use yii\helpers\Url;
use yii\helpers\Html;
use backend\assets\AdminAsset;
use app\models\ListHire;
use app\models\Reference;

AdminAsset::register($this);
$this->title = Yii::t('app', $Tabname. " Detail");

$url =Url::to(['Edit']);
$urlApprove =Url::to(['approvelead']);
$this->registerCssFile('@web/thememain/css/listview.css', ['depends' => [AdminAsset::class]]);
$baseUrl = Yii::$app->HomeUrl; 
// echo "<pre>";
// print_r($Record);
$fullname = $Record["firstname"]." ".$Record["lastname"];
$csrfTokenName = Yii::$app->request->csrfParam;  // This replaces csrfTokenName
$csrfToken = Yii::$app->request->csrfToken;      // Get the CSRF token itself
?>
<style>
    /*  LEAD DETAILS CSS */
    .div-rectangle-33 {
        position: absolute;
        width: 100%;
        height: 77px;
        top: 85px;
        left: 97px;
        font-size: 0px;
        background: #ffffff;
        z-index: 2;
    }

    .span-lead {
        display: block;
        position: relative;
        height: 18px;
        margin: 6px 0 0 72px;
        color: #000000;
        font-family: Poppins, var(--default-font-family);
        font-size: 12px;
        font-weight: 500;
        line-height: 17.988px;
        text-align: left;
        text-transform: capitalize;
        white-space: nowrap;
        letter-spacing: 0.36px;
        z-index: 23;
    }

    .div-flex-row-cf {
        position: relative;
        width: 1269px;
        height: 39px;
        margin: -9px 0 0 25px;
        z-index: 16;
    }

    .div-lead-svgrepo {
        position: absolute;
        width: 36px;
        height: 36px;
        top: 0;
        left: 0;
        z-index: 16;
        overflow: hidden;
    }

    .div-group {
        position: relative;
        width: 36px;
        height: 36px;
        margin: 0 0 0 0px;
        z-index: 17;
        overflow: visible auto;
    }

    .div-group-34 {
        position: relative;
        width: 6.48px;
        height: 6.48px;
        margin: 7.203px 0 0 14.76px;
        z-index: 19;
    }


    .div-vector-35 {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;

        background: url("<?= Yii::getAlias('@web/thememain/images/5e087009-64d5-448b-8623-9969421c7382.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 20;
    }

    .div-group-36 {
        position: relative;
        width: 21.619px;
        height: 12.971px;
        margin: 2.157px 0 0 7.189px;
        z-index: 21;
    }

    .div-vector-37 {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        background: url("<?= Yii::getAlias('@web/thememain/images/594e51cd-403e-445a-941c-6792783d17b5.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 22;
    }

    .div-ellipse {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        background: url("<?= Yii::getAlias('@web/thememain/images/8122fed4-dd1a-4422-bade-58ec10cdd482.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 18;
        border-radius: 50%;
    }

    .div-regroup {
        display: flex;
        align-items: center;
        justify-content: space-between;
        /*position: absolute;*/
        /*width: 124px;*/
        height: 39px;
        top: 0;
        left: 1052px;
        z-index: 9;
        gap:4px;
    }

    .button-frame {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        flex-shrink: 0;
        gap: 8px;
        position: relative;
        width: 71px;
        padding: 10px 10px 10px 10px;
        cursor: pointer;
        background: transparent;
        border: 0.5px solid #535353;
        z-index: 3;
        border-radius: 5px;
    }


    .span-convert {
        flex-basis: auto;
        position: relative;
        height: 18px;
        color: #000000;
        font-family: Poppins, var(--default-font-family);
        font-size: 12px;
        font-weight: 500;
        line-height: 17.988px;
        text-align: left;
        text-transform: capitalize;
        white-space: nowrap;
        letter-spacing: 0.36px;
        z-index: 4;
    }

    .button-frame-38 {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: nowrap;
        flex-shrink: 0;
        gap: 8px;
        position: relative;
        width: 44px;
        padding: 10px 10px 10px 10px;
        cursor: pointer;
        background: transparent;
        border: 0.5px solid #535353;
        z-index: 9;
        border-radius: 5px;
    }


    .span-edit {
        flex-shrink: 0;
        flex-basis: auto;
        position: relative;
        height: 18px;
        color: #000000;
        font-family: Poppins, var(--default-font-family);
        font-size: 12px;
        font-weight: 500;
        line-height: 17.988px;
        text-align: left;
        text-transform: capitalize;
        white-space: nowrap;
        letter-spacing: 0.36px;
        z-index: 10;
    }

    .div-frame {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: nowrap;
        gap: 8px;
        /*position: absolute;*/
        width: 84px;
        height: 38px;
        top: 1px;
        left: 1185px;
        padding: 10px 13px 10px 13px;
        border: 0.5px solid #535353;
        z-index: 5;
        border-radius: 5px;
    }

    .span-more {
        flex-shrink: 0;
        flex-basis: auto;
        position: relative;
        height: 18px;
        color: #000000;
        font-family: Poppins, var(--default-font-family);
        font-size: 12px;
        font-weight: 500;
        line-height: 17.988px;
        text-align: left;
        text-transform: capitalize;
        white-space: nowrap;
        letter-spacing: 0.36px;
        z-index: 6;
    }

    .div-mdi-menu-down {
        flex-shrink: 0;
        position: relative;
        width: 24px;
        height: 24px;
        z-index: 7;
        overflow: hidden;
    }

    .div-vector-39 {
        position: relative;
        width: 10px;
        height: 5px;
        margin: 10px 0 0 7px;
        background: url("<?= Yii::getAlias('@web/thememain/images/d919a55a-b9b2-4cf2-86b3-19e6ff74fe81.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 8;
    }

    .span-mrs-swati-vispute {
        display: flex;
        align-items: flex-start;
        justify-content: flex-start;
        position: absolute;
        height: 53.85%;
        top: 25.64%;
        left: 3.7%;
        color: #000000;
        font-family: Poppins, var(--default-font-family);
        font-size: 14px;
        font-weight: 500;
        line-height: 20.986px;
        text-align: left;
        text-transform: capitalize;
        white-space: nowrap;
        letter-spacing: 0.42px;
        z-index: 12;
    }

    .div-flex-row-ec {
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        width: 63px;
        height: 15px;
        margin: -6px 0 0 72px;
        z-index: 14;
    }

    .div-gridicons-tag {
        flex-shrink: 0;
        position: relative;
        width: 14px;
        height: 14px;
        z-index: 14;
        overflow: hidden;
    }

    .div-vector-3a {
        position: relative;
        width: 11.662px;
        height: 11.662px;
        margin: 1.172px 0 0 1.172px;
        background: url("<?= Yii::getAlias('@web/thememain/images/aa8d4a0e-1078-4dbf-ae7b-a0022828fa4b.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 15;
    }

    .span-add-tag {
        flex-shrink: 0;
        position: relative;
        height: 15px;
        color: #a0a0a0;
        font-family: Poppins, var(--default-font-family);
        font-size: 10px;
        font-weight: 500;
        line-height: 14.99px;
        text-align: left;
        text-transform: capitalize;
        white-space: nowrap;
        letter-spacing: 0.3px;
        z-index: 13;
    }


    /* Description */
    .div-rectangle-3b {
        /*position: absolute;*/
        width: 100%;
        height: 140px;
        top: 360px;
        left: 97px;
        font-size: 0px;
        background: #ffffff;
        z-index: 29;
    }

    .span-description {
        display: block;
        position: relative;
        height: 18px;
        margin: 16px 0 0 0px;
        color: #a0a0a0;
        font-family: Poppins, var(--default-font-family);
        font-size: 12px;
        font-weight: 600;
        line-height: 17.988px;
        text-align: left;
        text-transform: capitalize;
        white-space: nowrap;
        letter-spacing: 0.36px;
        z-index: 28;
    }

  .open-lead {
    display: flex;
    align-items: flex-start;
    justify-content: flex-start;
    position: relative;
    width: 100%;
    height: 75px;
    margin: 4px 0 0 0px;
    color: #000000;
    font-family: Poppins, var(--default-font-family);
    font-size: 12px;
    font-weight: 500;
    line-height: 25px;
    text-align: left;
    text-transform: capitalize;
    letter-spacing: 0.36px;
}

    /* End Description */

    /* summary */
    .rectangle-1f {
        position: absolute;
        width: 100%;
        height: 9%;
        top: 70%;
        left: 96px;
        background: #ffffff;
        z-index: 167;
    }

    .flex-row-cc {
        position: relative;
        width: 787px;
        height: 36px;
        margin: 13px 0 0 19px;
        z-index: 191;
    }

    .uil-calender {
        position: absolute;
        width: 4.57%;
        height: 100%;
        top: 0;
        left: 59.85%;
        background: #b178fb;
        z-index: 180;
        overflow: hidden;
    }

    .vector-20 {
        position: relative;
        width: 21px;
        height: 21px;
        margin: 7.5px 0 0 7.5px;
        background: url("<?= Yii::getAlias('@web/thememain/images/914c1c4e-a57c-44e8-9f5c-4aabf3f8d7b9.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 181;
    }

    .foundation-mail {
        position: absolute;
        width: 4.57%;
        height: 100%;
        top: 0;
        left: 70.78%;
        background: var(--color-primary) !important;
        z-index: 182;
        overflow: hidden;
    }

    .vector-21 {
        position: relative;
        width: 23.247px;
        height: 18px;
        margin: 9px 0 0 6px;
        background: url("<?= Yii::getAlias('@web/thememain/images/4b4a4c80-247e-4c69-950b-a07427972893.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 183;
    }

    .line-md-file-document-filled {
        position: absolute;
        width: 4.57%;
        height: 100%;
        top: 0;
        left: 81.7%;
        background: #a0a0a0;
        z-index: 184;
        overflow: hidden;
    }

    .mask-group {
        position: relative;
        width: 24px;
        height: 24px;
        margin: 6px 0 0 6px;
        z-index: 185;
    }

    .oui-documents {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        z-index: 186;
    }

    .vector-22 {
        position: relative;
        width: 21px;
        height: 24px;
        margin: 0 0 0 1.5px;
        background: url("<?= Yii::getAlias('@web/thememain/images/2e3d0e76-849c-43e2-87cc-89c3480e6a77.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 187;
    }

   /* .frame {
        position: absolute;
        width: 4.57%;
        height: 100%;
        top: 0;
        left: 92.63%;
        background: #f8af92;
        z-index: 188;
    }*/

    .vector-23 {
        position: relative;
        width: 24px;
        height: 24px;
        margin: 6px 0 0 6px;
        background: url("<?= Yii::getAlias('@web/thememain/images/725c8f75-bdd5-4bc7-9a84-e0e3c12300bc.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 189;
    }

    .summary {
        display: flex;
        align-items: flex-start;
        justify-content: flex-start;
        position: absolute;
        height: 66.67%;
        top: 16.67%;
        left: 0;
        color: #3c77ff;
        font-family: Poppins, var(--default-font-family);
        font-size: 16px;
        font-weight: 500;
        line-height: 23.984px;
        text-align: left;
        text-transform: capitalize;
        white-space: nowrap;
        letter-spacing: 0.48px;
        z-index: 169;
    }

    .history {
        display: flex;
        align-items: flex-start;
        justify-content: flex-start;
        position: absolute;
        height: 66.67%;
        top: 16.67%;
        left: 15.88%;
        color: #000000;
        font-family: Poppins, var(--default-font-family);
        font-size: 16px;
        font-weight: 500;
        line-height: 23.984px;
        text-align: left;
        text-transform: capitalize;
        white-space: nowrap;
        letter-spacing: 0.48px;
        z-index: 170;
    }

    .rectangle-24 {
        position: absolute;
        width: 12px;
        height: 12px;
        top: 22px;
        left: 517px;
        background: #ff7986;
        z-index: 172;
        border-radius: 2px;
    }

    .text-1b {
        display: flex;
        align-items: flex-start;
        justify-content: flex-start;
        position: absolute;
        height: 12px;
        top: 0;
        left: 4px;
        color: #ffffff;
        font-family: Poppins, var(--default-font-family);
        font-size: 8px;
        font-weight: 500;
        line-height: 11.992px;
        text-align: left;
        text-transform: capitalize;
        white-space: nowrap;
        letter-spacing: 0.24px;
        z-index: 173;
    }

    .group-a {
        position: absolute;
        width: 12px;
        height: 12px;
        top: 22px;
        left: 603px;
        background: #ff7986;
        z-index: 175;
        border-radius: 2px;
    }

    .text-1c {
        display: flex;
        align-items: flex-start;
        justify-content: flex-start;
        position: absolute;
        height: 12px;
        top: 0;
        left: 4px;
        color: #ffffff;
        font-family: Poppins, var(--default-font-family);
        font-size: 8px;
        font-weight: 500;
        line-height: 11.992px;
        text-align: left;
        text-transform: capitalize;
        white-space: nowrap;
        letter-spacing: 0.24px;
        z-index: 176;
    }

    .section-8 {
        position: absolute;
        width: 12px;
        height: 12px;
        top: 22px;
        left: 689px;
        background: #ff7986;
        z-index: 178;
        border-radius: 2px;
    }

    .span {
        display: flex;
        align-items: flex-start;
        justify-content: flex-start;
        position: absolute;
        height: 12px;
        top: 0;
        left: 4px;
        color: #ffffff;
        font-family: Poppins, var(--default-font-family);
        font-size: 8px;
        font-weight: 500;
        line-height: 11.992px;
        text-align: left;
        text-transform: capitalize;
        white-space: nowrap;
        letter-spacing: 0.24px;
        z-index: 179;
    }

    .div-rectangle {
        position: absolute;
        width: 12px;
        height: 12px;
        top: 22px;
        left: 775px;
        background: #ff7986;
        z-index: 191;
        border-radius: 2px;
    }

    .span-25 {
        display: flex;
        align-items: flex-start;
        justify-content: flex-start;
        position: absolute;
        height: 12px;
        top: 0;
        left: 4px;
        color: #ffffff;
        font-family: Poppins, var(--default-font-family);
        font-size: 8px;
        font-weight: 500;
        line-height: 11.992px;
        text-align: left;
        text-transform: capitalize;
        white-space: nowrap;
        letter-spacing: 0.24px;
        z-index: 192;
    }

    .div-vector {
        position: relative;
        width: 93.5px;
        height: 3px;
        margin: 8.5px 0 0 14px;
        background: url("<?= Yii::getAlias('@web/thememain/images/00254cf3-52a0-4dd3-9792-9bdc702e6e76.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 168;
    }

    /* end summary */

    .flex-row-b {
        position: absolute;
        height: 493px;
        top: 584px;
        right: 0;
        /*left: 97px;*/
        left: 174px;
        z-index: 143;
    }

    .rectangle-3c {
        position: absolute;
        width: 479px;
        height: 489px;
        top: 0;
        left: 0;
        background: #ffffff;
        z-index: 143;
    }

    .frame-3d {
        position: relative;
        width: 457px;
        height: 476px;
        margin: 13px 0 0 11px;
        z-index: 144;
        overflow: hidden;
    }

    .summery-dropdown {
        position: relative;
        width: 457px;
        height: 208px;
        margin: 0 0 0 0;
        background: #ffffff;
        z-index: 145;
        overflow: visible auto;
    }

    .group-3e {
        position: relative;
        width: 435px;
        height: 38px;
        margin: 16px 0 0 11px;
        z-index: 146;
        border-radius: 5px;
    }

    .rectangle-3f {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        background: #f3f2f2;
        z-index: 147;
        border-radius: 5px;
    }

    .lead-information {
        display: flex;
        align-items: flex-start;
        justify-content: flex-start;
        position: absolute;
        height: 55.26%;
        top: 21.05%;
        left: 2.3%;
        color: #000000;
        font-family: Poppins, var(--default-font-family);
        font-size: 14px;
        font-weight: 500;
        line-height: 20.986px;
        text-align: left;
        text-transform: capitalize;
        white-space: nowrap;
        letter-spacing: 0.42px;
        z-index: 148;
    }

    .ep-arrow-down-40 {
        position: absolute;
        width: 4.6%;
        height: 52.63%;
        top: 23.68%;
        left: 91.26%;
        z-index: 149;
        overflow: hidden;
        border-radius: 5px;
    }

    .vector-40 {
        position: relative;
        width: 14.508px;
        height: 7.641px;
        margin: 6.484px 0 0 2.746px;
        background: url("<?= Yii::getAlias('@web/thememain/images/1112b03a-fa97-47f3-9aa6-3c7b3012f7c1.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 150;
    }

    .group-41 {
        position: relative;
        width: 435px;
        height: 34px;
        margin: 12px 0 0 11px;
        z-index: 151;
        border-radius: 5px;
    }

    .rectangle-42 {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        background: #f3f2f2;
        z-index: 152;
        border-radius: 5px;
    }

    .get-in-touch {
        display: flex;
        align-items: flex-start;
        justify-content: flex-start;
        position: absolute;
        height: 61.76%;
        top: 17.65%;
        left: 2.3%;
        color: #000000;
        font-family: Poppins, var(--default-font-family);
        font-size: 14px;
        font-weight: 500;
        line-height: 20.986px;
        text-align: left;
        text-transform: capitalize;
        white-space: nowrap;
        letter-spacing: 0.42px;
        z-index: 153;
    }

    .ep-arrow-down-43 {
        position: absolute;
        width: 4.6%;
        height: 58.82%;
        top: 20.59%;
        left: 91.26%;
        z-index: 154;
        overflow: hidden;
        border-radius: 5px;
    }

    .vector-44 {
        position: relative;
        width: 14.508px;
        height: 7.641px;
        margin: 6.484px 0 0 2.746px;
        background: url("<?= Yii::getAlias('@web/thememain/images/4ef88523-fe92-4b8e-9405-314d3bffc12a.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 155;
    }

    .group-45 {
        position: relative;
        width: 435px;
        height: 34px;
        margin: 12px 0 0 11px;
        z-index: 156;
        border-radius: 5px;
    }

    .rectangle-46 {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        background: #f3f2f2;
        z-index: 157;
        border-radius: 5px;
    }

    .ep-arrow-down-47 {
        position: absolute;
        width: 4.6%;
        height: 58.82%;
        top: 20.59%;
        left: 91.26%;
        z-index: 159;
        overflow: hidden;
        border-radius: 5px;
    }

    .vector-48 {
        position: relative;
        width: 14.508px;
        height: 7.641px;
        margin: 6.484px 0 0 2.746px;
        background: url("<?= Yii::getAlias('@web/thememain/images/0be4f1e6-9e32-4234-bec3-990efcb971ea.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 160;
    }

    .segment {
        display: flex;
        align-items: flex-start;
        justify-content: flex-start;
        position: absolute;
        height: 61.76%;
        top: 23.53%;
        left: 2.3%;
        color: #000000;
        font-family: Poppins, var(--default-font-family);
        font-size: 14px;
        font-weight: 500;
        line-height: 20.986px;
        text-align: left;
        text-transform: capitalize;
        white-space: nowrap;
        letter-spacing: 0.42px;
        z-index: 158;
    }

    .group-49 {
        position: relative;
        width: 435px;
        height: 34px;
        margin: 12px 0 0 11px;
        z-index: 161;
        border-radius: 5px;
    }

    .rectangle-4a {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        background: #f3f2f2;
        z-index: 162;
        border-radius: 5px;
    }

    .system-generated {
        display: flex;
        align-items: flex-start;
        justify-content: flex-start;
        position: absolute;
        height: 61.76%;
        top: 20.59%;
        left: 1.61%;
        color: #000000;
        font-family: Poppins, var(--default-font-family);
        font-size: 14px;
        font-weight: 500;
        line-height: 20.986px;
        text-align: left;
        text-transform: capitalize;
        white-space: nowrap;
        letter-spacing: 0.42px;
        z-index: 163;
    }

    .ep-arrow-down-4b {
        position: absolute;
        width: 4.6%;
        height: 58.82%;
        top: 20.59%;
        left: 91.26%;
        z-index: 164;
        overflow: hidden;
        border-radius: 5px;
    }

    .vector-4c {
        position: relative;
        width: 14.508px;
        height: 7.641px;
        margin: 6.488px 0 0 2.746px;
        background: url("<?= Yii::getAlias('@web/thememain/images/728d4810-2fbc-446c-b7a3-2d239db33298.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 165;
    }

    .frame-4d {
        position: absolute;
        width: 430px;
        height: 493px;
        top: 0;
        left: 488px;
        background: #ffffff;
        z-index: 66;
        overflow: hidden auto;
    }

    .rectangle-4e {
        position: relative;
        width: 401px;
        height: 38px;
        margin: 15px 0 0 14px;
        background: #e9e9e9;
        border-top: 1px solid #000000;
        z-index: 94;
    }

    .activity {
        display: flex;
        align-items: flex-start;
        justify-content: flex-start;
        position: absolute;
        height: 21px;
        top: 9px;
        left: 10px;
        color: #000000;
        font-family: Poppins, var(--default-font-family);
        font-size: 14px;
        font-weight: 600;
        line-height: 20.986px;
        text-align: left;
        text-transform: capitalize;
        white-space: nowrap;
        letter-spacing: 0.42px;
        z-index: 94;
    }

    .flex-row-f {
        position: relative;
        width: 400px;
        height: 41px;
        margin: 14px 0 0 14px;
        z-index: 88;
    }

    .frame-4f {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: nowrap;
        gap: 5px;
        position: absolute;
        width: 130px;
        height: 41px;
        top: 0;
        left: 0;
        padding: 10px 0 10px 0;
        cursor: pointer;
        background: transparent;
        border: 0.5px solid #000000;
        z-index: 84;
        border-radius: 5px;
    }

    .ion-call {
        flex-shrink: 0;
        position: relative;
        width: 20px;
        height: 20px;
        background: #f9beff;
        z-index: 85;
        overflow: hidden;
        border-radius: 3px;
    }

    .vector-50 {
        position: relative;
        width: 14.997px;
        height: 15px;
        margin: 2.5px 0 0 2.5px;
        background: url("<?= Yii::getAlias('@web/thememain/images/3225b502-967f-4d15-88a2-64c91fe6ddbb.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 86;
    }

    .add-calls {
        flex-shrink: 0;
        flex-basis: auto;
        position: relative;
        height: 21px;
        color: #000000;
        font-family: Poppins, var(--default-font-family);
        font-size: 14px;
        font-weight: 500;
        line-height: 20.986px;
        text-align: left;
        text-transform: capitalize;
        white-space: nowrap;
        letter-spacing: 0.42px;
        z-index: 87;
    }

    .frame-51 {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: nowrap;
        gap: 5px;
        position: absolute;
        width: 130px;
        height: 41px;
        top: 0;
        left: 135px;
        padding: 10px 0 10px 0;
        cursor: pointer;
        background: transparent;
        border: 0.5px solid #000000;
        z-index: 80;
        border-radius: 5px;
    }

    .simple-icons-gotomeeting {
        flex-shrink: 0;
        position: relative;
        width: 20px;
        height: 20px;
        background: #ffce86;
        z-index: 81;
        overflow: hidden;
        border-radius: 3px;
    }

    .vector-52 {
        position: relative;
        width: 12.011px;
        height: 13.333px;
        margin: 3.332px 0 0 4.166px;
        background: url("<?= Yii::getAlias('@web/thememain/images/5c09e741-9661-40ad-85cd-db77c2d27aaa.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 82;
    }

    .add-meeting {
        flex-shrink: 0;
        flex-basis: auto;
        position: relative;
        height: 21px;
        color: #000000;
        font-family: Poppins, var(--default-font-family);
        font-size: 14px;
        font-weight: 500;
        line-height: 20.986px;
        text-align: left;
        text-transform: capitalize;
        white-space: nowrap;
        letter-spacing: 0.42px;
        z-index: 83;
    }

    .frame-53 {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: nowrap;
        gap: 5px;
        position: absolute;
        width: 130px;
        height: 41px;
        top: 0;
        left: 270px;
        padding: 10px 0 10px 0;
        cursor: pointer;
        background: transparent;
        border: 0.5px solid #000000;
        z-index: 88;
        border-radius: 5px;
    }

    .pajamas-task-done {
        flex-shrink: 0;
        position: relative;
        width: 20px;
        height: 20px;
        background: #07bf97;
        z-index: 89;
        overflow: hidden;
        border-radius: 3px;
    }

    .vector-54 {
        position: relative;
        width: 16.051px;
        height: 15px;
        margin: 3.125px 0 0 2.5px;
        background: url("<?= Yii::getAlias('@web/thememain/images/7ec35286-3856-4459-85fa-6d6a84894741.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 90;
    }

    .add-task {
        flex-shrink: 0;
        flex-basis: auto;
        position: relative;
        height: 21px;
        color: #000000;
        font-family: Poppins, var(--default-font-family);
        font-size: 14px;
        font-weight: 500;
        line-height: 21px;
        text-align: left;
        text-transform: capitalize;
        white-space: nowrap;
        letter-spacing: 0.42px;
        z-index: 91;
    }

    .history-dropdown {
        position: relative;
        width: 400px;
        height: 87px;
        margin: 16px 0 0 14px;
        background: #ffffff;
        z-index: 67;
        overflow: visible auto;
    }

    .rectangle-55 {
        position: relative;
        width: 400px;
        height: 38px;
        margin: 0 0 0 0;
        background: #f3f2f2;
        z-index: 70;
        border-radius: 5px;
    }

    .upcoming-overdue {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        position: absolute;
        height: 55.26%;
        top: 23.68%;
        left: 2.5%;
        color: #000000;
        font-family: Poppins, var(--default-font-family);
        font-size: 14px;
        font-weight: 500;
        line-height: 20.986px;
        text-align: left;
        text-transform: capitalize;
        white-space: nowrap;
        letter-spacing: 0.42px;
        z-index: 71;
    }

    .ep-arrow-down-56 {
        position: absolute;
        width: 5%;
        height: 52.63%;
        top: 23.68%;
        left: 91.25%;
        z-index: 72;
        overflow: hidden;
        border-radius: 5px;
    }

    .vector-57 {
        position: relative;
        width: 14.508px;
        height: 7.641px;
        margin: 6.484px 0 0 2.748px;
        background: url("<?= Yii::getAlias('@web/thememain/images/fca3d3a1-f488-46b8-bab2-9506bd9b8298.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 73;
    }

    .rectangle-58 {
        position: relative;
        width: 400px;
        height: 38px;
        margin: 11px 0 0 0;
        background: #f3f2f2;
        z-index: 75;
        border-radius: 5px;
    }

    .ep-arrow-down-59 {
        position: absolute;
        width: 5%;
        height: 52.63%;
        top: 23.68%;
        left: 91.25%;
        z-index: 77;
        overflow: hidden;
        border-radius: 5px;
    }

    .vector-5a {
        position: relative;
        width: 14.508px;
        height: 7.641px;
        margin: 6.484px 0 0 2.748px;
        background: url("<?= Yii::getAlias('@web/thememain/images/1b63cddb-4b0c-4519-a3b9-dad3032dd0b1.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 78;
    }

    .october {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        position: absolute;
        height: 55.26%;
        top: 26.32%;
        left: 2.5%;
        color: #000000;
        font-family: Poppins, var(--default-font-family);
        font-size: 14px;
        font-weight: 500;
        line-height: 20.986px;
        text-align: left;
        text-transform: capitalize;
        white-space: nowrap;
        letter-spacing: 0.42px;
        z-index: 76;
    }

    .ellipse-5b {
        position: absolute;
        width: 0.75%;
        height: 7.89%;
        top: 50%;
        left: 19%;
        background: url("<?= Yii::getAlias('@web/thememain/images/5707a928-fe4c-42c6-a641-1c68c1e65010.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 79;
        border-radius: 50%;
    }

    .rectangle-5c {
        position: absolute;
        width: 417px;
        height: 493px;
        top: 0;
        right: 0;
        left: 926px;
        background: #ffffff;
        z-index: 97;
    }

    .rectangle-5d {
        position: relative;
        width: 389px;
        height: 38px;
        margin: 13px 0 0 14px;
        background: #e9e9e9;
        border-top: 1px solid #000000;
        z-index: 99;
    }

    .notes {
        display: flex;
        align-items: flex-start;
        justify-content: flex-start;
        position: absolute;
        height: 21px;
        top: 9px;
        right: 328px;
        color: #000000;
        font-family: Poppins, var(--default-font-family);
        font-size: 14px;
        font-weight: 600;
        line-height: 20.986px;
        text-align: left;
        text-transform: capitalize;
        white-space: nowrap;
        letter-spacing: 0.42px;
        z-index: 100;
    }

    .rectangle-5e {
        position: relative;
        width: 389px;
        height: 209px;
        margin: 13px 0 0 14px;
        background: #ffffff;
        border: 0.5px solid #535353;
        z-index: 108;
        overflow: visible auto;
        border-radius: 10px;
    }

    .rectangle-5f {
        position: relative;
        width: 389px;
        height: 34px;
        margin: 0 0 0 0;
        background: #d9d9d9;
        border: 0.5px solid #f3f2f2;
        z-index: 109;
        border-radius: 10px 10px 0 0;
    }

    .vector-60 {
        position: absolute;
        width: 1px;
        height: 22.5px;
        top: 5.5px;
        left: 345px;
        background: url("<?= Yii::getAlias('@web/thememain/images/af4c207c-e8dd-416e-a382-082d1dbfc977.png') ?>") no-repeat center;
        background-size: cover;
        z-index: 128;
    }

    .paragraph {
        display: flex;
        align-items: flex-start;
        justify-content: flex-start;
        position: absolute;
        height: 18px;
        top: 7.5px;
        right: 302.5px;
        color: #414141;
        font-family: Poppins, var(--default-font-family);
        font-size: 12px;
        font-weight: 500;
        line-height: 17.988px;
        text-align: left;
        text-transform: capitalize;
        white-space: nowrap;
        letter-spacing: 0.36px;
        z-index: 110;
    }

    .ph-list-bullets-bold {
        position: absolute;
        width: 16px;
        height: 16px;
        top: 9.5px;
        right: 91.5px;
        z-index: 111;
        overflow: hidden;
    }

    .vector-61 {
        position: relative;
        width: 12.5px;
        height: 6px;
        margin: 4.801px 0 0 1.6px;
        background: url("<?= Yii::getAlias('@web/thememain/images/e1f2b9d5-fc9c-4110-a70d-24f5e099fb29.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 112;
    }

    .font-size {
        position: absolute;
        width: 16px;
        height: 16px;
        top: 9.5px;
        left: 167.5px;
        background: url("<?= Yii::getAlias('@web/thememain/images/47f478e2-a977-47b2-b708-7e9789ba9543.png') ?>") no-repeat center;
        background-size: cover;
        z-index: 114;
        overflow: hidden;
    }

    .list-number-solid {
        position: absolute;
        width: 16px;
        height: 16px;
        top: 9.5px;
        left: 251.5px;
        z-index: 117;
        overflow: hidden;
    }

    .vector-62 {
        position: relative;
        width: 12.667px;
        height: 11.001px;
        margin: 2.5px 0 0 1.834px;
        background: url("<?= Yii::getAlias('@web/thememain/images/6e4cdec9-1788-4e90-b408-fd8ac774858b.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 118;
    }

    .font-family {
        position: absolute;
        width: 16px;
        height: 16px;
        top: 9.5px;
        right: 62.5px;
        z-index: 124;
        overflow: hidden;
    }

    .vector-63 {
        position: relative;
        width: 12.462px;
        height: 10.667px;
        margin: 2.668px 0 0 2.205px;
        background: url("<?= Yii::getAlias('@web/thememain/images/7250005c-376a-4782-a9c3-f13641eadd57.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 125;
    }

    .menu-kebab {
        position: absolute;
        width: 16px;
        height: 16px;
        top: 9.5px;
        left: 360.5px;
        z-index: 129;
        overflow: hidden;
    }

    .group-64 {
        position: relative;
        width: 1.5px;
        height: 12.5px;
        margin: 1.75px 0 0 7.25px;
        z-index: 130;
        overflow: visible auto;
    }

    .vector-65 {
        position: relative;
        width: 3px;
        height: 3px;
        margin: -0.75px 0 0 -0.75px;
        background: url("<?= Yii::getAlias('@web/thememain/images/3f81ff79-08ae-4017-a119-2506b5a4379a.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 131;
    }

    .vector-66 {
        position: relative;
        width: 3px;
        height: 3px;
        margin: 2.5px 0 0 -0.75px;
        background: url("<?= Yii::getAlias('@web/thememain/images/5a192c3d-3abf-4a33-89d6-76f243ee4dc4.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 132;
    }

    .vector-67 {
        position: relative;
        width: 3px;
        height: 3px;
        margin: 2.5px 0 0 -0.75px;
        background: url("<?= Yii::getAlias('@web/thememain/images/003d2ff5-5566-49c7-a377-1d36d6839630.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 133;
    }

    .vector-68 {
        position: absolute;
        width: 3.08%;
        height: 36.77%;
        top: 33.82%;
        left: 54.63%;
        background: url("<?= Yii::getAlias('@web/thememain/images/4ee7fed9-2152-4870-a45c-32535ace813b.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 120;
    }

    .arrow-down {
        position: absolute;
        width: 12px;
        height: 12px;
        top: 11.5px;
        left: 187.5px;
        z-index: 115;
        overflow: hidden;
    }

    .vector-69 {
        position: relative;
        width: 8.705px;
        height: 4.585px;
        margin: 3.891px 0 0 1.648px;
        background: url("<?= Yii::getAlias('@web/thememain/images/99209117-1a43-433f-9937-e4dc5a3a836b.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 116;
    }

    .arrow-down-6a {
        position: absolute;
        width: 12px;
        height: 12px;
        top: 11.5px;
        left: 226.5px;
        z-index: 121;
        overflow: hidden;
    }

    .vector-6b {
        position: relative;
        width: 8.705px;
        height: 4.585px;
        margin: 3.891px 0 0 1.648px;
        background: url("<?= Yii::getAlias('@web/thememain/images/2221e282-a921-4768-a40e-9a320ecdfd0d.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 122;
    }

    .arrow-down-6c {
        position: absolute;
        width: 12px;
        height: 12px;
        top: 11.5px;
        left: 327.5px;
        z-index: 126;
        overflow: hidden;
    }

    .vector-6d {
        position: relative;
        width: 8.705px;
        height: 4.585px;
        margin: 3.891px 0 0 1.648px;
        background: url("<?= Yii::getAlias('@web/thememain/images/f90dc646-5d8c-4a77-a351-6ed8706c9313.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 127;
    }

    .rectangle-6e {
        position: relative;
        width: 389px;
        height: 87px;
        margin: 67px 0 0 14px;
        z-index: 103;
        overflow: visible auto;
    }

    .flex-row-fe {
        position: relative;
        width: 377px;
        height: 20px;
        margin: 8px 0 0 0;
        z-index: 137;
    }

    .add-notes-outline {
        position: absolute;
        width: 20px;
        height: 20px;
        top: 0;
        left: 0;
        background: #f9b092;
        z-index: 135;
        border-radius: 3px;
    }

    .vector-6f {
        position: relative;
        width: 16.667px;
        height: 16.667px;
        margin: 2.5px 0 0 2.5px;
        background: url("<?= Yii::getAlias('@web/thememain/images/9961e1e8-8dcb-4eed-a856-fa82c81f0e1d.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 136;
    }

    .lead-status-change {
        position: absolute;
        width: 276px;
        height: 18px;
        top: 2px;
        right: 77px;
        font-family: Poppins, var(--default-font-family);
        font-size: 12px;
        font-weight: 400;
        line-height: 17.988px;
        text-align: left;
        text-overflow: initial;
        white-space: nowrap;
        letter-spacing: 0.36px;
        z-index: 104;
    }

    .l {
        position: relative;
        color: #535353;
        font-family: Poppins, var(--default-font-family);
        font-size: 12px;
        font-weight: 500;
        line-height: 17.988px;
        text-align: left;
        text-transform: capitalize;
        letter-spacing: 0.36px;
    }

    .lead-status-change-70 {
        position: relative;
        color: #535353;
        font-family: Poppins, var(--default-font-family);
        font-size: 12px;
        font-weight: 500;
        line-height: 17.988px;
        text-align: left;
        text-transform: lowercase;
        letter-spacing: 0.36px;
    }

    .menu-kebab-71 {
        position: absolute;
        width: 16px;
        height: 16px;
        top: 4px;
        left: 361px;
        z-index: 137;
        overflow: hidden;
    }

    .group-72 {
        position: relative;
        width: 1.5px;
        height: 12.5px;
        margin: 1.75px 0 0 7.25px;
        z-index: 138;
        overflow: visible auto;
    }

    .vector-73 {
        position: relative;
        width: 3px;
        height: 3px;
        margin: -0.75px 0 0 -0.75px;
        background: url("<?= Yii::getAlias('@web/thememain/images/bbf7bdb8-6085-477d-a467-1ef740a70a40.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 139;
    }

    .vector-74 {
        position: relative;
        width: 3px;
        height: 3px;
        margin: 2.5px 0 0 -0.75px;
        background: url("<?= Yii::getAlias('@web/thememain/images/61def7f4-1683-4315-8d68-32821b3bba8d.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 140;
    }

    .vector-75 {
        position: relative;
        width: 3px;
        height: 3px;
        margin: 2.5px 0 0 -0.75px;
        background: url("<?= Yii::getAlias('@web/thememain/images/67f66285-c6c2-48c2-94fb-64f341efd16c.png') ?>") no-repeat center;
        background-size: 100% 100%;
        z-index: 141;
    }

    .flex-row-cf {
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        width: 384px;
        height: 15px;
        margin: 38px 0 0 0;
        z-index: 106;
    }

    .amit-dingra {
        flex-shrink: 0;
        position: relative;
        height: 15px;
        color: #414141;
        font-family: Poppins, var(--default-font-family);
        font-size: 10px;
        font-weight: 500;
        line-height: 14.99px;
        text-align: left;
        text-transform: capitalize;
        white-space: nowrap;
        letter-spacing: 0.3px;
        z-index: 105;
    }

    .date-time {
        flex-shrink: 0;
        position: relative;
        width: 199px;
        height: 15px;
        font-family: Poppins, var(--default-font-family);
        font-size: 10px;
        font-weight: 400;
        line-height: 14.99px;
        text-align: left;
        text-overflow: initial;
        white-space: nowrap;
        letter-spacing: 0.3px;
        z-index: 106;
    }

    .date {
        position: relative;
        color: #414141;
        font-family: Poppins, var(--default-font-family);
        font-size: 10px;
        font-weight: 400;
        line-height: 14.99px;
        text-align: left;
        text-transform: capitalize;
        letter-spacing: 0.3px;
    }

    .time {
        position: relative;
        color: #414141;
        font-family: Poppins, var(--default-font-family);
        font-size: 10px;
        font-weight: 400;
        line-height: 14.99px;
        text-align: left;
        text-transform: lowercase;
        letter-spacing: 0.3px;
    }

    .date-76 {
        position: relative;
        color: #414141;
        font-family: Poppins, var(--default-font-family);
        font-size: 10px;
        font-weight: 400;
        line-height: 14.99px;
        text-align: left;
        text-transform: capitalize;
        letter-spacing: 0.3px;
    }

    .time-77 {
        position: relative;
        color: #414141;
        font-family: Poppins, var(--default-font-family);
        font-size: 10px;
        font-weight: 400;
        line-height: 14.99px;
        text-align: left;
        text-transform: lowercase;
        letter-spacing: 0.3px;
    }

    .frame-78 {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: nowrap;
        gap: 5px;
        position: absolute;
        width: 67px;
        height: 26px;
        top: 289px;
        left: 332px;
        padding: 4px 20px 4px 20px;
        cursor: pointer;
        background: var(--color-primary) !important;
        border: none;
        z-index: 101;
        border-radius: 5px;
    }

    .post {
        flex-shrink: 0;
        flex-basis: auto;
        position: relative;
        height: 18px;
        color: #ffffff;
        font-family: Poppins, var(--default-font-family);
        font-size: 12px;
        font-weight: 500;
        line-height: 18px;
        text-align: left;
        text-transform: capitalize;
        white-space: nowrap;
        letter-spacing: 0.36px;
        z-index: 102;
    }

    .attach-document {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        position: absolute;
        height: 18px;
        top: 293px;
        left: 14px;
        color: var(--color-primary) !important;
        font-family: Poppins, var(--default-font-family);
        font-size: 12px;
        font-weight: 500;
        line-height: 17.988px;
        text-align: left;
        text-decoration: underline;
        text-transform: capitalize;
        white-space: nowrap;
        letter-spacing: 0.36px;
        z-index: 134;
    }


    /* Lead pipeline Status */

  /*  .pipeline-container {
        position: absolute;
        width: 90%;
        margin: 20px auto;
        background-color: #ffffff;
        padding: 15px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        top: 147px;
        left: 95px;
    }
*/
    .pipeline-title {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 15px;
        text-align: center;
    }

    /* Pipeline Stages */
    .pipeline-stages {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        /* justify-content: center; */
        margin-bottom: 15px;
    }

    .stage {
        flex: 0 1 calc(14% - 10px);
        text-align: center;
        padding: 10px;
        border-radius: 5px;
        cursor: pointer;
        background-color: gray;
        color: white;
        font-size: 14px;
        transition: background-color 0.3s;
    }

    .stage.active {
        background-color: green;
    }

    .stage.marked {
        background-color: green;
        /* Marked stages turn green */
    }

   .stage:hover {
    background-color: var(--color-primary) !important;
}

    /* Stage Details */
    .stage-details,
    .stage-info {
        display: flex;
        /* justify-content: space-between; */
        /* margin-top: 10px; */
        /* font-size: 16px; */
        padding: 0px 10px;
        gap: 84px;
    }

    .detail-title {
        font-weight: bold;
    }

    .detail-value {
        font-size: 14px;
    }

    /* Mark as Current Button */
    .mark-as-current {
        display: block;
        margin: 20px auto;
        padding: 10px 20px;
        color: white;
        background-color: #007bff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }

    .mark-as-current:hover {
        background-color: #0056b3;
    }

    .mark-btn {
        /*position: absolute;*/
        top: 87px;
        left: 1153px;

    }

    .lead-other-btn {
        display: block;
       
        padding: 10px 20px;
        color: white;
        background-color: var(--color-primary) !important;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }

  .custom-modal {
    /*max-width: 300px;  
    margin: auto;*/
  }


    /* End Lead pipeline Status */
</style>


<div class="page-content">
         <div class="records table-responsive">
            <div class="record-header">
               <div class="add">
                  <img src="<?= $baseUrl;?>/thememain/img/lead icon.svg">
                  <select name="" id="">
                     <option value=""><?= $fullname; ?></option>
                  </select>
               </div>
               <div class="">
                  
                <?php
                if($Record['vertical_manager'] == Yii::$app->user->id)
                {?>
                    <div class="div-regroup">
                        <button class="approve">
                            <span class="">Approve</span></button>
                            <button class="delegate" id="delegate">
                            <span class="">Delegate</span>
                            </button>
                            <button class="modify" id="modify">
                            <span class="">Modify</span>
                            </button>
                            <button class="reject" id="reject">
                            <span class="">Reject</span>
                            </button>
                       
                 
                </div>
                <?php
                }
                else if($Record['ownerid'] == Yii::$app->user->id)
                {
                ?>
                <div class="div-regroup">
                        <button class="button-frame">
                            <?php
                            if($Record['leadstatus'] == 13)
                            {?>
                            <span class="span-convert">Convert</span></button><button class="button-frame-38" id="add-lead-btn">
                                <?php
                            }?>
                            <span class="span-edit" id="add-lead-btn">Edit</span>
                        </button>
                         <div class="div-frame">
                        <span class="span-more">More</span>
                        <div class="div-mdi-menu-down">
                            <div class="div-vector-39"></div>
                        </div>
                    </div>
                 
                </div>
                <?php
                }?>

               </div>
            </div>
         </div>
</div>
<div class="select-1">
    <div class="container-d">

        <div class="col-md-12">
            <div class="pipeline-container">
                <h2 class="pipeline-title">Lead Pipeline Status</h2>
                <div class="pipeline-stages">
                    <div class="stage stage-new <?php if($Record['leadstatus'] == 1) echo "active";?>" data-stage="new">New</div>
                    <div class="stage stage-not-contacted <?php if($Record['leadstatus'] == 2) echo "active";?>" data-stage="not-contacted">Not Contacted</div>
                    <div class="stage stage-changes-required <?php if($Record['leadstatus'] == 3) echo "active";?>" data-stage="changes-required">Changes Required</div>
                    <div class="stage stage-approval-pending <?php if($Record['leadstatus'] == 4) echo "active";?>" data-stage="approval-pending">Approval Pending</div>
                    <div class="stage stage-qualified <?php if($Record['leadstatus'] == 13) echo "active";?>" data-stage="qualified">Qualified</div>
                    <div class="stage stage-converted <?php if($Record['leadstatus'] == 6) echo "active";?>" data-stage="converted">Converted</div>

                    <button class="lead-other-btn">Other Lead</button>
                </div>


                <div class="stage-details">
                    <div class="detail-title">Stage Name</div>
                    <div class="detail-title">Entered At</div>
                    <div class="detail-title">Duration</div>
                    <div class="detail-title"></div>

                </div>
                <div class="stage-info">
                    <div class="detail-value stage-info-name">New</div>
                    <div class="detail-value stage-info-time">Oct 22, 2024 at 9:16 am</div>
                    <div class="detail-value stage-info-duration">21 Days | 8 Hours 20 Min</div>

                </div>

                <!-- <button class="mark-as-current mark-btn">Mark as Current</button> -->
            </div>
        </div>
        <div class="col-md-12">
            <div class="div-rectangle-3b">
                <span class="span-description">Description</span><span class="open-lead">When someone fills a form on your website, calls your company number,
                    initiates a chat on your website, or interacts with you on social
                    media and you have not got any specific information related to their
                    requirement/budget to qualify for further stages, it's called Open
                    Lead. Open Leads are those you have interacted or not interacted and
                    just received basic details, with no specific requirement from the
                    lead.</span>
            </div>
        </div>
        <div class="col-md-12" style="background: #f6f5f5;
    border-radius: 7px;
    margin-bottom: 20px;">
           
            <div class="row" style="padding: 20px">
                <div class="col-md-12">
                     <h2 style="color: #5c9cff">Lead Detail</h2>
                </div>
            <?php
            foreach ($ColumnList->blocks as $BlockKey => $Block) {
                foreach ($Block->fields as $field) {
                    ?>
                    <div class="col-md-6">
                        <label><b><?= $field["fieldlabel"]; ?></b>:</label>
                        <?php
                        if($field["uitype"] == 12)
                        {
                            $ref_hid_value = isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '';
                            $model1 = new Reference($TableName,$FieldId);
                            $relatedmodulename = $model1->getRelatedNoduleName($field["fieldid"]);
                            $getRelatedDisplayFieldName=$model1->getRelatedDisplayFieldName($field["fieldid"]);
                                if(isset($Record->{$field["columnname"]}) && $Record->{$field["columnname"]} !='')
             $Record->{$field["columnname"]}=$model1->getRefEntityValue($field["fieldid"],$ref_hid_value);
        else  $Record->{$field["columnname"]}='';
                        }
                        else if($field["uitype"] == 8)
                            {
                                $modellist = new Listhire;
                                if(isset($Record->{$field["columnname"]}))
                                $Record->{$field["columnname"]} = $modellist->getPickListDetailvalue($field["fieldid"],$Record->{$field["columnname"]});
                            else $Record->{$field["columnname"]} ;


                            }
                             else if($field["uitype"] == 53)
                            {
                                $modellist = new Listhire;
                                if(isset($Record->{$field["columnname"]}))
                                $Record->{$field["columnname"]} = $modellist->getuser($field["fieldid"],$Record->{$field["columnname"]});
                            else $Record->{$field["columnname"]} ;


                            }?>
                        <span><?= isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : ""?></span>
                    </div>
            <?php
                }
            }?>
            
            </div>
        </div>
        <!-- <div class="col-md-12">
            <div class="flex-row-b">
            <div class="rectangle-3c">
                <div class="frame-3d">
                    <div class="summery-dropdown">
                        <div class="group-3e">
                            <div class="rectangle-3f"></div>
                            <span class="lead-information">Lead Information</span>
                            <div class="ep-arrow-down-40">
                                <div class="vector-40"></div>
                            </div>
                        </div>
                        <div class="group-41">
                            <div class="rectangle-42"></div>
                            <span class="get-in-touch">Get in touch</span>
                            <div class="ep-arrow-down-43">
                                <div class="vector-44"></div>
                            </div>
                        </div>
                        <div class="group-45">
                            <div class="rectangle-46"></div>
                            <div class="ep-arrow-down-47">
                                <div class="vector-48"></div>
                            </div>
                            <span class="segment">Segment</span>
                        </div>
                        <div class="group-49">
                            <div class="rectangle-4a"></div>
                            <span class="system-generated">System Generated</span>
                            <div class="ep-arrow-down-4b">
                                <div class="vector-4c"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="frame-4d">
                <div class="rectangle-4e"><span class="activity">Activity</span></div>
                <div class="flex-row-f">
                    <button class="frame-4f">
                        <div class="ion-call">
                            <div class="vector-50"></div>
                        </div>
                        <span class="add-calls">Add Calls</span>
                    </button><button class="frame-51">
                        <div class="simple-icons-gotomeeting">
                            <div class="vector-52"></div>
                        </div>
                        <span class="add-meeting">Add Meeting</span>
                    </button><button class="frame-53">
                        <div class="pajamas-task-done">
                            <div class="vector-54"></div>
                        </div>
                        <span class="add-task">Add Task</span>
                    </button>
                </div>
                <div class="history-dropdown">
                    <div class="rectangle-55">
                        <span class="upcoming-overdue">Upcoming & overdue</span>
                        <div class="ep-arrow-down-56">
                            <div class="vector-57"></div>
                        </div>
                    </div>
                    <div class="rectangle-58">
                        <div class="ep-arrow-down-59">
                            <div class="vector-5a"></div>
                        </div>
                        <span class="october">october 2024</span>
                        <div class="ellipse-5b"></div>
                    </div>
                </div>
            </div>
            <div class="rectangle-5c">
                <div class="rectangle-5d"><span class="notes">Notes</span></div>
                <div class="rectangle-5e">
                    <div class="rectangle-5f">
                        <div class="vector-60"></div>
                        <span class="paragraph">Paragraph</span>
                        <div class="ph-list-bullets-bold">
                            <div class="vector-61"></div>
                        </div>
                        <div class="font-size"></div>
                        <div class="list-number-solid">
                            <div class="vector-62"></div>
                        </div>
                        <div class="font-family">
                            <div class="vector-63"></div>
                        </div>
                        <div class="menu-kebab">
                            <div class="group-64">
                                <div class="vector-65"></div>
                                <div class="vector-66"></div>
                                <div class="vector-67"></div>
                            </div>
                        </div>
                        <div class="vector-68"></div>
                        <div class="arrow-down">
                            <div class="vector-69"></div>
                        </div>
                        <div class="arrow-down-6a">
                            <div class="vector-6b"></div>
                        </div>
                        <div class="arrow-down-6c">
                            <div class="vector-6d"></div>
                        </div>
                    </div>
                </div>
                <div class="rectangle-6e">
                    <div class="flex-row-fe">
                        <div class="add-notes-outline">
                            <div class="vector-6f"></div>
                        </div>
                        <div class="lead-status-change">
                            <span class="l">L</span><span class="lead-status-change-70">ead Status required to be change form.......</span>
                        </div>
                        <div class="menu-kebab-71">
                            <div class="group-72">
                                <div class="vector-73"></div>
                                <div class="vector-74"></div>
                                <div class="vector-75"></div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-row-cf">
                        <span class="amit-dingra">Amit Dingra </span>
                        <div class="date-time">
                            <span class="date">Oct 22,2024 </span><span class="time">at 9.16 am </span><span class="date-76">I </span><span class="time-77"> 8 hours ago </span>
                        </div>
                    </div>
                </div>
                <button class="frame-78"><span class="post">Post</span></button><span class="attach-document">Attach Document</span>
            </div>
        </div> -->
        </div>
    </div>
</div>






<!-- Add Modal Structure -->

<div class="modal fade" id="add-lead-modal" tabindex="-1" role="dialog" aria-labelledby="addLeadModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
     

     
    </div>
  </div>
</div>

<!-- end add model -->
<div class="modal fade " id="approve-modal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="approveModalLabel">Approve Comment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="approve-form">
          <input type="hidden" id="Recordid" value="<?= $_REQUEST['Record'] ?>">
          <input type="hidden" name="csrfToken" id="csrfToken" value="<?= $csrfToken; ?>">
          <input type="hidden" name="csrfTokenName" id="csrfTokenName" value="<?= $csrfTokenName; ?>">
          <input type="hidden" name="leadstatus_v" id="leadstatus_v" value="13">

          <div class="mb-3">
            <label for="approve_comment" class="form-label">Comment</label>
            <textarea id="approve_comment" class="form-control" rows="4" placeholder="Add your comment here..."></textarea>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" id="approvesubmit" class="btn btn-primary">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
  #approve_comment {
    resize: none;
  }
</style>

<div class="modal fade" id="delegate-modal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="approveModalLabel">Delegate Comment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="approve-form">
          <input type="hidden" id="Recordid" value="<?= $_REQUEST['Record'] ?>">
          <input type="hidden" name="csrfToken" id="csrfToken" value="<?= $csrfToken; ?>">
          <input type="hidden" name="csrfTokenName" id="csrfTokenName" value="<?= $csrfTokenName; ?>">
          <input type="hidden" name="leadstatus_v" id="leadstatus_d" value="3">

          <div class="mb-3">
            <label for="delegate_comment" class="form-label">Comment</label>
            <textarea id="delegate_comment" class="form-control" rows="4" placeholder="Add your comment here..."></textarea>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" id="delegatesubmit" class="btn btn-primary">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="modify-modal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modifyModalLabel">Modify Comment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="approve-form">
          <input type="hidden" id="Recordid" value="<?= $_REQUEST['Record'] ?>">
          <input type="hidden" name="csrfToken" id="csrfToken" value="<?= $csrfToken; ?>">
          <input type="hidden" name="csrfTokenName" id="csrfTokenName" value="<?= $csrfTokenName; ?>">
          <input type="hidden" name="leadstatus_v" id="leadstatus_d" value="3">
           <div class="mb-3" style="width: 50%">
            <label for="modify_comment" class="form-label">Delegate User</label>
            
            <select class="form-control">
                <option>-select-</option>

                <?php
                foreach ($userlist as $key => $value) {
                     # code...
                 
                ?>
                <option value="<?= $value['id'];?>"><?= $value['showfield'];?> (<?= $value['email'];?>)</option>
                <?php
                }
                ?>
            </select>
          </div>

          <div class="mb-3">
            <label for="modify_comment" class="form-label">Comment</label>
            <textarea id="modify_comment" class="form-control" rows="4" placeholder="Add your comment here..."></textarea>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" id="modifysubmit" class="btn btn-primary">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="reject-modal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="approveModalLabel">Reject Comment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="approve-form">
          <input type="hidden" id="Recordid" value="<?= $_REQUEST['Record'] ?>">
          <input type="hidden" name="csrfToken" id="csrfToken" value="<?= $csrfToken; ?>">
          <input type="hidden" name="csrfTokenName" id="csrfTokenName" value="<?= $csrfTokenName; ?>">
          <input type="hidden" name="leadstatus_r" id="leadstatus_r" value="5">

          <div class="mb-3">
            <label for="reject_comment" class="form-label">Comment</label>
            <textarea id="reject_comment" class="form-control" rows="5" placeholder="Add your comment here..."></textarea>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" id="rejectsubmit" class="btn btn-primary">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- <div class="modal fade" id="delegate-modal" tabindex="-1" role="dialog" aria-labelledby="addLeadModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
       
            <input type="hidden" id="Recordid" value="">
            <input type="hidden" name="csrfToken" id="csrfToken" value="<?= $csrfToken;?>">
            <input type="hidden" name="csrfTokenName" id="csrfTokenName" value="<?= $csrfTokenName;?>">
            <input type="hidden" name="leadstatus_v" id="leadstatus_d" value="3">
        <textarea id="delegate_comment"></textarea>
        <button type="button" id="delegatesubmit">Submit</button>
       
    </div>
  </div>
</div> -->
<!-- <div class="modal fade" id="reject-modal" tabindex="-1" role="dialog" aria-labelledby="addLeadModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
       
            <input type="hidden" id="Recordid" value="<?php //$_REQUEST['Record'] ?>">
            <input type="hidden" name="csrfToken" id="csrfToken" value="<?= $csrfToken;?>">
            <input type="hidden" name="csrfTokenName" id="csrfTokenName" value="<?= $csrfTokenName;?>">
            <input type="hidden" name="leadstatus_v" id="leadstatus_r" value="5">
        <textarea id="reject_comment"></textarea>
        <button type="button" id="rejectsubmit">Submit</button>
       
    </div>
  </div>
</div> -->

<?php
$this->registerJs("
   
    $('.btn-close, .btn-secondary').click(function() {
       $('#add-lead-modal').modal('hide');
    });
     

    //modal create
    
    $('#add-lead-btn').on('click', function () {
         $.get('edit?Record={$_REQUEST['Record']}', function(data) {

       
            $('#add-lead-modal').modal('show')
                .find('.modal-content')
                .html(data);
        });
    });

        $('.approve').on('click', function () {
        

       
            $('#approve-modal').modal('show')
                .find('.modal-content')
                .html();

    });
       $('.reject').on('click', function () {
        

       
            $('#reject-modal').modal('show')
                .find('.modal-content')
                .html();

    });
       $('.delegate').on('click', function () {
        
// alert('cxbx');
       
            $('#delegate-modal').modal('show')
                .find('.modal-content')
                .html();

    });

      $('.modify').on('click', function () {
        
// alert('cxbx');
       
            $('#modify-modal').modal('show')
                .find('.modal-content')
                .html();

    });



 
");

$this->registerJsFile('@web/thememain/js/lead-details.js', ['depends' => [AdminAsset::class]]);

?>
<script type="text/javascript">

</script>