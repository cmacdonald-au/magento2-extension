<?php

namespace Ess\M2ePro\Block\Adminhtml\Amazon\Template;

use Ess\M2ePro\Block\Adminhtml\Magento\Form\AbstractContainer;

abstract class Edit extends AbstractContainer
{
    protected function _beforeToHtml()
    {
        $this->jsTranslator->addTranslations([
            'Do not show any more' => $this->__('Do not show this message anymore'),
            'Save Policy' => $this->__('Save Policy')
        ]);

        return parent::_beforeToHtml();
    }

    protected function getSaveConfirmationText($id = null)
    {
        $saveConfirmation = '';
        $template = $this->getHelper('Data\GlobalData')->getValue('tmp_template');

        if (is_null($id) && !is_null($template)) {
            $id = $template->getId();
        }

        if ($id) {
            $saveConfirmation = $this->getHelper('Data')->escapeJs(
                $this->__('<br/>
<b>Note:</b> All changes you have made will be automatically applied to all M2E Pro Listings where this Policy is used.'
                )
            );
        }

        return $saveConfirmation;
    }
}