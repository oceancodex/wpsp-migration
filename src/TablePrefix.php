<?php
namespace WPSPCORE\Migration;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;

class TablePrefix {

	protected $prefix = '';

	public function __construct($prefix) {
		$this->prefix = (string)$prefix;
	}

	/**
	 * @param LoadClassMetadataEventArgs $eventArgs
	 *
	 */
	public function loadClassMetadata($eventArgs) {
		$classMetadata = $eventArgs->getClassMetadata();

		if (!$classMetadata->isInheritanceTypeSingleTable() || $classMetadata->getName() === $classMetadata->rootEntityName) {
			$classMetadata->setPrimaryTable([
				'name' => $this->prefix . $classMetadata->getTableName()
			]);
		}

		foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping) {
			if ($mapping['type'] == ClassMetadata::MANY_TO_MANY && $mapping['isOwningSide']) {
				$mappedTableName                                                     = $mapping['joinTable']['name'];
				$classMetadata->associationMappings[$fieldName]['joinTable']['name'] = $this->prefix . $mappedTableName;
			}
		}
	}

}