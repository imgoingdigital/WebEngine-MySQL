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

function templateBuildNavbar() {
	$cfg = loadConfig('navbar');
	if(!is_array($cfg)) return;
	
	echo '<ul>';
	foreach($cfg as $element) {
		if(!is_array($element)) continue;
		
		# active
		if(!$element['active']) continue;
		
		# type
		$link = ($element['type'] == 'internal' ? __BASE_URL__ . $element['link'] : $element['link']);
		
		# title
		$title = (check_value(lang($element['phrase'], true)) ? lang($element['phrase'], true) : 'Unk_phrase');
		
		# visibility
		if($element['visibility'] == 'guest') if(isLoggedIn()) continue;
		if($element['visibility'] == 'user') if(!isLoggedIn()) continue;
		
		# print
		if($element['newtab']) {
			echo '<li><a href="'.$link.'" target="_blank">'.$title.'</a></li>';
		} else {
			echo '<li><a href="'.$link.'">'.$title.'</a></li>';
		}
	}
	echo '</ul>';
}

function templateBuildUsercp() {
	$cfg = loadConfig('usercp');
	if(!is_array($cfg)) return;
	
	echo '<ul>';
	foreach($cfg as $element) {
		if(!is_array($element)) continue;
		
		# active
		if(!$element['active']) continue;
		
		# type
		$link = ($element['type'] == 'internal' ? __BASE_URL__ . $element['link'] : $element['link']);
		
		# title
		$title = (check_value(lang($element['phrase'], true)) ? lang($element['phrase'], true) : 'Unk_phrase');
		
		# icon
		$icon = (check_value($element['icon']) ? __PATH_TEMPLATE_IMG__ . 'icons/' . $element['icon'] : __PATH_TEMPLATE_IMG__ . 'icons/usercp_default.png');
		
		# visibility
		if($element['visibility'] == 'guest') if(isLoggedIn()) continue;
		if($element['visibility'] == 'user') if(!isLoggedIn()) continue;
		
		# print
		if($element['newtab']) {
			echo '<li><img src="'.$icon.'"><a href="'.$link.'" target="_blank">'.$title.'</a></li>';
		} else {
			echo '<li><img src="'.$icon.'"><a href="'.$link.'">'.$title.'</a></li>';
		}
	}
	echo '</ul>';
}

function templateLanguageSelector() {
	$langList = array(
		'en' => array('English', 'US'),
		'es' => array('Español', 'ES'),
		'ph' => array('Filipino', 'PH'),
		'br' => array('Português', 'BR'),
		'ro' => array('Romanian', 'RO'),
		'cn' => array('Simplified Chinese', 'CN'),
		'ru' => array('Russian', 'RU'),
		'lt' => array('Lithuanian', 'LT'),
	);
	
	if(isset($_SESSION['language_display'])) {
		$lang = $_SESSION['language_display'];
	} else {
		$lang = config('language_default', true);
	}
	
	echo '<ul class="webengine-language-switcher">';
		echo '<li><a href="'.__BASE_URL__.'language/switch/to/'.strtolower($lang).'" title="'.$langList[$lang][0].'"><img src="'.getCountryFlag($langList[$lang][1]).'" /> '.strtoupper($lang).'</a></li> ';
		foreach($langList as $language => $languageInfo) {
			if($language == $lang) continue;
			echo '<li><a href="'.__BASE_URL__.'language/switch/to/'.strtolower($language).'" title="'.$languageInfo[0].'"><img src="'.getCountryFlag($languageInfo[1]).'" /> '.strtoupper($language).'</a></li> ';
		}
	echo '</ul>';
}