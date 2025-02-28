<?php
/**
 * WebEngine CMS
 * https://webenginecms.org/
 * 
 * @version 1.2.6-dvteam
 * @author Lautaro Angelico <http://lautaroangelico.com/>
 * @copyright (c) 2013-2025 Lautaro Angelico, All Rights Reserved
 * 
 * Licensed under the MIT license
 * http://opensource.org/licenses/MIT
 */

class weProfiles {
	
	private $_request;
	private $_type;
	
	private $_reqMaxLen;
	private $_guildsCachePath;
	private $_playersCachePath;
	private $_cacheUpdateTime;
	
	private $_fileData;
	
	protected $common;
	protected $dB;
	protected $cfg;
	
	function __construct() {
		
		# database
		$this->common = new common();
		$this->dB = Connection::Database('MuOnline');
		
		# settings
		$this->_guildsCachePath = __PATH_CACHE__ . 'profiles/guilds/';
		$this->_playersCachePath = __PATH_CACHE__ . 'profiles/players/';
		$this->_cacheUpdateTime = 300;
		
		# check cache directories
		$this->checkCacheDir($this->_guildsCachePath);
		$this->checkCacheDir($this->_playersCachePath);
		
		# configs
		$profileConfig = loadConfigurations('profiles');
		if(!is_array($profileConfig)) throw new Exception(lang('error_25',true));
		$this->cfg = $profileConfig;
		
	}
	
	public function setType($input) {
		switch($input) {
			case "guild":
				$this->_type = "guild";
				$this->_reqMaxLen = 8;
				break;
			default:
				$this->_type = "player";
				$this->_reqMaxLen = 10;
		}
	}
	
	public function setRequest($input) {
		if(array_key_exists('encode', $this->cfg) && $this->cfg['encode'] == 1) {
			if(!Validator::Chars($input, array('a-z', 'A-Z', '0-9', '_', '-'))) throw new Exception(lang('error_25',true));
			$decodedReq = base64url_decode($input);
			if($decodedReq == false) throw new Exception(lang('error_25',true));
			$this->_request = $decodedReq;
			return true;
		}
		
		if(!Validator::AlphaNumeric($input)) throw new Exception(lang('error_25',true));
		if(strlen($input) > $this->_reqMaxLen) throw new Exception(lang('error_25',true));
		if(strlen($input) < 4) throw new Exception(lang('error_25',true));
		
		$this->_request = $input;
	}
	
	private function checkCacheDir($path) {
		if(check_value($path)) {
			if(!file_exists($path) || !is_dir($path)) {
				if(config('error_reporting',true)) {
					throw new Exception("Invalid cache directory ($path)");
				} else {
					throw new Exception(lang('error_21',true));
				}
			} else {
				if(!is_writable($path)) {
					if(config('error_reporting',true)) {
						throw new Exception("The cache directory is not writable ($path)");
					} else {
						throw new Exception(lang('error_21',true));
					}
				}
			}
		}
	}
	
	private function checkCache() {
		switch($this->_type) {
			case "guild":
				$reqFile = $this->_guildsCachePath . trim(strtolower($this->_request)) . '.cache';
				if(!file_exists($reqFile)) {
					$this->cacheGuildData();
				}
				$fileData = file_get_contents($reqFile);
				$fileData = explode("|", $fileData);
				if(is_array($fileData)) {
					if(time() > ($fileData[0]+$this->_cacheUpdateTime)) {
						$this->cacheGuildData();
					}
				} else {
					throw new Exception(lang('error_21',true));
				}
				$this->_fileData = file_get_contents($reqFile);
				break;
			default:
				$reqFile = $this->_playersCachePath . trim(strtolower($this->_request)) . '.cache';
				if(!file_exists($reqFile)) {
					$this->cachePlayerData();
				}
				$fileData = file_get_contents($reqFile);
				$fileData = explode("|", $fileData);
				if(is_array($fileData)) {
					if(time() > ($fileData[0]+$this->_cacheUpdateTime)) {
						$this->cachePlayerData();
					}
				} else {
					throw new Exception(lang('error_21',true));
				}
				$this->_fileData = file_get_contents($reqFile);
		}
	}
	
