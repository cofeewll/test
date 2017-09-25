<?php
use Think\Log;
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidBroadcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidUnicast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidListcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidFilecast.php');

require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSBroadcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSUnicast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSListcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSFilecast.php');

class Umeng {
	protected $and_appkey           = '59154f81b27b0a6a9500006e'; 
	protected $and_appMasterSecret     = '87mvqup1ur9ifbp2v8id8u7v2bmthhgr';
	protected $ios_appkey           = ''; 
	protected $ios_appMasterSecret     = '';
	protected $ios_mode = "false";
	protected $timestamp        = NULL;
	protected $validation_token = NULL;
	protected $file = NULL;

	protected $device_tokens = NULL;
	protected $msg = NULL;
	protected $extra = NULL;

	function __construct($device_tokens,$msg, $extra = '') {
		$this->device_tokens = $device_tokens;
		$this->msg = $msg;
		$this->extra = $extra;

		$this->timestamp = strval(time());
		$this->file = './ApiLogs/Umeng/'.date('Y-m-d').'log';
	}

	function sendAndroidBroadcast() {
		try {
			$brocast = new AndroidBroadcast();
			$brocast->setAppMasterSecret($this->and_appMasterSecret);
			$brocast->setPredefinedKeyValue("appkey",           $this->and_appkey);
			$brocast->setPredefinedKeyValue("timestamp",        $this->timestamp);
			$brocast->setPredefinedKeyValue("ticker",           $this->msg['ticker']);
			$brocast->setPredefinedKeyValue("title",            $this->msg['title']);
			$brocast->setPredefinedKeyValue("text",             $this->msg['text']);
			$brocast->setPredefinedKeyValue("after_open",       "go_app");
			// Set 'production_mode' to 'false' if it's a test device. 
			// For how to register a test device, please see the developer doc.
			$brocast->setPredefinedKeyValue("production_mode", "true");
			// [optional]Set extra fields
			if($this->extra && is_array($this->extra)){
	            foreach ($this->extra as $key => $value) {
	               $brocast->setExtraField($key, $value);
	            }
	        }
			// $brocast->setExtraField("test", "helloworld");
			// print("Sending broadcast notification, please wait...\r\n");
			$res = $brocast->send();
			// Log::record ( '[AndBroadcast推送成功] res data:' .json_encode($res) , Log::INFO );
			log_result($this->file,'[AndBroadcast推送成功] res data:' .json_encode($res));
			return $res;
			// print("Sent SUCCESS\r\n");
		} catch (Exception $e) {
			// Log::record ( '[AndBroadcast推送失败] exception:' . $e->getMessage(), Log::WARN );
			log_result($this->file,'[AndBroadcast推送失败] exception:' . $e->getMessage());
			return false;
			// print("Caught exception: " . $e->getMessage());
		}
	}

	function sendAndroidUnicast() {
		try {
			$unicast = new AndroidUnicast();
			$unicast->setAppMasterSecret($this->and_appMasterSecret);
			$unicast->setPredefinedKeyValue("appkey",           $this->and_appkey);
			$unicast->setPredefinedKeyValue("timestamp",        $this->timestamp);
			// Set your device tokens here
			$unicast->setPredefinedKeyValue("device_tokens",    $this->device_tokens); 
			$unicast->setPredefinedKeyValue("ticker",           $this->msg['ticker']);
			$unicast->setPredefinedKeyValue("title",            $this->msg['title']);
			$unicast->setPredefinedKeyValue("text",             $this->msg['text']);
			$unicast->setPredefinedKeyValue("after_open",       "go_app");
			// Set 'production_mode' to 'false' if it's a test device. 
			// For how to register a test device, please see the developer doc.
			$unicast->setPredefinedKeyValue("production_mode", "true");
			// Set extra fields
			if($this->extra && is_array($this->extra)){
	            foreach ($this->extra as $key => $value) {
	               $unicast->setExtraField($key, $value);
	            }
	        }
			$unicast->setExtraField("test", "helloworld");
			// print("Sending unicast notification, please wait...\r\n");
			$res = $unicast->send();
			// Log::record ( '[AndUnicast推送成功] res data:' .json_encode($res) , Log::INFO );
			log_result($this->file,'[AndUnicast推送成功] res data:' .json_encode($res));
			return $res;
			// print("Sent SUCCESS\r\n");
		} catch (Exception $e) {
			// Log::record ( '[AndUnicast推送失败] exception:' . $e->getMessage(), Log::WARN );
			log_result($this->file,'[AndUnicast推送失败] exception:' . $e->getMessage());
			// print("Caught exception: " . $e->getMessage());
			return false;
		}
	}

