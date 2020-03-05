<?php

namespace Apitizer\JsonApi;

use Illuminate\Contracts\Support\Arrayable;

class ResourceObject implements Arrayable
{

    /** @var array */
    protected $attributes = [];

    /** @var array */
    protected $relationships;

    /** @var string  */
    protected $type;

    /** @var string  */
    protected $id;

    public function __construct(string $type, string $id)
    {
        $this->type = $type;
        $this->id = $id;
    }

    /**
     * @param  array   $attributes
     * @param  string  $type
     * @param  string  $id
     * @return ResourceObject
     */
    public static function factory(array $attributes, string $type, string $id)
    {
        $resourceObject = new self($type, $id);
        $resourceObject->attributes = $attributes;

        return $resourceObject;
    }

    public function toArray(): array
    {
        $array = [];
        $array['type'] = $this->type;
        $array['id'] = $this->id;
        if (! empty($this->attributes)) {
            $array['attributes'] = $this->attributes;
        }

        // if ($this->meta !== null && $this->meta->isEmpty() === false) {
        //     $array['meta'] = $this->meta->toArray();
        // }
        // if ($this->relationships !== null && $this->relationships->isEmpty() === false) {
        // 	$array['relationships'] = $this->relationships->toArray();
        // }
        // if ($this->links !== null && $this->links->isEmpty() === false) {
        // 	$array['links'] = $this->links->toArray();
        // }

        return $array;
    }


    // /**
    //  * @param  object     $attributes
    //  * @param  string     $type       optional
    //  * @param  string|int $id         optional
    //  * @param  array      $options    optional {@see ResourceObject::$defaults}
    //  * @return ResourceObject
    //  */
    // public static function fromObject($attributes, $type=null, $id=null, array $options=[]) {
    // 	$array = Converter::objectToArray($attributes);

    // 	return self::fromArray($array, $type, $id, $options);
    // }

    // /**
    //  * add key-value pairs to attributes
    //  *
    //  * @param string $key
    //  * @param mixed  $value
    //  * @param array  $options optional {@see ResourceObject::$defaults}
    //  */
    // public function add($key, $value, array $options=[]) {
    // 	$options = array_merge(self::$defaults, $options);

    // 	if ($this->attributes === null) {
    // 		$this->attributes = new AttributesObject();
    // 	}

    // 	$this->validator->claimUsedFields([$key], Validator::OBJECT_CONTAINER_ATTRIBUTES, $options);

    // 	$this->attributes->add($key, $value);
    // }

    // /**
    //  * @param  string $key
    //  * @param  mixed  $relation ResourceInterface | ResourceInterface[] | CollectionDocument
    //  * @param  array  $links    optional
    //  * @param  array  $meta     optional
    //  * @param  array  $options  optional {@see ResourceObject::$defaults}
    //  * @return RelationshipObject
    //  */
    // public function addRelationship($key, $relation, array $links=[], array $meta=[], array $options=[]) {
    // 	$relationshipObject = RelationshipObject::fromAnything($relation, $links, $meta);

    // 	$this->addRelationshipObject($key, $relationshipObject, $options);

    // 	return $relationshipObject;
    // }

    // /**
    //  * @param string $href
    //  * @param array  $meta optional, if given a LinkObject is added, otherwise a link string is added
    //  */
    // public function setSelfLink($href, array $meta=[]) {
    // 	$this->addLink('self', $href, $meta);
    // }


    // /**
    //  * @param string             $key
    //  * @param RelationshipObject $relationshipObject
    //  * @param array              $options            optional {@see ResourceObject::$defaults}
    //  *
    //  * @throws DuplicateException if the resource is contained as a resource in the relationship
    //  */
    // public function addRelationshipObject($key, RelationshipObject $relationshipObject, array $options=[]) {
    // 	if ($relationshipObject->hasResource($this)) {
    // 		throw new DuplicateException('can not add relation to self');
    // 	}

    // 	if ($this->relationships === null) {
    // 		$this->setRelationshipsObject(new RelationshipsObject());
    // 	}

    // 	$this->validator->claimUsedFields([$key], Validator::OBJECT_CONTAINER_RELATIONSHIPS, $options);

    // 	$this->relationships->addRelationshipObject($key, $relationshipObject);
    // }

    // /**
    //  * @param RelationshipsObject $relationshipsObject
    //  */
    // public function setRelationshipsObject(RelationshipsObject $relationshipsObject) {
    // 	$newKeys = $relationshipsObject->getKeys();
    // 	$this->validator->clearUsedFields(Validator::OBJECT_CONTAINER_RELATIONSHIPS);
    // 	$this->validator->claimUsedFields($newKeys, Validator::OBJECT_CONTAINER_RELATIONSHIPS);

    // 	$this->relationships = $relationshipsObject;
    // }

    // /**
    //  * internal api
    //  */

    // /**
    //  * whether the ResourceObject is empty except for the ResourceIdentifierObject
    //  *
    //  * this can be used to determine if a Relationship's resource could be added as included resource
    //  *
    //  * @internal
    //  *
    //  * @return boolean
    //  */
    // public function hasIdentifierPropertiesOnly() {
    // 	if ($this->attributes !== null && $this->attributes->isEmpty() === false) {
    // 		return false;
    // 	}
    // 	if ($this->relationships !== null && $this->relationships->isEmpty() === false) {
    // 		return false;
    // 	}
    // 	if ($this->links !== null && $this->links->isEmpty() === false) {
    // 		return false;
    // 	}

    // 	return true;
    // }

    // /**
    //  * ResourceInterface
    //  */

    // /**
    //  * @inheritDoc
    //  */
    // public function getResource($identifierOnly=false) {
    // 	if ($identifierOnly) {
    // 		return ResourceIdentifierObject::fromResourceObject($this);
    // 	}

    // 	return $this;
    // }

    // /**
    //  * ObjectInterface
    //  */

    // /**
    //  * @inheritDoc
    //  */
    // public function isEmpty() {
    // 	if (parent::isEmpty() === false) {
    // 		return false;
    // 	}
    // 	if ($this->attributes !== null && $this->attributes->isEmpty() === false) {
    // 		return false;
    // 	}
    // 	if ($this->relationships !== null && $this->relationships->isEmpty() === false) {
    // 		return false;
    // 	}
    // 	if ($this->links !== null && $this->links->isEmpty() === false) {
    // 		return false;
    // 	}

    // 	return true;
    // }


    // /**
    //  * @inheritDoc
    //  */
    // public function getNestedContainedResourceObjects() {
    // 	if ($this->relationships === null) {
    // 		return [];
    // 	}

    // 	return $this->relationships->getNestedContainedResourceObjects();
    // }
}
