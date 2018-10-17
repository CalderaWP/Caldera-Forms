<?php


namespace calderawp\CalderaContainers\Tests\Mocks;


use calderawp\CalderaContainers\Interfaces\ProvidesService;
use calderawp\CalderaContainers\Interfaces\ServiceContainer;

class Provider implements ProvidesService
{

	/** @inheritdoc */
	public function registerService(ServiceContainer $container)
	{
		$container->bind( $this->getAlias(), function (){
			return (object) [
				'Roy' => 'Sivan',
				'Mike' => 'Corkum'
			];
		} );
	}

	/** @inheritdoc */
	public function getAlias()
	{
		return 'SIVAN';
	}
}