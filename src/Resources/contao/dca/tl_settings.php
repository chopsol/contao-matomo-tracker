<?php

$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ";{legend_comatrack_global},comatrack_exclude_ip,comatrack_exclude_ua";

$GLOBALS['TL_DCA']['tl_settings']['fields']['comatrack_exclude_ip'] = array	(
			'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['comatrack_exclude_ip'],
			'default'                 => '',
			'inputType'               => 'textarea',
			'eval'                    => array('mandatory'=>false,'rows'=>3,'allowHtml'=>false),
		    'load_callback'           => array(array('tl_settings_comatrack','comatrackSettingsLoadCallback')),
		    'save_callback'           => array(array('tl_settings_comatrack','comatrackSettingsSaveCallback'))
		);

$GLOBALS['TL_DCA']['tl_settings']['fields']['comatrack_exclude_ua'] = array	(
			'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['comatrack_exclude_ua'],
			'default'                 => '',
			'inputType'               => 'textarea',
			'eval'                    => array('mandatory'=>false,'rows'=>3,'allowHtml'=>false),
		    'load_callback'           => array(array('tl_settings_comatrack','comatrackSettingsLoadCallback')),
		    'save_callback'           => array(array('tl_settings_comatrack','comatrackSettingsSaveCallback'))
		);

class tl_settings_comatrack extends Backend {

    public function comatrackSettingsSaveCallback($varValue, DataContainer $dc)
    {

    	$retValues = array();

    	$varValue = explode("\n",$varValue);
    	if (count($varValue)>0) {
    		foreach ($varValue as $val) {
    			if (trim($val)) {
    				$retValues[] = trim($val);
    			}
    		}
    	}
        return implode("~~~",$retValues);
    }
    public function comatrackSettingsLoadCallback($varValue, DataContainer $dc)
    {
		return str_replace("~~~","\n",$varValue);
    }
}