	function sendAndroidListcast() {
		try {
			$listcast = new AndroidListcast();
			$listcast->setAppMasterSecret($this->and_appMasterSecret);
			$listcast->setPredefinedKeyValue("appkey",           $this->and_appkey);
			$listcast->setPredefinedKeyValue("timestamp",        $this->timestamp);
			// Set your device tokens here
			$listcast->setPredefinedKeyValue("device_tokens",    $this->device_tokens); 
			$listcast->setPredefinedKeyValue("ticker",           $this->msg['ticker']);
			$listcast->setPredefinedKeyValue("title",            $this->msg['title']);
			$listcast->setPredefinedKeyValue("text",             $this->msg['text']);
			$listcast->setPredefinedKeyValue("after_open",       "go_app");
			// Set 'production_mode' to 'false' if it's a test device. 
			// For how to register a test device, please see the developer doc.
			$listcast->setPredefinedKeyValue("production_mode", "true");
			// Set extra fields
			if($this->extra && is_array($this->extra)){
	            foreach ($this->extra as $key => $value) {
	               $listcast->setExtraField($key, $value);
	            }
	        }
			$listcast->setExtraField("test", "helloworld");
			// print("Sending listcast notification, please wait...\r\n");
			$res = $listcast->send();
			// Log::record ( '[AndListcast推送成功] res data:' .json_encode($res) , Log::INFO );
			log_result($this->file,'[AndListcast推送成功] res data:' .json_encode($res));
			return $res;
			// print("Sent SUCCESS\r\n");
		} catch (Exception $e) {
			// Log::record ( '[AndListcast推送失败] exception:' . $e->getMessage(), Log::WARN );
			log_result($this->file,'[AndListcast推送失败] exception:' . $e->getMessage());
			// print("Caught exception: " . $e->getMessage());
			return false;
		}
	}

	function sendAndroidFilecast() {
		try {
			$filecast = new AndroidFilecast();
			$filecast->setAppMasterSecret($this->and_appMasterSecret);
			$filecast->setPredefinedKeyValue("appkey",           $this->and_appkey);
			$filecast->setPredefinedKeyValue("timestamp",        $this->timestamp);
			$filecast->setPredefinedKeyValue("ticker",           $this->msg['ticker']);
			$filecast->setPredefinedKeyValue("title",            $this->msg['title']);
			$filecast->setPredefinedKeyValue("text",             $this->msg['text']);
			$filecast->setPredefinedKeyValue("after_open",       "go_app");  //go to app
			// print("Uploading file contents, please wait...\r\n");
			// Upload your device tokens, and use '\n' to split them if there are multiple tokens
			$device_tokens = str_replace(',', "\n", $this->device_tokens);
			$filecast->uploadContents($device_tokens);
			// print("Sending filecast notification, please wait...\r\n");
			$res = $filecast->send();
			// Log::record ( '[AndroidFilecast推送成功] res data:' .json_encode($res) , Log::INFO );
			log_result($this->file,'[AndroidFilecast推送成功] res data:' .json_encode($res));
			return $res;
			// print("Sent SUCCESS\r\n");
		} catch (Exception $e) {
			// Log::record ( '[AndroidFilecast推送失败] exception:' . $e->getMessage(), Log::WARN );
			log_result($this->file,'[AndroidFilecast推送失败] exception:' . $e->getMessage());
			// print("Caught exception: " . $e->getMessage());
			return false;
		}
	}


	function sendIOSBroadcast() {
		try {
			$brocast = new IOSBroadcast();
			$brocast->setAppMasterSecret($this->ios_appMasterSecret);
			$brocast->setPredefinedKeyValue("appkey",           $this->ios_appkey);
			$brocast->setPredefinedKeyValue("timestamp",        $this->timestamp);

			$brocast->setPredefinedKeyValue("alert", $this->msg['title']);
			$brocast->setPredefinedKeyValue("badge", 0);
			$brocast->setPredefinedKeyValue("sound", "chime");
			// Set 'production_mode' to 'true' if your app is under production mode
			$brocast->setPredefinedKeyValue("production_mode", $this->ios_mode);
			// Set customized fields
			if($this->extra && is_array($this->extra)){
	            foreach ($this->extra as $key => $value) {
	               $brocast->setCustomizedField($key, $value);
	            }
	        }
			// $brocast->setCustomizedField("test", "helloworld");
			// print("Sending broadcast notification, please wait...\r\n");
			$res = $brocast->send();
			// Log::record ( '[IOSBroadcast推送成功] res data:' .json_encode($res) , Log::INFO );
			log_result($this->file,'[IOSBroadcast推送成功] res data:' .json_encode($res));
			return $res;
			// print("Sent SUCCESS\r\n");
		} catch (Exception $e) {
			// Log::record ( '[IOSBroadcast推送失败] exception:' . $e->getMessage(), Log::WARN );
			log_result($this->file,'[IOSBroadcast推送失败] exception:' . $e->getMessage());
			return false;
			// print("Caught exception: " . $e->getMessage());
		}
	}

