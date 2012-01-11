<?php

/**
 * undocumented class
 *
 * @package default
 * @author 
 **/
interface Installable {
		/**
	 * called when installing a model.
	 * Nothing happens in the base class but override it in the sub-class, for creating database table etc.
	 *
	 * @return void
	 * @author 
	 **/
	public function installModel();

	/**
	 * called when removing a model
	 * Nothing happens in the base class but override it in the sub-class, for like maybe deleteing a database table.
	 *
	 * @return void
	 * @author 
	 **/
	public function removeModel();

	/**
	 * called when updating a model
	 * Nothing happens in the base class but override it in the sub-class.
	 *
	 * @return void
	 * @author 
	 **/
	public function updateModel();
	
} // END interface installable