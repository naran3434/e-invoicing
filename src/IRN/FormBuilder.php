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
    public function setTransaction(string $scheme = 'GST', string $type = 'B2B', string $reverse = 'N', $gstin = null, string $igst = 'N'){
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
    public function setDocument($invoiceNo, string $type = 'INV', string $date = null){
        $this->documentDetails = [
            'Typ'       => $type,
            'No'        => $invoiceNo,
            'Dt'        => $date ?? $date('d/m/Y')
        ];
    }

    public function setBuyer(
        $gstin, $legalName, $address, $location, $state, $tradeName = null, $pinCode = null, $phone = null, $email = null
    ){
        $this->buyerDetails = [
            'Gstin'             => $gstin,
            'LglNm'             => $legalName,
            'Pos'               => substr($gstin, 0, 2),
            'Addr1'             => $address,
            'Loc'               => $location,
            'Stcd'              => $state
        ];

        if($tradeName) { $this->buyerDetails['TrdNm'] = $tradeName; }
        if($pinCode) { $this->buyerDetails['Pin'] = $pinCode; }
        if($phone) { $this->buyerDetails['Ph'] = $phone; }
        if($email) { $this->buyerDetails['Em'] = $pinCode; }
    }


    /**
     * @param $gstin
     * @param $legalName
     * @param $address
     * @param $location
     * @param $state
     * @param $tradeName
     * @param $pinCode
     * @param $phone
     * @param $email
     * @return void
     */
    public function setSeller(
        $gstin, $legalName, $address, $location, $state, $tradeName = null, $pinCode = null, $phone = null, $email = null
    ){
        $this->sellerDetails = [
            'Gstin'             => $gstin,
            'LglNm'             => $legalName,
            'Pos'               => substr($gstin, 0, 2),
            'Addr1'             => $address,
            'Loc'               => $location,
            'Stcd'              => $state
        ];

        if($tradeName) { $this->sellerDetails['TrdNm'] = $tradeName; }
        if($pinCode) { $this->sellerDetails['Pin'] = $pinCode; }
        if($phone) { $this->sellerDetails['Ph'] = $phone; }
        if($email) { $this->sellerDetails['Em'] = $pinCode; }
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
        $productNo, $description, $quantity, $price, $hsn, int $gstRate = 18, string $isService = 'N', string $unit = 'Nos', int $freeQuantity = 0, int $discount = 0
    ){
        $amount = ($quantity * $price) - $discount;
        $igst = 0;
        $sgst = 0;
        $cgst = 0;

        if($this->transactionDetails['IgstOnIntra'] === 'Y'){
            $igst = ($amount * $gstRate)/100;
        } else {
            $sgst = ($amount * ($gstRate/2))/100;
            $cgst = ($amount * ($gstRate/2))/100;
        }

        $item = [
            'PrdSlNo'           => $productNo,
            'TotItemVal'        => $amount + $igst + $cgst + $sgst,
            'SgstAmt'           => $sgst,
            'CgstAmt'           => $cgst,
            'IgstAmt'           => $igst,
            'Qty'               => $quantity,
            'AssAmt'            => $amount,
            'TotAmt'            => ($price * $quantity),
            'UnitPrice'         => $price,
            'Discount'          => $discount,
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
}