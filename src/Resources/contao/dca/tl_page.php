<?php

$GLOBALS['TL_DCA']['tl_page']['fields']['useComatrack'] = [
	'exclude'    => true,
	'inputType'  => 'checkbox',
	'eval'       => array('submitOnChange'=>true),
	'sql'        => "char(1) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_page']['fields']['comatrack_url'] = [
	'label'      => &$GLOBALS['TL_LANG']['tl_page']['comatrack_url'],
	'inputType'  => 'text',
	'eval'       => ['maxlength' => 255, 'mandatory' => true, 'rgxp' => 'url'],
	'sql'        => "varchar(255) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_page']['fields']['comatrack_id'] = [
	'label'      => &$GLOBALS['TL_LANG']['tl_page']['comatrack_id'],
	'inputType'  => 'text',
	'eval'       => ['tl_class'=>'w50', 'maxlength' => 5, 'mandatory' => true, 'rgxp' => 'natural'],
	'sql'        => "varchar(5) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_page']['fields']['comatrack_token'] = [
	'label'      => &$GLOBALS['TL_LANG']['tl_page']['comatrack_token'],
	'inputType'  => 'text',
	'eval'       => ['tl_class'=>'w50', 'maxlength' => 50],
	'sql'        => "varchar(50) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_page']['fields']['comatrack_debug'] = [
	'label'      => &$GLOBALS['TL_LANG']['tl_page']['comatrack_debug'],
	'default'    => '0',
	'inputType'  => 'checkbox',
	'eval'       => ['tl_class'=>'w50'],
	'sql'        => "char(1) NOT NULL default '0'"
];

$GLOBALS['TL_DCA']['tl_page']['fields']['comatrack_404'] = [
	'label'      => &$GLOBALS['TL_LANG']['tl_page']['comatrack_404'],
	'default'    => '0',
	'inputType'  => 'checkbox',
	'eval'       => ['tl_class'=>'w50'],
	'sql'        => "char(1) NOT NULL default '0'"
];

$GLOBALS['TL_DCA']['tl_page']['fields']['comatrack_dnt'] = [
	'label'      => &$GLOBALS['TL_LANG']['tl_page']['comatrack_dnt'],
	'default'    => '1',
	'inputType'  => 'checkbox',
	'eval'       => ['tl_class'=>'w50'],
	'sql'        => "char(1) NOT NULL default '1'"
];

$GLOBALS['TL_DCA']['tl_page']['fields']['comatrack_dim_dnt'] = [
	'label'      => &$GLOBALS['TL_LANG']['tl_page']['comatrack_dim_dnt'],
	'inputType'  => 'text',
	'eval'       => ['tl_class'=>'w50', 'maxlength' => 3, 'mandatory' => false, 'rgxp' => 'natural'],
	'sql'        => "varchar(3) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_page']['fields']['comatrack_ip'] = [
	'label'      => &$GLOBALS['TL_LANG']['tl_page']['comatrack_ip'],
	'default'    => '0',
	'inputType'  => 'checkbox',
	'eval'       => ['tl_class'=>'w50'],
	'sql'        => "char(1) NOT NULL default '0'"
];

$GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'useComatrack';
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['useComatrack'] = 'comatrack_url,comatrack_id,comatrack_token,comatrack_404,comatrack_ip,comatrack_dnt,comatrack_dim_dnt,comatrack_debug';
$GLOBALS['TL_DCA']['tl_page']['palettes']['root'] = str_replace(';{protected_legend',';{comatrack_legend},useComatrack;{protected_legend',$GLOBALS['TL_DCA']['tl_page']['palettes']['root']);
$GLOBALS['TL_DCA']['tl_page']['palettes']['rootfallback'] = str_replace(';{protected_legend',';{comatrack_legend},useComatrack;{protected_legend',$GLOBALS['TL_DCA']['tl_page']['palettes']['rootfallback']);
