<?php
/**
 * Facebook SDK Module and Authentication.
 * 
 * @author gizmore
 * @since 4.0
 * @version 5.0
 * 
 * @see GWF_OAuthToken
 * @see GDO_FBAuthButton
 */
final class Module_Facebook extends GWF_Module
{
	public $module_priority = 45;
	
	public function getClasses() { return ['GWF_OAuthToken', 'GDO_FBAuthButton']; }
	public function onLoadLanguage() { $this->loadLanguage('lang/facebook'); }

	##############
	### Config ###
	##############
	public function getConfig()
	{
		return array(
			GDO_Checkbox::make('fb_auth')->initial('1'),
			GDO_String::make('fb_app_id')->ascii()->caseS()->max(32)->initial('224073134729877'),
			GDO_String::make('fb_secret')->ascii()->caseS()->max(64)->initial('f0e9ee41ea8d2dd2f9d5491dc81783e8'),
		);
	}
	public function cfgAuth() { return $this->getConfigValue('fb_auth'); }
	public function cfgAppID() { return $this->getConfigValue('fb_app_id'); }
	public function cfgSecret() { return $this->getConfigValue('fb_secret'); }
	
	############
	### Util ###
	############
	/**
	 * @return \Facebook\Facebook
	 */
	public function getFacebook()
	{
		if (!GWF5::instance()->isCLI())
		{
			# lib requires normal php sessions.
			if (!session_id()) { session_start(); }
		}
		require_once $this->filePath('php-graph-sdk/src/Facebook/autoload.php');
		return new Facebook\Facebook(array('app_id' => $this->cfgAppID(), 'app_secret' => $this->cfgSecret()));
	}
	
	#############
	### Hooks ###
	#############
	/**
	 * Hook into register form creation and add a link.
	 * @param GWF_Form $form
	 */
	public function hookRegisterForm(GWF_Form $form)
	{
		$form->addField(GDO_Link::make('link_fb_auth')->href(href('Facebook', 'Auth')));
	}
}
