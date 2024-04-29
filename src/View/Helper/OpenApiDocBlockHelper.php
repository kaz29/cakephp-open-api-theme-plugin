<?php
declare(strict_types=1);

namespace OpenApiTheme\View\Helper;

use Bake\View\Helper\DocBlockHelper;
use Cake\Collection\Collection;
use Cake\Database\Schema\TableSchema;

class OpenApiDocBlockHelper extends DocBlockHelper
{
    /**
     * Writes the DocBlock header for a class which includes the property and method declarations. Annotations are
     * sorted and grouped by type and value. Groups of annotations are separated by blank lines.
     *
     * @param string $className The class this comment block is for.
     * @param string $classType The type of class (example, Entity)
     * @param array $annotations An array of PHP comment block annotations.
     * @return string The DocBlock for a class header.
     */
    public function openApiClassDescription(
        string $className,
        string $classType,
        array $annotations,
        array $propertySchema
    ): string {
        $lines = [];
        if ($className && $classType) {
            $lines[] = "{$className} {$classType}";
        }

        if ($annotations && $lines) {
            $lines[] = '';
        }

        $previous = false;
        foreach ($annotations as $annotation) {
            if (strlen($annotation) > 1 && $annotation[0] === '@' && strpos($annotation, ' ') > 0) {
                $type = substr($annotation, 0, strpos($annotation, ' '));
                if (
                    $this->_annotationSpacing &&
                    $previous !== false &&
                    $previous !== $type
                ) {
                    $lines[] = '';
                }
                $previous = $type;
            }
            $lines[] = $annotation;
        }

        $lines = array_merge(['/**'], (new Collection($lines))->map(function ($line) {
            return rtrim(" * {$line}");
        })->toArray(), [' */']);

        $required = '';
        foreach ($propertySchema as $name => $values) {
            if ((array_key_exists('null', $values) && $values['null'] === true)) {
                continue;
            }

            $required .= strlen($required) === 0 ? "'{$name}'" : ", '{$name}'";
        }

        $lines[] = '#[OA\Schema(';
        $lines[] = "    schema: '{$className}}',";
        $lines[] = "    title: '{$className}',";
        $lines[] = "    description: '{$className} Entity',";
        $lines[] = "    required: [{$required}],";
        $lines[] = '    properties: [';
        foreach ($propertySchema as $name => $values) {
            $lines = array_merge($lines, $this->makeOpenAPIColumn($name, $values['type'], $values, 8));
        }
        $lines[] = '    ]';
        $lines[] = ')]';

        return implode("\n", $lines);
    }

    public function schemaProperty(array $propertySchema, $name): ?array {
        $format = null;
        $type = $propertySchema[$name]['type'];
        switch ($type) {
            case TableSchema::TYPE_CHAR:
            case TableSchema::TYPE_STRING:
            case TableSchema::TYPE_TEXT:
                $type = 'string';
                break;

            case TableSchema::TYPE_DECIMAL:
                $type = 'string';
                $format = 'decimal';
                break;

            case TableSchema::TYPE_UUID:
                $type = 'string';
                $format = 'uuid';
                break;

            case TableSchema::TYPE_DATE:
                $type = 'string';
                $format = 'date';
                break;

            case TableSchema::TYPE_TIMESTAMP:
            case TableSchema::TYPE_TIMESTAMP_FRACTIONAL:
            case TableSchema::TYPE_TIMESTAMP_TIMEZONE:
            case TableSchema::TYPE_DATETIME:
                $type = 'string';
                $format = 'datetime';
                break;

            case TableSchema::TYPE_TIME:
                $type = 'string';
                $format = '"time';
                break;

            case TableSchema::TYPE_INTEGER:
                $type = 'integer';
                $format = 'int32';
                break;
            case TableSchema::TYPE_BIGINTEGER:
                $type = 'integer';
                $format = 'bigint';
                break;

            case TableSchema::TYPE_TINYINTEGER:
            case TableSchema::TYPE_SMALLINTEGER:
                $type = 'integer';
                $format = 'smallint';
                break;

            case TableSchema::TYPE_FLOAT:
                $type = 'float';
                break;

            case TableSchema::TYPE_JSON:
                $type = 'string';
                $format = 'json';
                break;
            case TableSchema::TYPE_BOOLEAN:
                $type = 'boolean';
                break;

            default:
                if ($type[0] === '\\') {
                    // exclude associated properties.
                    return null;
                } else {
                    $format = $type;
                    $type = 'string';
                }
                break;
        }

        return [
            'type' => $type,
            'format' => $format,
        ];
    }

