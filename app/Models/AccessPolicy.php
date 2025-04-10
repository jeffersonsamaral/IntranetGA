<?php

namespace App\Services;

use App\Models\AccessPolicy;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AccessPolicyService
{
    /**
     * Avalia se um usuário tem permissão para realizar uma ação em um recurso
     * com base nas políticas ABAC
     *
     * @param User $user Usuário a ser avaliado
     * @param string $resource O recurso que está sendo acessado
     * @param string $action A ação que está sendo executada
     * @return bool True se o acesso for permitido
     */
    public function evaluate(User $user, string $resource, string $action): bool
    {
        // Se o usuário tem uma role de administrador, permitir tudo
        if ($user->hasRole('admin')) {
            return true;
        }

        // Buscar políticas relevantes para o recurso e ação
        $policies = AccessPolicy::where('resource', $resource)
            ->where('action', $action)
            ->where('is_active', true)
            ->get();

        // Se não há políticas definidas, negar o acesso
        if ($policies->isEmpty()) {
            Log::warning("Nenhuma política encontrada para recurso: {$resource}, ação: {$action}");
            return false;
        }

        // Coletar todos os atributos do usuário e suas roles
        $userAttributes = $this->collectUserAttributes($user);

        // Avaliar cada política, se qualquer uma permitir, o acesso é concedido
        foreach ($policies as $policy) {
            if ($this->evaluatePolicy($policy, $userAttributes)) {
                return true;
            }
        }

        // Se nenhuma política permitir, negar o acesso
        return false;
    }

    /**
     * Coleta todos os atributos do usuário e suas roles
     */
    private function collectUserAttributes(User $user): array
    {
        $attributes = [];

        // Adicionar atributos do próprio usuário
        $userAttrs = $user->attributes()->get();
        foreach ($userAttrs as $attr) {
            $attributes["user.{$attr->key}"] = $attr->value;
        }

        // Adicionar atributos básicos do usuário
        $attributes['user.id'] = $user->id;
        $attributes['user.username'] = $user->username;
        $attributes['user.email'] = $user->email;

        // Adicionar roles do usuário
        $roles = $user->roles()->get();
        $attributes['user.roles'] = $roles->pluck('slug')->toArray();

        // Adicionar atributos de cada role
        foreach ($roles as $role) {
            $roleAttrs = $role->attributes()->get();
            foreach ($roleAttrs as $attr) {
                $attributes["role.{$role->slug}.{$attr->key}"] = $attr->value;
            }
        }

        return $attributes;
    }

    /**
     * Avalia uma política específica contra os atributos do usuário
     */
    private function evaluatePolicy(AccessPolicy $policy, array $attributes): bool
    {
        $conditions = $policy->conditions;

        // Caso a política não tenha condições, consideramos permitido
        if (empty($conditions)) {
            return true;
        }

        // Se a condição for um array, avaliamos cada uma delas
        if (isset($conditions['operator'])) {
            return $this->evaluateCondition($conditions, $attributes);
        }

        // Caso contrário, todas as condições devem ser satisfeitas (AND implícito)
        foreach ($conditions as $condition) {
            if (!$this->evaluateCondition($condition, $attributes)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Avalia uma condição específica
     */
    private function evaluateCondition(array $condition, array $attributes): bool
    {
        $operator = $condition['operator'] ?? 'eq';
        
        // Operadores lógicos (AND, OR)
        if (in_array($operator, ['and', 'or'])) {
            return $this->evaluateLogicalCondition($condition, $attributes);
        }
        
        // Operadores de comparação
        $attribute = $condition['attribute'] ?? null;
        $value = $condition['value'] ?? null;

        // Se o atributo não existir, a condição falha
        if (!isset($attributes[$attribute])) {
            return false;
        }

        $attributeValue = $attributes[$attribute];

        // Aplicar o operador apropriado
        switch ($operator) {
            case 'eq': // igual
                return $attributeValue == $value;
            case 'neq': // não igual
                return $attributeValue != $value;
            case 'gt': // maior que
                return $attributeValue > $value;
            case 'gte': // maior ou igual
                return $attributeValue >= $value;
            case 'lt': // menor que
                return $attributeValue < $value;
            case 'lte': // menor ou igual
                return $attributeValue <= $value;
            case 'in': // contido em (array)
                return in_array($attributeValue, (array)$value);
            case 'contains': // contém (string ou array)
                if (is_array($attributeValue)) {
                    return in_array($value, $attributeValue);
                }
                return strpos($attributeValue, $value) !== false;
            case 'pattern': // expressão regular
                return preg_match($value, $attributeValue) === 1;
            default:
                return false;
        }
    }

    /**
     * Avalia condições lógicas (AND, OR)
     */
    private function evaluateLogicalCondition(array $condition, array $attributes): bool
    {
        $operator = $condition['operator'];
        $conditions = $condition['conditions'] ?? [];

        if (empty($conditions)) {
            return true;
        }

        // Para operador AND, todas as condições devem ser verdadeiras
        if ($operator === 'and') {
            foreach ($conditions as $subCondition) {
                if (!$this->evaluateCondition($subCondition, $attributes)) {
                    return false;
                }
            }
            return true;
        }

        // Para operador OR, pelo menos uma condição deve ser verdadeira
        if ($operator === 'or') {
            foreach ($conditions as $subCondition) {
                if ($this->evaluateCondition($subCondition, $attributes)) {
                    return true;
                }
            }
            return false;
        }

        return false;
    }
}