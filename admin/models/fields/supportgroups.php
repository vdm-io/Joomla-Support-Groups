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

	@version		1.0.8
	@build			5th May, 2018
	@created		24th February, 2016
	@package		Support Groups
	@subpackage		supportgroups.php
	@author			Llewellyn van der Merwe <http://www.vdm.io>	
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html 
	
	Support Groups 
                                                             
/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Supportgroups Form Field class for the Supportgroups component
 */
class JFormFieldSupportgroups extends JFormFieldList
{
	/**
	 * The supportgroups field type.
	 *
	 * @var		string
	 */
	public $type = 'supportgroups';

	/**
	 * Override to add new button
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   3.2
	 */
	protected function getInput()
	{
		// see if we should add buttons
		$setButton = $this->getAttribute('button');
		// get html
		$html = parent::getInput();
		// if true set button
		if ($setButton === 'true')
		{
			$button = array();
			$script = array();
			$buttonName = $this->getAttribute('name');
			// get the input from url
			$app = JFactory::getApplication();
			$jinput = $app->input;
			// get the view name & id
			$values = $jinput->getArray(array(
				'id' => 'int',
				'view' => 'word'
			));
			// check if new item
			$ref = '';
			$refJ = '';
			if (!is_null($values['id']) && strlen($values['view']))
			{
				// only load referal if not new item.
				$ref = '&amp;ref=' . $values['view'] . '&amp;refid=' . $values['id'];
				$refJ = '&ref=' . $values['view'] . '&refid=' . $values['id'];
			}
			$user = JFactory::getUser();
			// only add if user allowed to create support_group
			if ($user->authorise('support_group.create', 'com_supportgroups') && $app->isAdmin()) // TODO for now only in admin area.
			{
				// build Create button
				$buttonNamee = trim($buttonName);
				$buttonNamee = preg_replace('/_+/', ' ', $buttonNamee);
				$buttonNamee = preg_replace('/\s+/', ' ', $buttonNamee);
				$buttonNamee = preg_replace("/[^A-Za-z ]/", '', $buttonNamee);
				$buttonNamee = ucfirst(strtolower($buttonNamee));
				$button[] = '<a id="'.$buttonName.'Create" class="btn btn-small btn-success hasTooltip" title="'.JText::sprintf('COM_SUPPORTGROUPS_CREATE_NEW_S', $buttonNamee).'" style="border-radius: 0px 4px 4px 0px; padding: 4px 4px 4px 7px;"
					href="index.php?option=com_supportgroups&amp;view=support_group&amp;layout=edit'.$ref.'" >
					<span class="icon-new icon-white"></span></a>';
			}
			// only add if user allowed to edit support_group
			if (($buttonName === 'support_group' || $buttonName === 'support_groups') && $user->authorise('support_group.edit', 'com_supportgroups') && $app->isAdmin()) // TODO for now only in admin area.
			{
				// build edit button
				$buttonNamee = trim($buttonName);
				$buttonNamee = preg_replace('/_+/', ' ', $buttonNamee);
				$buttonNamee = preg_replace('/\s+/', ' ', $buttonNamee);
				$buttonNamee = preg_replace("/[^A-Za-z ]/", '', $buttonNamee);
				$buttonNamee = ucfirst(strtolower($buttonNamee));
				$button[] = '<a id="'.$buttonName.'Edit" class="btn btn-small hasTooltip" title="'.JText::sprintf('COM_SUPPORTGROUPS_EDIT_S', $buttonNamee).'" style="display: none; padding: 4px 4px 4px 7px;" href="#" >
					<span class="icon-edit"></span></a>';
				// build script
				$script[] = "
					jQuery(document).ready(function() {
						jQuery('#adminForm').on('change', '#jform_".$buttonName."',function (e) {
							e.preventDefault();
							var ".$buttonName."Value = jQuery('#jform_".$buttonName."').val();
							".$buttonName."Button(".$buttonName."Value);
						});
						var ".$buttonName."Value = jQuery('#jform_".$buttonName."').val();
						".$buttonName."Button(".$buttonName."Value);
					});
					function ".$buttonName."Button(value) {
						if (value > 0) {
							// hide the create button
							jQuery('#".$buttonName."Create').hide();
							// show edit button
							jQuery('#".$buttonName."Edit').show();
							var url = 'index.php?option=com_supportgroups&view=support_groups&task=support_group.edit&id='+value+'".$refJ."';
							jQuery('#".$buttonName."Edit').attr('href', url);
						} else {
							// show the create button
							jQuery('#".$buttonName."Create').show();
							// hide edit button
							jQuery('#".$buttonName."Edit').hide();
						}
					}";
			}
			// check if button was created for support_group field.
			if (is_array($button) && count($button) > 0)
			{
				// Load the needed script.
				$document = JFactory::getDocument();
				$document->addScriptDeclaration(implode(' ',$script));
				// return the button attached to input field.
				return '<div class="input-append">' .$html . implode('',$button).'</div>';
			}
		}
		return $html;
	}

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	public function getOptions()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('a.id','a.name'),array('id','support_group_name')));
		$query->from($db->quoteName('#__supportgroups_support_group', 'a'));
		$query->where($db->quoteName('a.published') . ' = 1');
		$query->order('a.name ASC');
		$db->setQuery((string)$query);
		$items = $db->loadObjectList();
		$options = array();
		if ($items)
		{
			$options[] = JHtml::_('select.option', '', 'Select a support group');
			foreach($items as $item)
			{
				$options[] = JHtml::_('select.option', $item->id, $item->support_group_name);
			}
		}
		return $options;
	}
}
