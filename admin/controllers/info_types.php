<?php
/*--------------------------------------------------------------------------------------------------------|  www.vdm.io  |------/
    __      __       _     _____                 _                                  _     __  __      _   _               _
    \ \    / /      | |   |  __ \               | |                                | |   |  \/  |    | | | |             | |
     \ \  / /_ _ ___| |_  | |  | | _____   _____| | ___  _ __  _ __ ___   ___ _ __ | |_  | \  / | ___| |_| |__   ___   __| |
      \ \/ / _` / __| __| | |  | |/ _ \ \ / / _ \ |/ _ \| '_ \| '_ ` _ \ / _ \ '_ \| __| | |\/| |/ _ \ __| '_ \ / _ \ / _` |
       \  / (_| \__ \ |_  | |__| |  __/\ V /  __/ | (_) | |_) | | | | | |  __/ | | | |_  | |  | |  __/ |_| | | | (_) | (_| |
        \/ \__,_|___/\__| |_____/ \___| \_/ \___|_|\___/| .__/|_| |_| |_|\___|_| |_|\__| |_|  |_|\___|\__|_| |_|\___/ \__,_|
                                                        | |                                                                 
                                                        |_| 				
/-------------------------------------------------------------------------------------------------------------------------------/

	@version		@update number 3 of this MVC
	@build			25th October, 2017
	@created		2nd April, 2016
	@package		Support Groups
	@subpackage		info_types.php
	@author			Llewellyn van der Merwe <http://www.vdm.io>	
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html 
	
	Support Groups 
                                                             
/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * Info_types Controller
 */
class SupportgroupsControllerInfo_types extends JControllerAdmin
{
	protected $text_prefix = 'COM_SUPPORTGROUPS_INFO_TYPES';
	/**
	 * Proxy for getModel.
	 * @since	2.5
	 */
	public function getModel($name = 'Info_type', $prefix = 'SupportgroupsModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		
		return $model;
	}

	public function exportData()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// check if export is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('info_type.export', 'com_supportgroups') && $user->authorise('core.export', 'com_supportgroups'))
		{
			// Get the input
			$input = JFactory::getApplication()->input;
			$pks = $input->post->get('cid', array(), 'array');
			// Sanitize the input
			JArrayHelper::toInteger($pks);
			// Get the model
			$model = $this->getModel('Info_types');
			// get the data to export
			$data = $model->getExportData($pks);
			if (SupportgroupsHelper::checkArray($data))
			{
				// now set the data to the spreadsheet
				$date = JFactory::getDate();
				SupportgroupsHelper::xls($data,'Info_types_'.$date->format('jS_F_Y'),'Info types exported ('.$date->format('jS F, Y').')','info types');
			}
		}
		// Redirect to the list screen with error.
		$message = JText::_('COM_SUPPORTGROUPS_EXPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_supportgroups&view=info_types', false), $message, 'error');
		return;
	}


	public function importData()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// check if import is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('info_type.import', 'com_supportgroups') && $user->authorise('core.import', 'com_supportgroups'))
		{
			// Get the import model
			$model = $this->getModel('Info_types');
			// get the headers to import
			$headers = $model->getExImPortHeaders();
			if (SupportgroupsHelper::checkObject($headers))
			{
				// Load headers to session.
				$session = JFactory::getSession();
				$headers = json_encode($headers);
				$session->set('info_type_VDM_IMPORTHEADERS', $headers);
				$session->set('backto_VDM_IMPORT', 'info_types');
				$session->set('dataType_VDM_IMPORTINTO', 'info_type');
				// Redirect to import view.
				$message = JText::_('COM_SUPPORTGROUPS_IMPORT_SELECT_FILE_FOR_INFO_TYPES');
				$this->setRedirect(JRoute::_('index.php?option=com_supportgroups&view=import', false), $message);
				return;
			}
		}
		// Redirect to the list screen with error.
		$message = JText::_('COM_SUPPORTGROUPS_IMPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_supportgroups&view=info_types', false), $message, 'error');
		return;
	}  
}
