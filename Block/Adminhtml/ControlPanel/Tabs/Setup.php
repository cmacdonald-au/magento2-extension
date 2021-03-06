<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Block\Adminhtml\ControlPanel\Tabs;

use Ess\M2ePro\Block\Adminhtml\Magento\Form\AbstractForm;
use Ess\M2ePro\Helper\Client;
use Ess\M2ePro\Helper\Magento;
use Ess\M2ePro\Helper\Module as ModuleHelper;

class Setup extends AbstractForm
{
    //########################################

    public function _construct()
    {
        parent::_construct();

        // Initialization block
        // ---------------------------------------
        $this->setId('controlPanelSetup');
        // ---------------------------------------

        $this->setTemplate('control_panel/tabs/setup.phtml');
    }

    //########################################

    protected function _beforeToHtml()
    {
        $this->setChild('inspection', $this->getLayout()->createBlock(
            '\Ess\M2ePro\Block\Adminhtml\ControlPanel\Inspection\Installation'
        ));

        $this->setChild('setup_info', $this->getLayout()->createBlock(
            '\Ess\M2ePro\Block\Adminhtml\ControlPanel\Info\Installation'
        ));

        return parent::_beforeToHtml();
    }

    //########################################
}