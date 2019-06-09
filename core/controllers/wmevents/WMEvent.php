<?php

/**
 *
 * @author Diego Lopez Rivera (forgin50@gmail.com)
 *
 */
namespace core\controllers\wmevents;


class WMEvent implements EventInterface {

	private $name;
	private $target;
	private $params;
	private $isStopped;
	/**
	 * {@inheritDoc}
	 * @see \Core\Controllers\GFEvents\EventInterface::getName()
	 */
	public function getName() {
		return $this->name;

	}

	/**
	 * {@inheritDoc}
	 * @see \Core\Controllers\GFEvents\EventInterface::getTarget()
	 */
	public function getTarget() {
		return $this->target;

	}

	/**
	 * {@inheritDoc}
	 * @see \Core\Controllers\GFEvents\EventInterface::getParams()
	 */
	public function getParams() {
		return $this->params;

	}

	/**
	 * {@inheritDoc}
	 * @see \Core\Controllers\GFEvents\EventInterface::getParam()
	 */
	public function getParam($name) {
		return $this->name[$name];

	}

	/**
	 * {@inheritDoc}
	 * @see \Core\Controllers\GFEvents\EventInterface::setName()
	 */
	public function setName($name) {
		$this->name = $name;
		return $this;

	}

	/**
	 * {@inheritDoc}
	 * @see \Core\Controllers\GFEvents\EventInterface::setTarget()
	 */
	public function setTarget($target) {
		$this->target = $target;
		return $this;

	}

	/**
	 * {@inheritDoc}
	 * @see \Core\Controllers\GFEvents\EventInterface::setParams()
	 */
	public function setParams(array $params) {
		$this->params = $params;
		return $this;

	}

	/**
	 * {@inheritDoc}
	 * @see \Core\Controllers\GFEvents\EventInterface::stopPropagation()
	 */
	public function stopPropagation($flag) {
		$this->isStopped = $flag;

	}

	/**
	 * {@inheritDoc}
	 * @see \Core\Controllers\GFEvents\EventInterface::isPropagationStopped()
	 */
	public function isPropagationStopped() {
		return $this->isStopped;

	}

}