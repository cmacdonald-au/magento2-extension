<?php

namespace Ess\M2ePro\Controller\Adminhtml\Amazon\Template\Synchronization;

use Ess\M2ePro\Controller\Adminhtml\Amazon\Template;

class Save extends Template
{
    public function execute()
    {
        $post = $this->getRequest()->getPost();

        if (!$post->count()) {
            $this->_forward('index');
            return;
        }

        $id = $this->getRequest()->getParam('id');

        // Base prepare
        // ---------------------------------------
        $data = array();
        // ---------------------------------------

        // tab: list
        // ---------------------------------------
        $keys = array(
            'title',
            'list_mode',
            'list_status_enabled',
            'list_is_in_stock',
            'list_qty_magento',
            'list_qty_magento_value',
            'list_qty_magento_value_max',
            'list_qty_calculated',
            'list_qty_calculated_value',
            'list_qty_calculated_value_max',
        );
        foreach ($keys as $key) {
            if (isset($post[$key])) {
                $data[$key] = $post[$key];
            }
        }

        $data['title'] = strip_tags($data['title']);
        // ---------------------------------------

        // tab: revise
        // ---------------------------------------
        $keys = array(
            'revise_update_qty',
            'revise_update_qty_max_applied_value_mode',
            'revise_update_qty_max_applied_value',
            'revise_update_price',
            'revise_update_price_max_allowed_deviation_mode',
            'revise_update_price_max_allowed_deviation',
            'revise_update_details',
            'revise_update_images',
            'revise_change_selling_format_template',
            'revise_change_description_template',
            'revise_change_shipping_override_template',
            'revise_change_listing'
        );
        foreach ($keys as $key) {
            if (isset($post[$key])) {
                $data[$key] = $post[$key];
            }
        }
        // ---------------------------------------

        // tab: relist
        // ---------------------------------------
        $keys = array(
            'relist_mode',
            'relist_filter_user_lock',
            'relist_send_data',
            'relist_status_enabled',
            'relist_is_in_stock',
            'relist_qty_magento',
            'relist_qty_magento_value',
            'relist_qty_magento_value_max',
            'relist_qty_calculated',
            'relist_qty_calculated_value',
            'relist_qty_calculated_value_max'
        );
        foreach ($keys as $key) {
            if (isset($post[$key])) {
                $data[$key] = $post[$key];
            }
        }
        // ---------------------------------------

        // tab: stop
        // ---------------------------------------
        $keys = array(
            'stop_status_disabled',
            'stop_out_off_stock',
            'stop_qty_magento',
            'stop_qty_magento_value',
            'stop_qty_magento_value_max',
            'stop_qty_calculated',
            'stop_qty_calculated_value',
            'stop_qty_calculated_value_max'
        );
        foreach ($keys as $key) {
            if (isset($post[$key])) {
                $data[$key] = $post[$key];
            }
        }
        // ---------------------------------------

        // Add or update model
        // ---------------------------------------
        $model = $this->amazonFactory->getObject('Template\Synchronization');
        if ($id) {
            $model->load($id);
            $model->addData($data);
            $model->getChildObject()->addData($data);
        } else {
            $model->setData($data);
        }

        $oldData = $model->getDataSnapshot();
        if ($id) {
            $oldData = array_merge($oldData, $model->getChildObject()->getDataSnapshot());
        }
        $model->save();
        $newData = array_merge($model->getDataSnapshot(), $model->getChildObject()->getDataSnapshot());

        $model->getChildObject()->setSynchStatusNeed($newData,$oldData);

        if ($this->isAjax()) {
            $this->setJsonContent([
                'status' => true
            ]);
            return $this->getResult();
        }

        $id = $model->getId();
        // ---------------------------------------

        $this->messageManager->addSuccess($this->__('Policy was successfully saved'));
        return $this->_redirect($this->getHelper('Data')->getBackUrl('*/amazon_template/index', array(), array(
            'edit' => array(
                'id' => $id,
                'wizard' => $this->getRequest()->getParam('wizard'),
                'close_on_save' => $this->getRequest()->getParam('close_on_save')
            ),
        )));
    }
}