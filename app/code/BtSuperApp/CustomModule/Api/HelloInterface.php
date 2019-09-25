<?php
namespace BtSuperApp\CustomModule\Api;

interface HelloInterface
{
    /**
     * Returns greeting message to user
     *
     * @api
     * @param int customer id
     * @return mixed 
     */
    public function displayWishlist($customer_id);
   
   /**
     * Returns greeting message to user
     *
     * @api
     * @param int customer id
     * @return mixed 
     */
    public function getUserSettings($customer_id);
    
   /**
     * Returns greeting message to user
     *
     * @api
     * @param int customer id
     * @param int proximity_notification
     * @param int push_notification
     * @param int geo_location
     * @param string phone_number
     * @return mixed 
     */
    public function saveUserSetting($customer_id,$proximity_notification,$push_notification,$geo_location,$phone_number);
	
	/**
     * Returns greeting message to user
     *
     * @api
     * @param int customer id
     * @param int websiteId
     * @param int store
     * @param int nonce_from_client
     * @param double amount
     * @param int save_my_card
     * @param int newsletter
     * @return mixed 
     */
	public function getConfirmPurchase($customerId,$websiteId,$store,$nonce_from_client,$amount,$save_my_card,$newsletter);
	
	/**
     * Returns greeting message to user
     *
     * @api
     * @param int customer id
     * @return mixed 
     */
	public function getGenerateToken($customerId);
	
	/**
     * Returns logo image from theme header
     *
     * @return mixed 
     */
	public function getLogoUrl();
	
	/**
     * Returns Order status change
     *
     * @api
     * @param int order id
     * @return mixed 
     */
	public function changeOrderStatusToComplete($orderId);
	
	/**
     * Returns Order pdf 
     *
     * @api
     * @param int order id
     * @return mixed 
     */
	public function getCustomPdfUrl($orderId);
}
