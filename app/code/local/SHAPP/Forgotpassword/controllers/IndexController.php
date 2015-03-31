<?php
class SHAPP_Forgotpassword_IndexController extends Mage_Core_Controller_Front_Action
{

    const XML_PATH_FORGOT_EMAIL_TEMPLATE        = 'customer/password/forgot_email_template';
    const XML_PATH_FORGOT_EMAIL_IDENTITY        = 'customer/password/forgot_email_identity';

   public function indexAction()
   {
      if($this->getRequest()->getParams('emailAddress'))
      {
      	$emailAddress = $this->getRequest()->getParams('emailAddress');
      	$customer = Mage::getModel('customer/customer');
      	$customer->setWebsiteId(Mage::app()->getWebsite()->getId());
      	$customer->loadByEmail($emailAddress['emailaddress']);
      	if ($customer->getId()) {
      		$rp_token = $this->generaterp_token($customer->getfirstname(),$customer->getlastname(),$emailAddress['emailaddress']);
      		$customer->setrp_token($rp_token);
      		$customer->setrp_token_date(Mage::getModel('core/date')->date('Y-m-d H:i:s'));
      		$customer->save();
      		$sendEmail = $this->sendTransactionalEmail($customer);
          Mage::getSingleton('customer/session')->addSuccess('Email has been sent.');
          echo $this->__('reset password link send your email address');
      	}else
      	{
      		echo $this->__('customer not exist');
      	}
      }
   }

   public function sendTransactionalEmail($customer)
    {
    	$storeId = Mage::app()->getStore()->getId();
    	Mage::getModel('core/email_template')
            ->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
            ->sendTransactional(
                Mage::getStoreConfig(self::XML_PATH_FORGOT_EMAIL_TEMPLATE, $storeId),
                Mage::getStoreConfig(self::XML_PATH_FORGOT_EMAIL_IDENTITY, $storeId),
                $customer->getEmail(),
                $customer->getName(),
                array('customer' => $customer)
            );
    }

    function generaterp_token($firstname,$lastname,$email)
    {
    	return $newPasscode = MD5($firstname.$lastname.$email.Mage::getModel('core/date')->date('Y-m-d H:i:s'));
    }

}

?>