<?php

namespace App\Traits;

use App\Models\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasAttributes
{
    /**
     * Relacionamento com os atributos da entidade
     */
    public function attributes(): MorphMany
    {
        return $this->morphMany(Attribute::class, 'entity');
    }
    
    /**
     * Obter o valor de um atributo específico
     *
     * @param string $key Chave do atributo
     * @param mixed $default Valor padrão caso o atributo não exista
     * @return mixed Valor do atributo ou valor padrão
     */
    public function getCustomAttributeValue(string $key, $default = null)
    {
        $attribute = $this->attributes()->where('key', $key)->first();
        return $attribute ? $attribute->value : $default;
    }
    
    /**
     * Definir um valor para um atributo
     *
     * @param string $key Chave do atributo
     * @param mixed $value Valor do atributo
     * @return $this
     */
    public function setCustomAttributeValue(string $key, $value)
    {
        $this->attributes()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
        
        return $this;
    }
    
    /**
     * Remover um atributo
     *
     * @param string $key Chave do atributo
     * @return $this
     */
    public function removeCustomAttribute(string $key)
    {
        $this->attributes()->where('key', $key)->delete();
        
        return $this;
    }
    
    /**
     * Verificar se um atributo existe
     *
     * @param string $key Chave do atributo
     * @return bool
     */
    public function hasCustomAttribute(string $key): bool
    {
        return $this->attributes()->where('key', $key)->exists();
    }
    
    /**
     * Obter todos os atributos como um array associativo
     *
     * @return array
     */
    public function getAllCustomAttributes(): array
    {
        $attributes = $this->attributes()->get();
        $result = [];
        
        foreach ($attributes as $attribute) {
            $result[$attribute->key] = $attribute->value;
        }
        
        return $result;
    }
}