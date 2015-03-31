<?php
class SHAPP_Forgotpassword_resetpasswordController extends Mage_Core_Controller_Front_Action
{

   public function indexAction()
   {
      $this->loadLayout();
      $this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
      $this->getLayout()->getBlock('root')->addBodyClass('customer-account-forgotpassword');
      $this->getLayout()->getBlock('head')->setTitle($this->__('reset your password'));
      $block = $this->getLayout()->createBlock(
        'Mage_Core_Block_Template',
        'shapp_resetpasword_block',
      array('template' => 'shapp/forgetpassword.phtml')
    );
      $this->getLayout()->getBlock('content')->append($block);
      $this->renderLayout();
    }

    function changepasswordAction()
    {
      if($this->getRequest()->getParams('new_password')){
        $passWordData = $this->getRequest()->getParams('new_password');
        $new_password = trim($passWordData['new_password']);
        if($passWordData['new_password'] == $passWordData['confirm_password'])
          $validationData = TRUE;
        $data = Mage::helper('forgotpassword/data');
        $UrlValidation = $data->checkCustomertokenValidation($passWordData['customer'],$passWordData['token']);
        if($validationData == TRUE AND $UrlValidation != "NOTMATCH")
        {
            $customer = Mage::getModel('customer/customer')->load($passWordData['customer']);
            $customerEmail = $customer->getEmail();
            $customer->setWebsiteId(Mage::app()->getWebsite()->getId())
            ->setStore(Mage::app()->getStore())
            ->setPassword("$new_password");
            $customer->setrp_token("");
            try{
                $customer->save();
                $userSession = Mage::getSingleton('customer/session');
                $userSession->setCustomer($customer);
                Mage::dispatchEvent('customer_login', array('customer'=>$customer));
                echo $this->__('Password Changed');
            }
            catch (Exception $e) {
                Zend_Debug::dump($e->getMessage());
            }
        }else{
          echo $this->__('Invalid Access');
        }
      }
    }

}

?>