	function sendIOSUnicast() {
		try {
			$unicast = new IOSUnicast();
			$unicast->setAppMasterSecret($this->ios_appMasterSecret);
			$unicast->setPredefinedKeyValue("appkey",           $this->ios_appkey);
			$unicast->setPredefinedKeyValue("timestamp",        $this->timestamp);
			// Set your device tokens here
			$unicast->setPredefinedKeyValue("device_tokens",    $this->device_tokens); 
			$unicast->setPredefinedKeyValue("alert", $this->msg['title']);
			$unicast->setPredefinedKeyValue("badge", 0);
			$unicast->setPredefinedKeyValue("sound", "chime");
			// Set 'production_mode' to 'true' if your app is under production mode
			$unicast->setPredefinedKeyValue("production_mode", $this->ios_mode);
			// Set customized fields
			if($this->extra && is_array($this->extra)){
	            foreach ($this->extra as $key => $value) {
	               $unicast->setCustomizedField($key, $value);
	            }
	        }
			// $unicast->setCustomizedField("test", "helloworld");
			// print("Sending unicast notification, please wait...\r\n");
			$res = $unicast->send();
			// Log::record ( '[IOSUnicast推送成功] res data:' .json_encode($res) , Log::INFO );
			log_result($this->file,'[IOSUnicast推送成功] res data:' .json_encode($res));
			return $res;
			// print("Sent SUCCESS\r\n");
		} catch (Exception $e) {
			// Log::record ( '[IOSUnicast推送失败] exception:' . $e->getMessage(), Log::WARN );
			log_result($this->file,'[IOSUnicast推送失败] exception:' . $e->getMessage());
			return false;
			// print("Caught exception: " . $e->getMessage());
		}
	}

	function sendIOSListcast() {
		try {
			$listcast = new IOSListcast();
			$listcast->setAppMasterSecret($this->ios_appMasterSecret);
			$listcast->setPredefinedKeyValue("appkey",           $this->ios_appkey);
			$listcast->setPredefinedKeyValue("timestamp",        $this->timestamp);
			// Set your device tokens here
			$listcast->setPredefinedKeyValue("device_tokens",    $this->device_tokens); 
			$listcast->setPredefinedKeyValue("alert", $this->msg['title']);
			$listcast->setPredefinedKeyValue("badge", 0);
			$listcast->setPredefinedKeyValue("sound", "chime");
			// Set 'production_mode' to 'true' if your app is under production mode
			$listcast->setPredefinedKeyValue("production_mode", $this->ios_mode);
			// Set customized fields
			if($this->extra && is_array($this->extra)){
	            foreach ($this->extra as $key => $value) {
	               $listcast->setCustomizedField($key, $value);
	            }
	        }
			// $listcast->setCustomizedField("test", "helloworld");
			// print("Sending listcast notification, please wait...\r\n");
			$res = $listcast->send();
			// Log::record ( '[IOSListcast推送成功] res data:' .json_encode($res) , Log::INFO );
			log_result($this->file,'[IOSListcast推送成功] res data:' .json_encode($res));
			return $res;
			// print("Sent SUCCESS\r\n");
		} catch (Exception $e) {
			// Log::record ( '[IOSListcast推送失败] exception:' . $e->getMessage(), Log::WARN );
			log_result($this->file,'[IOSListcast推送失败] exception:' . $e->getMessage());
			return false;
			// print("Caught exception: " . $e->getMessage());
		}
	}

	function sendIOSFilecast() {
		try {
			$filecast = new IOSFilecast();
			$filecast->setAppMasterSecret($this->ios_appMasterSecret);
			$filecast->setPredefinedKeyValue("appkey",           $this->ios_appkey);
			$filecast->setPredefinedKeyValue("timestamp",        $this->timestamp);

			$filecast->setPredefinedKeyValue("alert", $this->msg['title']);
			$filecast->setPredefinedKeyValue("badge", 0);
			$filecast->setPredefinedKeyValue("sound", "chime");
			// Set 'production_mode' to 'true' if your app is under production mode
			$filecast->setPredefinedKeyValue("production_mode", $this->ios_mode);
			// print("Uploading file contents, please wait...\r\n");
			// Upload your device tokens, and use '\n' to split them if there are multiple tokens
			$device_tokens = str_replace(',', "\n", $this->device_tokens);
			$filecast->uploadContents($device_tokens);
			// print("Sending filecast notification, please wait...\r\n");
			$res = $filecast->send();
			// Log::record ( '[IOSFilecast推送成功] res data:' .json_encode($res) , Log::INFO );
			log_result($this->file,'[IOSFilecast推送成功] res data:' .json_encode($res));
			return $res;
			// print("Sent SUCCESS\r\n");
		} catch (Exception $e) {
			// Log::record ( '[IOSFilecast推送失败] exception:' . $e->getMessage(), Log::WARN );
			log_result($this->file,'[IOSFilecast推送失败] exception:' . $e->getMessage());
			return false;
			// print("Caught exception: " . $e->getMessage());
		}
	}
	
}
