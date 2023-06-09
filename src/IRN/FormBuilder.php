<?php

namespace Naran3434\EInvoicing\IRN;

class FormBuilder
{

    /**
     * @var string
     */
    protected $version = '1.1';

    /**
     * @var
     */
    protected $transactionDetails;

    /**
     * @var
     */
    protected $documentDetails;

    /**
     * @var
     */
    protected $buyerDetails;

    /**
     * @var
     */
    protected $sellerDetails;

    /**
     * @var array
     */
    protected $itemList = [];

    /**
     * @var array
     */
    protected $itemTotal;

    /**
     * @var
     */
    protected $referenceDetails;

    /**
     * @param string $scheme
     * @param string $type
     * @param string $reverse
     * @param $gstin
     * @param string $igst
     * @return void
     */
    public function setTransaction(string $igst = 'N', string $scheme = 'GST', string $type = 'B2B', string $reverse = 'N', $gstin = null){
        $this->transactionDetails = [
            'TaxSch'        => $scheme,
            'SupTyp'        => $type,
            'RegRev'        => $reverse,
            'EcmGstin'      => $gstin,
            'IgstOnIntra'   => $igst
        ];
    }

    /**
     * @param $invoiceNo
     * @param string $type
     * @param string|null $date
     * @return void
     */
    public function setDocument($invoiceNo, string $date = null, string $type = 'INV'){
        $this->documentDetails = [
            'Typ'       => $type,
            'No'        => $invoiceNo,
            'Dt'        => $date ?? $date('d/m/Y')
        ];
    }

    /**
     * @param $gstin
     * @param $legalName
     * @param $address
     * @param $location
     * @param $tradeName
     * @param $pinCode
     * @param $phone
     * @param $email
     * @return void
     */
    public function setBuyer(
        $gstin, $legalName, $address, $location, $tradeName = null, $pinCode = null, $phone = null, $email = null
    ){
        $this->buyerDetails = [
            'Gstin'             => $gstin,
            'LglNm'             => $legalName,
            'Pos'               => substr($gstin, 0, 2),
            'Addr1'             => $address,
            'Loc'               => $location,
            'Stcd'              => substr($gstin, 0, 2)
        ];

        if($tradeName) { $this->buyerDetails['TrdNm'] = $tradeName; }
        if($pinCode) { $this->buyerDetails['Pin'] = $pinCode; }
        if($phone) { $this->buyerDetails['Ph'] = $phone; }
        if($email) { $this->buyerDetails['Em'] = $email; }
    }


    /**
     * @param $gstin
     * @param $legalName
     * @param $address
     * @param $location
     * @param $tradeName
     * @param $pinCode
     * @param $phone
     * @param $email
     * @return void
     */
    public function setSeller(
        $gstin, $legalName, $address, $location, $tradeName = null, $pinCode = null, $phone = null, $email = null
    ){
        $this->sellerDetails = [
            'Gstin'             => $gstin,
            'LglNm'             => $legalName,
            'Pos'               => substr($gstin, 0, 2),
            'Addr1'             => $address,
            'Loc'               => $location,
            'Stcd'              => substr($gstin, 0, 2)
        ];

        if($tradeName) { $this->sellerDetails['TrdNm'] = $tradeName; }
        if($pinCode) { $this->sellerDetails['Pin'] = $pinCode; }
        if($phone) { $this->sellerDetails['Ph'] = $phone; }
        if($email) { $this->sellerDetails['Em'] = $email; }
    }


    /**
     * @param $productNo
     * @param $description
     * @param $quantity
     * @param $price
     * @param $hsn
     * @param int $gstRate
     * @param string $isService
     * @param string $unit
     * @param int $freeQuantity
     * @param int $discount
     * @return void
     */
    public function addItems(
        $productNo, $description, $quantity, $price, $hsn, string $isService = 'N',  int $discount = 0, int $gstRate = 18, string $unit = 'Nos', int $freeQuantity = 0
    ){
        $amount = ($quantity * $price) - $discount;
        $igst = 0;
        $sgst = 0;
        $cgst = 0;

        if(substr($this->sellerDetails['Gstin'], 0, 2) != substr($this->buyerDetails['Gstin'], 0, 2)){
            $igst = ($amount * $gstRate)/100;
        } else {
            $sgst = ($amount * ($gstRate/2))/100;
            $cgst = ($amount * ($gstRate/2))/100;
        }

        $item = [
            'PrdSlNo'           => $productNo,
            'TotItemVal'        => round($amount + $igst + $cgst + $sgst, 2),
            'SgstAmt'           => round($sgst, 2),
            'CgstAmt'           => round($cgst, 2),
            'IgstAmt'           => round($igst, 2),
            'Qty'               => $quantity,
            'AssAmt'            => round($amount, 2),
            'TotAmt'            => round($price * $quantity, 2),
            'UnitPrice'         => $price,
            'Discount'          => round($discount, 2),
            'Unit'              => $unit,
            'FreeQty'           => $freeQuantity,
            'GstRt'             => $gstRate,
            'HsnCd'             => $hsn,
            'IsServc'           => $isService,
            'PrdDesc'           => $description,
            'SlNo'              => count($this->itemList) + 1,
        ];

        $this->itemList[] = $item;
    }


    /**
     * @return void
     */
    public function setItemTotal(){
        if(count($this->itemList) === 0){ throw new \LengthException('Empty items list'); }
        $assetValue = 0;
        $cgst = 0;
        $sgst = 0;
        $igst = 0;
        $total = 0;
        $discount = 0;

        foreach ($this->itemList as $row) {
            $assetValue += $row['AssAmt'];
            $cgst       += $row['CgstAmt'];
            $sgst       += $row['SgstAmt'];
            $igst       += $row['IgstAmt'];
            $total      += $row['TotItemVal'];
            $discount   += $row['Discount'];
        }

        $this->itemTotal = [
            'AssVal'    => $assetValue,
            'CgstVal'   => $cgst,
            'SgstVal'   => $sgst,
            'IgstVal'   => $igst,
            'TotInvVal' => $total,
            'Discount'  => $discount,
        ];
    }

    /**
     * @return false|string
     */
    public function toJson(){
        $data = [
            'Version'       => $this->version,
            'TranDtls'      => $this->transactionDetails,
            'DocDtls'       => $this->documentDetails,
            'BuyerDtls'     => $this->buyerDetails,
            'SellerDtls'    => $this->sellerDetails,
            'ItemList'      => $this->itemList,
            'ValDtls'       => $this->itemTotal
        ];
        return json_encode($data);
    }

    /**
     * @return array
     */
    public function toArray(): array {
        return [
            'Version'       => $this->version,
            'TranDtls'      => $this->transactionDetails,
            'DocDtls'       => $this->documentDetails,
            'BuyerDtls'     => $this->buyerDetails,
            'SellerDtls'    => $this->sellerDetails,
            'ItemList'      => $this->itemList,
            'ValDtls'       => $this->itemTotal
        ];
    }
}