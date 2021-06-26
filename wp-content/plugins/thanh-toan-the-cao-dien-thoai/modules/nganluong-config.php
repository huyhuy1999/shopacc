<?php
define('NGANLUONG_URL_CARD_POST', 'https://www.nganluong.vn/mobile_card.api.post.v2.php');
define('NGANLUONG_URL_CARD_SOAP', 'https://nganluong.vn/mobile_card_api.php?wsdl');
class NganLuongConfig {
	public static $_FUNCTION = "CardCharge";
	public static $_VERSION = "2.0";
	public static $_MERCHANT_ID ;
	public static $_MERCHANT_PASSWORD ;
	public static $_EMAIL_RECEIVE_MONEY;
}