	private function cacheGuildData() {
		// General Data
		$guildData = DVT::getGuildInfoFromName($this->_request);
		if(!$guildData) throw new Exception(lang('error_25',true));
		
		// Members
		$guildMembers = DVT::getGuildMembersFromGuildId($guildData['guid']);
		if(!$guildMembers) throw new Exception(lang('error_25',true));
		$members = array();
		foreach($guildMembers as $gmember) {
			$members[] = $gmember[_CLMN_CHR_NAME_];
		}
		$gmembers_str = implode(",", $members);
		
		$guildMaster = DVT::getGuildMasterFromGuildId($guildData['guid']);
		
		// Cache
		$data = array(
			time(),
			$guildData[_CLMN_GUILD_NAME_],
			$guildData[_CLMN_GUILD_LOGO_],
			$guildData[_CLMN_GUILD_SCORE_],
			$guildMaster,
			$gmembers_str
		);
		
		// Cache Ready Data
		$cacheData = implode("|", $data);
		
		// Update Cache File
		$reqFile = $this->_guildsCachePath . trim(strtolower($this->_request)) . '.cache';
		$fp = fopen($reqFile, 'w+');
		fwrite($fp, $cacheData);
		fclose($fp);
	}
	
	private function cachePlayerData() {
		$Character = new Character();
		
		// general player data
		$playerData = $Character->CharacterData($this->_request);
		if(!$playerData) throw new Exception(lang('error_25',true));
		
		// master level data
		if(_TBL_MASTERLVL_ == _TBL_CHR_) {
			$playerMasterLevel = $playerData[_CLMN_ML_LVL_];
		} else {
			$masterLevelInfo = $Character->getMasterLevelInfo($this->_request);
			if(is_array($masterLevelInfo)) {
				$playerMasterLevel = $masterLevelInfo[_CLMN_ML_LVL_];
			}
		}
		
		// guild data
		$guild = "";
		$guildName = DVT::getGuildNameFromCharacterId($playerData['guid']);
		if($guildName != false) {
			$guild = $guildName;
		}
		
		// online status
		$status = 0;
		if($this->common->accountOnline($playerData[_CLMN_CHR_ACCID_])) {
			$status = 1;
		}
		
		// Cache
		$data = array(
			time(),
			$playerData[_CLMN_CHR_NAME_],
			$playerData[_CLMN_CHR_CLASS_],
			$playerData[_CLMN_CHR_LVL_],
			$playerData[_CLMN_CHR_RSTS_],
			$playerData[_CLMN_CHR_STAT_STR_],
			$playerData[_CLMN_CHR_STAT_AGI_],
			$playerData[_CLMN_CHR_STAT_VIT_],
			$playerData[_CLMN_CHR_STAT_ENE_],
			$playerData[_CLMN_CHR_STAT_CMD_],
			$playerData[_CLMN_CHR_PK_KILLS_],
			(check_value($playerData[_CLMN_CHR_GRSTS_]) ? $playerData[_CLMN_CHR_GRSTS_] : 0),
			$guild,
			$status,
			check_value($playerMasterLevel) ? $playerMasterLevel : 0,
		);
		
		// Cache Ready Data
		$cacheData = implode("|", $data);
		
		// Update Cache File
		$reqFile = $this->_playersCachePath . trim(strtolower($this->_request)) . '.cache';
		$fp = fopen($reqFile, 'w+');
		fwrite($fp, $cacheData);
		fclose($fp);
	}
	
	public function data() {
		if(!check_value($this->_type)) throw new Exception(lang('error_21',true));
		if(!check_value($this->_request)) throw new Exception(lang('error_21',true));
		$this->checkCache();
		return(explode("|", $this->_fileData));
	}
	
}