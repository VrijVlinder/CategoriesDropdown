<?php if (!defined('APPLICATION')) exit();

$PluginInfo['CategoriesDropdown'] = array(
	'Name'			=> 'CategoriesDropdown',
	'Description'	=> "Adds a Categories Link to the menu and Categories appear in a Sub-Menu .",
	'Version'		=> '1.2',
	'Author'		=> 'VrijVlinder',
	'AuthorEmail'	=> 'contact@vrijvlinder.com',
	'AuthorUrl'		=> 'http://vrijvlinder.com',
	'License'		=> 'Free',
	'RequiredPlugins' => FALSE,
	'HasLocale'		=> TRUE,
	'RegisterPermissions' => FALSE,
	'SettingsUrl'	=> FALSE,
	'SettingsPermission' => FALSE,
	'MobileFriendly' =>FALSE
	
);


class CategoriesDropdownPlugin extends Gdn_Plugin
{
	
	protected $_CategoryData;
		
	public function Base_Render_Before($Sender)
	{
		
		$Sender->AddCssFile($this->GetResource('catdrop.css', FALSE, FALSE));
		
		$CatDropJQuerySource =
'<script type="text/javascript">
var ddmenuitem = 0;
var menustyles = { "visibility":"visible", "display":"block", "z-index":"99999"}

function Menu_close()
{  if(ddmenuitem) { ddmenuitem.css("visibility", "hidden"); } }

function Menu_open()
{  Menu_close();
   ddmenuitem = $(this).find("ul").css(menustyles);
}

jQuery(document).ready(function()
{  $("ul#Menu > li").bind("mouseover", Menu_open);
   $("ul#Menu > li").bind("mouseout", Menu_close);
});

document.onclick = Menu_close;</script>
';
		
		$Sender->Head->AddString($CatDropJQuerySource);
		
		
		if ($Sender->Menu)
		{
			// Set this to FALSE|TRUE whether you want to display the Discussion-Counter next to each Category or not
			$DisplayCounter = TRUE;
			
			// Build the Categories Model & load Categories Data
			$CategoryModel = new CategoryModel();
			$_CategoryData = $CategoryModel->GetFull();
			
			// If there are any Categories...
			if ($_CategoryData != FALSE)
			{
				// Add a link to the Category overview as first menuitem
				$Sender->Menu->AddLink('Categories', T('Categories'), '/categories/all');
				
				// If $DisplayCounter is set to TRUE, get Count discussions per Category separately
				$CountDiscussions = 0;
				foreach ($_CategoryData->Result() as $Category) {
					// (will ignore root node)
					if ($Category->Name <> 'Root') $CountDiscussions = $CountDiscussions + $Category->CountDiscussions;
				}
				$MaxDisplayDepth = C('Vanilla.Categories.MaxDisplayDepth') - 1;
				// Fetch every single Category...
				foreach ($_CategoryData->Result() as $Category)
				{
					if ($Category->Name <> 'Root')
					{
						if($Category->Depth <= $MaxDisplayDepth) {

						if ($DisplayCounter == TRUE)
						{
							// Build the Categories-Menu with Discussions-Counter
							$Sender->Menu->AddLink('Categories', $Category->Name.' <span>'.$Category->CountDiscussions.'</span>', '/categories/'.$Category->UrlCode, FALSE);
						} else {
							// Build the Categories-Menu
							$Sender->Menu->AddLink('Categories', $Category->Name, '/categories/'.$Category->UrlCode, FALSE);
						  }
						}
					}
				}
			}
		}
	}
	
	
	
	public function Setup() { }	
		
}

?>