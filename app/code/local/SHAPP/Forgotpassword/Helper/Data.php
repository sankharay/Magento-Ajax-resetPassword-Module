<?php
class SHAPP_Forgotpassword_Helper_Data extends Mage_Core_Helper_Abstract{

    /**
     * Retrieve customer login POST URL
     *
     * @return string
     */
    function checkCustomertokenValidation($CustomerId,$tokEn)
    {
        if($CustomerId != "" AND $tokEn !=""){
        $customer = Mage::getModel('customer/customer')->load($CustomerId);
        if(trim($tokEn) != trim($customer->getrp_token()))
        return "NOTMATCH";
        else{
        $now = new DateTime($customer->getrp_token_date());
        $ref = new DateTime(Mage::getModel('core/date')->date('Y-m-d H:i:s'));
        $diff = $now->diff($ref);
        if($diff->h > 3 OR $diff->d > 0)
        return "NOTMATCH";
        }
        }else{
            return $this->__('Invalid Access');
        }
    }

}

?>