<?php
/**
 *	This file is part of the UnikaCMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Model;

abstract class Eav extends Model
{
	protected $eav_info = null;
	protected $attributes_info = null;

	/**
	 *
	 * get eav info for this model
	 *
	 */
	public function getEavInfo()
	{
		if( null === $this->eav_info )
		{
			$self = $this;
			$this->eav_info = static::$app['cache']->remember('eav_'.$this->getTable(),10,function() use($self){
				$result = $self->getConnection()->select('select * FROM eav_entity_type WHERE table_name = ?',[$self->getTable()]);	
				return $result[0];
			});
		}

		return $this->eav_info;
	}

	/**
	 *
	 * get attributes information of this record
	 */
	public function getAttributesInfo()
	{		
		if( null === $this->attributes_info )
		{
			$self = $this;
			$this->attributes_info = static::$app['cache']->remember('attributes_'.$this->getTable(),10,function()use($self){
				$entity_type_id = $self->getEavInfo()['id'];
				$_sql = 'SELECT * FROM eav_entity_attributes WHERE entity_type_id = '.$entity_type_id;

				return $self->getConnection()->select($_sql);
			});
		}
		return $this->attributes_info;
	}

	protected function internalgetAttributeSet(array $attributes = null)
	{
		if( False === $this->exists )
			throw new \RuntimeException("cannot get EavAttributes on not exists model.");
			
		$entity_type_id = $this->getEavInfo()['id'];

$_sql = <<<NOW
SELECT
eav_entity_attributes.entity_value,
eav_entity_attributes.name,
eav_entity_attributes.label %s

FROM eav_entity_attributes

%s

WHERE
eav_entity_attributes.entity_type_id = ? AND
eav_entity_attributes.entity_id = ?
NOW;

		$attributes_info = $this->getAttributesInfo();
		if( null === $attributes ){
			$attributes = array();
			foreach ($attributes_info as $attribute) 
			{
				$attributes[] = $attribute['name'];
			}
		}

		$attrs = implode("','",$attributes);
		$_sql .= " AND eav_entity_attributes.name IN('".$attrs."')";

		$append_case = ',(CASE WHEN ';
		$append_join = '';

		//cache attribute type so it dont get twice join
		$cached_entity_type = array();

		foreach ($attributes_info as $attribute) 
		{
			if( in_array($attribute['entity_value'], $cached_entity_type) )
				continue;

			if( in_array($attribute['name'], $attributes) )
			{
				switch ($attribute['entity_value']) 
				{
					case 'string':
						$append_case .= '(eav_entity_attributes.entity_value = "string") THEN eav_value_string.value WHEN ';
						$append_join .= 'LEFT JOIN eav_value_string ON (eav_entity_attributes.id = eav_value_string.attribute_id) ';
						$cached_entity_type[] = 'string';
						break;
					case 'integer':
						$append_case .= '(eav_entity_attributes.entity_value = "integer") THEN eav_value_integer.value WHEN ';
						$append_join .= 'LEFT JOIN eav_value_integer ON (eav_entity_attributes.id = eav_value_integer.attribute_id) ';
						$cached_entity_type[] = 'integer';
						break;	
					case 'boolean':
						$append_case .= '(eav_entity_attributes.entity_value = "boolean") THEN eav_value_boolean.value WHEN ';
						$append_join .= 'LEFT JOIN eav_value_boolean ON (eav_entity_attributes.id = eav_value_boolean.attribute_id) ';
						$cached_entity_type[] = 'boolean';
						break;	
					case 'options':
						$append_case .= '(eav_entity_attributes.entity_value = "options") THEN CONCAT(eav_value_options.value,"|",eav_value_options.text) WHEN ';
						$append_join .= 'LEFT JOIN eav_value_options ON (eav_entity_attributes.id = eav_value_options.attribute_id) ';
						$cached_entity_type[] = 'options';
						break;												
					default:
						continue;
						break;
				}					
			}
		}
		
		$append_case = rtrim($append_case,' WHEN ');
		$append_case .= ' END) as value';
		
		$_sql = sprintf($_sql, $append_case,$append_join);
		return $this->getConnection()->select($_sql,[$entity_type_id,$this->id]);	
	}

	public function getAttributeSet(array $attributes = null)
	{
		$_key = 'eav_attributes_'.$this->getTable();
		if( is_array($attributes) )
			$_key .= '_'.implode(',',$attributes);

		$attributeSets = static::$app['cache']->remember($_key,10,function()use($attributes){
			return $this->internalgetAttributeSet($attributes);
		});

		foreach ($attributeSets as $attr) 
		{
			// options is special
			if( 'options' == $attr['entity_value'] ){
				$attribute_options = $this->getAttribute($attr['name'],array());
				$option = explode("|",$attr['value']);
				$attribute_options[$option[0]] = $option[1];
				$this->setAttribute($attr['name'],$attribute_options);
				continue;
			}

			$this->setAttribute($attr['name'],$attr['value']);
		}

		return $attributeSets;
	}

	public function setAttributeSet(array $attributes)
	{

	}
}