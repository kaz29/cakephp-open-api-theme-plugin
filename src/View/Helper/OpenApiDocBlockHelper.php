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

        $lines[] = "@OA\\Schema(\n *      schema=\"{$className}\",\n *      title=\"\",";
        $lines[] = "     description=\"{$className} entity\",";
        foreach ($propertySchema as $name => $values) {
            $lines = array_merge($lines, $this->makeOpenAPIColumn($name, $values['type'], $values));
        }
        $lines[] = ')';

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

        return implode("\n", $lines);
    }

    protected function makeOpenAPIColumn(string $name, string $type, array $column): array
    {
        $result = [];
        $format = null;
        $comment = null;
        switch ($type) {
            case TableSchema::TYPE_CHAR:
            case TableSchema::TYPE_STRING:
            case TableSchema::TYPE_TEXT:
                $type = 'string';
                break;

            case TableSchema::TYPE_DECIMAL:
                $type = 'string';
                $format = '          format="decimal",';
                break;

            case TableSchema::TYPE_UUID:
                $type = 'string';
                $format = '          format="uuid",';
                break;

            case TableSchema::TYPE_DATE:
                $type = 'string';
                $format = '          format="date",';
                break;

            case TableSchema::TYPE_TIMESTAMP:
            case TableSchema::TYPE_TIMESTAMP_FRACTIONAL:
            case TableSchema::TYPE_TIMESTAMP_TIMEZONE:
            case TableSchema::TYPE_DATETIME:
                $type = 'string';
                $format = '          format="datetime",';
                break;

            case TableSchema::TYPE_TIME:
                $type = 'string';
                $format = '          format="time",';
                break;

            case TableSchema::TYPE_INTEGER:
                $type = 'integer';
                $format = '          format="int32",';
                break;
            case TableSchema::TYPE_BIGINTEGER:
                $type = 'integer';
                $format = '          format="bigint",';
                break;

            case TableSchema::TYPE_TINYINTEGER:
            case TableSchema::TYPE_SMALLINTEGER:
                $type = 'integer';
                $format = '          format="smallint",';
                break;

            case TableSchema::TYPE_FLOAT:
                $type = 'float';
                break;

            case TableSchema::TYPE_JSON:
                $type = 'string';
                $format = '          format="json",';
                break;
            case TableSchema::TYPE_BOOLEAN:
                $type = 'boolean';
                break;

            default:
                if ($type[0] === '\\') {
                    // exclude associated properties.
                    return $result;
                } else {
                    $format = "          format=\"{$type}\",";
                    $type = 'string';
                }
                break;
        }

        if (is_null($comment) && !empty($column['comment'])) {
            $comment = str_replace("\n", ' ', $column['comment']);
        }

        $result[] = '      @OA\Property(';
        $result[] = "          property=\"{$name}\",";
        $result[] = "          type=\"{$type}\",";
        if (!empty($format)) {
            $result[] = $format;
        }
        $result[] = "          description=\"{$comment}\",";
        $result[] = '      ),';

        return $result;
    }

    public function openApiActionBody(
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
                return rtrim("     *        {$line}");
            })->toArray());
        }

        $lines[] = '     *          ),';
        $lines[] = '     *     ),';

        return implode("\n", $lines);
    }
}
