<?php
/*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class NoteToSelf extends Module
{
	public function __construct()
	{
		$this->name = 'notetoself';
		$this->tab = 'payments_gateways';
		$this->version = '0.0.1';
		$this->author = 'PrestaShop';
		$this->bootstrap = true;
		$this->displayName = $this->l('Note To Self');
		$this->description = $this->l('Let customers leave notes for themselves on your product sheets.');
		$this->controllers = array('update');
		parent::__construct();
	}

	private function setPrefix($sql) {
		return str_replace('PREFIX_', _DB_PREFIX_, $sql);
	}

	public function createTable()
	{
		$sql = 'CREATE TABLE PREFIX_notetoself_notes (
			id_guest 	int(10) unsigned NOT NULL default 0		,
			id_product 	int(10) unsigned NOT NULL default 0		,
			notes 		TEXT 			 NOT NULL 				,
			UNIQUE INDEX (id_guest, id_product)
		);';
	
		$sql = $this->setPrefix($sql);

		return Db::getInstance()->execute($sql);
	}

	public function dropTable()
	{
		$sql = 'DROP TABLE PREFIX_notetoself_notes';
	
		$sql = $this->setPrefix($sql);

		return Db::getInstance()->execute($sql);
	}

	public function install()
	{
		return parent::install() && $this->registerHook('productFooter') && $this->createTable();
	}

	public function unInstall()
	{
		return parent::unInstall() && $this->dropTable();
	}

	public function getControllerLink($params = array())
	{
		return $this->context->link->getModuleLink($this->name, 'update', $params);
	}

	public function updateNotes($id_product, $notes)
	{
		$sql = 'REPLACE INTO PREFIX_notetoself_notes (id_guest, id_product, notes)
				VALUES (:id_guest, :id_product, \':notes\')
		';

		$sql = str_replace(
			array(':id_guest', ':id_product', ':notes'),
			array((int)$this->context->customer->id_guest, (int)$id_product, pSQL($notes)),
			$sql
		);

		$sql = $this->setPrefix($sql);

		if (Db::getInstance()->execute($sql)) {
			return array('success' => true);
		} else {
			return array('success' => false);
		}
	}

	public function getNotes($id_product)
	{
		$sql = 'SELECT notes FROM PREFIX_notetoself_notes WHERE id_guest = :id_guest AND id_product = :id_product';

		$sql = str_replace(
			array(':id_guest', ':id_product'),
			array((int)$this->context->customer->id_guest, (int)$id_product),
			$sql
		);

		$sql = $this->setPrefix($sql);
		$result = Db::getInstance()->ExecuteS($sql);
		if (!empty($result)) {
			return $result[0]['notes'];
		} else {
			return "";
		}
	}

	public function hookProductFooter($args)
	{
		$id_product = $args['product']->id;

		$messages = array(
			'saved' 	=> $this->l('Notes saved!'),
			'oops'		=> $this->l('Oops, something went wrong, sorry!'),
			'saving'	=> $this->l('Saving your changes...')
		);

		$this->context->smarty->assign(array(
			'notetoself_id_product' 			=> $id_product,
			'notetoself_update_controller_url' 	=> $this->getControllerLink(),
			'notetoself_notes'					=> $this->getNotes($id_product),
			'notetoself_messages'				=> Tools::jsonEncode($messages),
		));

		$this->context->controller->addCSS($this->_path.'/css/notetoself.css', 'all');
		$this->context->controller->addJS($this->_path.'/js/notetoself.js', 'all');
		
		return $this->display(__FILE__, 'views/templates/hook/productFooter.tpl');
	}
}