    protected function makeOpenAPIColumn(string $name, string $type, array $column, int $indentNum = 20): array
    {
        $indentString = str_repeat(' ', $indentNum);
        $result = [];
        $format = null;
        switch ($type) {
            case TableSchema::TYPE_CHAR:
            case TableSchema::TYPE_STRING:
            case TableSchema::TYPE_TEXT:
                $type = 'string';
                break;

            case TableSchema::TYPE_DECIMAL:
                $type = 'string';
                $format = 'decimal';
                break;

            case TableSchema::TYPE_UUID:
                $type = 'string';
                $format = 'uuid';
                break;

            case TableSchema::TYPE_DATE:
                $type = 'string';
                $format = 'date';
                break;

            case TableSchema::TYPE_TIMESTAMP:
            case TableSchema::TYPE_TIMESTAMP_FRACTIONAL:
            case TableSchema::TYPE_TIMESTAMP_TIMEZONE:
            case TableSchema::TYPE_DATETIME:
                $type = 'string';
                $format = 'datetime';
                break;

            case TableSchema::TYPE_TIME:
                $type = 'string';
                $format = '"time';
                break;

            case TableSchema::TYPE_INTEGER:
                $type = 'integer';
                $format = 'int32';
                break;
            case TableSchema::TYPE_BIGINTEGER:
                $type = 'integer';
                $format = 'bigint';
                break;

            case TableSchema::TYPE_TINYINTEGER:
            case TableSchema::TYPE_SMALLINTEGER:
                $type = 'integer';
                $format = 'smallint';
                break;

            case TableSchema::TYPE_FLOAT:
                $type = 'float';
                break;

            case TableSchema::TYPE_JSON:
                $type = 'string';
                $format = 'json';
                break;
            case TableSchema::TYPE_BOOLEAN:
                $type = 'boolean';
                break;

            default:
                if ($type[0] === '\\') {
                    // exclude associated properties.
                    return $result;
                } else {
                    $format = $type;
                    $type = 'string';
                }
                break;
        }

        $comment = !empty($column['comment'])
            ? str_replace("\n", ' ', $column['comment'])
            : '';

        $result[] = "{$indentString}new OA\Property(";
        $result[] = "{$indentString}    property: '{$name}',";
        $result[] = "{$indentString}    type: '{$type}',";
        if (!empty($format)) {
            $result[] = "{$indentString}    format: '{$format}',";
        }
        $result[] = "{$indentString}    description: '{$comment}',";
        $result[] = "{$indentString}),";

        return $result;
    }

    public function openApiActionBody(
        array $propertySchema
    ): string {
        $lines = [];

        $exclude = ['id', 'created', 'modified'];

        $lines[] = '        requestBody: new OA\RequestBody(';
        $lines[] = '            required: true,';
        $lines[] = '            content: new OA\JsonContent(';
        $required = '';
        foreach ($propertySchema as $name => $values) {
            if (in_array($name, $exclude) || (array_key_exists('null', $values) && $values['null'] === true)) {
                continue;
            }

            $required .= strlen($required) === 0 ? "'{$name}'" : ", '{$name}'";
        }
        $lines[] = "                required: [{$required}],";

        $lines[] = '                properties: [';

        foreach ($propertySchema as $name => $values) {
            if (in_array($name, $exclude)) {
                continue;
            }

            $lines = array_merge($lines, (new Collection($this->makeOpenAPIColumn($name, $values['type'], $values)))->map(function ($line) {
                return rtrim($line);
            })->toArray());
        }

        $lines[] = '                ]';
        $lines[] = '            )';
        $lines[] = '        ),';

        return implode("\n", $lines);
    }

    public function openApiActionBodyDocComment(
        array $propertySchema
    ): string {
        $lines = [];

        $lines[] = '     *     @OA\RequestBody(';
        $lines[] = '     *          required=true,';
        $lines[] = '     *          @OA\JsonContent(';

        $exclude = ['id', 'created', 'modified'];
        foreach ($propertySchema as $name => $values) {
            if (in_array($name, $exclude)) {
                continue;
            }

            $lines = array_merge($lines, (new Collection($this->makeOpenAPIColumn($name, $values['type'], $values)))->map(function ($line) {
                return rtrim($line);
            })->toArray());
        }

        $lines[] = '     *          ),';
        $lines[] = '     *     ),';

        return implode("\n", $lines);
    }
}
