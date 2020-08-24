<?php

namespace Kaliop\ImportDiscount\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\SalesRule\Api\Data\RuleInterface;
use Magento\SalesRule\Model\Coupon;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Magento\SalesRule\Api\CouponRepositoryInterface;
use Magento\SalesRule\Api\Data\RuleInterfaceFactory;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\SalesRule\Model\CouponFactory;
use Magento\Framework\App\Helper\Context;

class Import extends AbstractHelper
{
    protected $_dir;
    protected $_coupon;
    protected $couponRepository;
    protected $ruleRepository;
    protected $rule;
    protected $couponModel;

    public function __construct(Context $context, DirectoryList $dir,
                                CouponRepositoryInterface $couponRepository,
                                RuleRepositoryInterface $ruleRepository,
                                RuleInterfaceFactory $rule,
                                CouponFactory $coupon, Coupon $couponModel) {
        $this->_dir = $dir;
        $this->_coupon = $coupon;
        $this->couponRepository = $couponRepository;
        $this->ruleRepository = $ruleRepository;
        $this->rule = $rule;
        $this->couponModel = $couponModel;
        parent::__construct($context);
    }
    public function readCSV() {
        $rootMedia = $this->_dir->getPath('media');
        $contentFolder = $rootMedia."/importFileCSV/";
        $files = glob($contentFolder."*.csv");
        foreach( $files as $f) {
            $row=0;
            $file = fopen($f, "r");
            if($file) {
                while (($data = fgetcsv($file,1000,";")) !== FALSE) { // read row file csv
                    if ($row > 0) {
                        list($name, $discount, $code) = $data;
                         $coupon = $this->getDiscountCode($code);
                        if(!$coupon->getRuleId()) {
                        $newRule = $this->rule->create();
                        $newRule->setName($name)
                            ->setIsAdvanced(true)
                            ->setStopRulesProcessing(false)
                            ->setCustomerGroupIds([0, 1, 2]) // customer groups id to apply discount
                            ->setWebsiteIds([1]) //website id
                            ->setCouponType(RuleInterface::COUPON_TYPE_SPECIFIC_COUPON)
                            ->setSimpleAction(RuleInterface::DISCOUNT_ACTION_BY_PERCENT)
                            ->setDiscountAmount($discount)
                            ->setIsActive(true); // enable coupon
                            $ruleCreate = $this->ruleRepository->save($newRule);
                        if ($ruleCreate->getRuleId()) {
                            $this->createCoupon($ruleCreate->getRuleId(), $code);
                        }
                    }
                }
                    $row++;
                }
            }
            fclose($file);
            $this->moveImportedFile($rootMedia,$f); //move file after imported file
        }
    }

    public function createCoupon($ruleId, $code) {
        $coupon = $this->_coupon->create();
        $coupon->setCode($code)
            ->setRuleId($ruleId);
        $couponCode = $this->couponRepository->save($coupon);

        return $couponCode->getCouponId();
    }

    public function getDiscountCode($couponCode) {
        return $this->couponModel->loadByCode($couponCode);
    }

    public function moveImportedFile($rootDir, $file) {
        $target=$rootDir.'/importedFileCSV/';
        if(!is_dir($target)) {
            mkdir($target, 0777, true);
        }

        rename($file,$target.date("Y_m_d_H_i_s").basename($file));
    }
}